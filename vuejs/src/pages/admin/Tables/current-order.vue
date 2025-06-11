<template>
  <h4 class="pb-2">Đơn hiện thời</h4>
  <div class="tab-content">
    <div class="tab-pane active" id="all-content" role="tabpanel" aria-labelledby="tab-all">
      <div class="order-cards-grid">
        <article
          class="order-card-container"
          v-for="(order, index) in orderOfTable"
          :key="order.id"
          v-show="order.order_status == 'Khách đã đến'"
        >
          <header class="order-header">
            <div class="user-info">
              <div class="avatar-placeholder">{{ ++index }}</div>
              <div class="name-order">
                <h3 class="user-name">{{ order.guest_name }}</h3>
                <p class="order-details-line">
                  #{{ order.id }}
                  <span v-if="order.tables"
                    >/ Bàn: {{ order.tables?.map((t) => `${t.table_number}`).join(', ') }}</span
                  >
                </p>
              </div>
            </div>
          </header>
          <div class="date-time-info">
            <div>
              Thời gian tiếp nhận:
              <strong
                >{{ formatTime(order.check_in_time ? order.check_in_time : order.order_time) }}h
              </strong>
            </div>
          </div>
          <div v-for="food in order.details" :key="food.food_id">
            <div class="flex-grow-1" >
              <div class="fw-semibold">{{ food.food_name }}</div>

              <div class="text-muted small" v-if="food.toppings && food.toppings.length">
                <div v-for="(topping, i) in food.toppings" :key="i">
                  + {{ topping.topping_name || 'Tên topping không có' }} ({{ formatNumber(topping.price) }} VNĐ)
                </div>
              </div>
              <div v-else class="text-muted small">Không có topping</div>
            </div>
          </div>

          <div class="status-update-section pt-1">
            <select
              v-model="order.order_status"
              class="status-dropdown"
              @change="updateStatus(order.id, order.order_status)"
            >
              <option
                value="Chờ xác nhận"
                :disabled="!canSelectStatus(order.order_status, 'Chờ xác nhận')"
              >
                Chờ xác nhận
              </option>
              <option
                value="Đã xác nhận"
                :disabled="!canSelectStatus(order.order_status, 'Đã xác nhận')"
              >
                Đã xác nhận
              </option>
              <option
                value="Đang xử lý"
                :disabled="!canSelectStatus(order.order_status, 'Đang xử lý')"
              >
                Đang xử lý
              </option>
              <option
                value="Khách đã đến"
                :disabled="!canSelectStatus(order.order_status, 'Khách đã đến')"
              >
                Khách đã đến
              </option>
              <option
                value="Hoàn thành"
                :disabled="!canSelectStatus(order.order_status, 'Hoàn thành')"
              >
                Hoàn thành
              </option>
              <option value="Đã hủy" :disabled="!canSelectStatus(order.order_status, 'Đã hủy')">
                Đã hủy
              </option>
            </select>
          </div>

          <div class="total-section">
            <div class="total-label">Tổng tiền</div>
            <div class="total-amount">{{ formatNumber(order.total_price) }}VNĐ</div>
          </div>
          <hr>
          <div class="buttons-section">
            <button class="btn btn-details">Chi tiết</button>
            <button class="btn btn-pay">Thanh toán</button>
          </div>
        </article>
      </div>
    </div>

    <div class="tab-pane" id="pending-content" role="tabpanel" aria-labelledby="tab-pending"></div>
    <div
      class="tab-pane"
      id="confirmed-content"
      role="tabpanel"
      aria-labelledby="tab-confirmed"
    ></div>
    <div
      class="tab-pane"
      id="in-progress-content"
      role="tabpanel"
      aria-labelledby="tab-in-progress"
    ></div>
    <div
      class="tab-pane"
      id="delivering-content"
      role="tabpanel"
      aria-labelledby="tab-delivering"
    ></div>
    <div
      class="tab-pane"
      id="completed-content"
      role="tabpanel"
      aria-labelledby="tab-completed"
    ></div>
    <div class="tab-pane" id="failed-content" role="tabpanel" aria-labelledby="tab-failed"></div>
    <div
      class="tab-pane"
      id="canceled-content"
      role="tabpanel"
      aria-labelledby="tab-canceled"
    ></div>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { Info } from '@/stores/info-order-reservation'
import { toast } from 'vue3-toastify'

export default {
  setup() {
    const { formatNumber, formatTime } = Info.setup()

    const timers = ref({})
    const startTimes = ref({})
    const updateTimers = () => {
      const now = new Date()
      for (const id in startTimes.value) {
        const start = startTimes.value[id]
        const end = new Date(start.getTime() + 2 * 60 * 60 * 1000) // cộng 2 tiếng

        const diff = Math.floor((end - now) / 1000)

        if (diff <= 0) {
          timers.value[id] = '00:00:00'
        } else {
          const hours = String(Math.floor(diff / 3600)).padStart(2, '0')
          const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0')
          const seconds = String(diff % 60).padStart(2, '0')
          timers.value[id] = `${hours}:${minutes}:${seconds}`
        }
      }
    }

    const orderOfTable = ref([])
    const getOrderOfTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/order-current-tables')
        orderOfTable.value = res.data.orders
        // console.log(orderOfTable.value);

        orderOfTable.value.forEach((order) => {
          if (order.reservation_status === 'Khách Đã Đến') {
            startTimes.value[order.id] = new Date(order.check_in_time)
          }
        })
        updateTimers()
      } catch (error) {
        console.log(error)
      }
    }

    const updateStatus = async (id, status) => {
      try {
        if (confirm(`Bạn có chắc chắn muốn cập nhật sang trạng thái ${status}`)) {
          await axios.post('http://127.0.0.1:8000/api/reservation-update-status', {
            id: id,
            order_status: status,
          })
          toast.success('Cập nhật thành công')
          await getOrderOfTable()
        }
      } catch (error) {
        toast.error('Có lỗi xảy ra')
        console.log(error)
      }
    }

    const canSelectStatus = (currentStatus, optionStatus) => {
      const statusOrder = [
        'Chờ xác nhận',
        'Đã xác nhận',
        'Đang xử lý',
        'Khách đã đến',
        'Hoàn thành',
        'Đã hủy',
      ]

      const currentIndex = statusOrder.indexOf(currentStatus)
      const optionIndex = statusOrder.indexOf(optionStatus)

      if (currentIndex === -1 || optionIndex === -1) return false

      if (optionStatus === currentStatus) return true

      if (currentStatus === 'Hoàn thành' || currentStatus === 'Đã hủy') return false

      if (optionIndex === currentIndex + 1) return true

      if (optionStatus === 'Đã hủy' && currentStatus !== 'Đã hủy') return true

      return false
    }

    onMounted(() => {
      getOrderOfTable()
      setInterval(updateTimers, 1000)
    })

    return {
      orderOfTable,
      formatNumber,
      timers,
      formatTime,
      canSelectStatus,
      updateStatus,
    }
  },
}
</script>

<style scoped>
.order-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  width: 100%;
}

@media (max-width: 1366px) {
  .order-cards-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 1024px) {
  .order-cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  body {
    padding: 15px;
  }

  .order-cards-grid {
    grid-template-columns: 1fr;
  }

  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .status-update-section {
    flex-direction: column;
    gap: 10px;
  }

  .buttons-section {
    flex-direction: column;
    gap: 10px;
  }
}

.order-card-container {
  background-color: #fff;
  border-radius: 5px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
  min-height: 380px;
  border: 1px solid #e2e0e0;
  /* Ensures consistent card height */
}

.order-card-container:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

/* Header Section */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.user-info {
  display: flex;
  gap: 12px;
  flex-grow: 1;
  min-width: 0;
  /* Allows content to shrink */
}

.avatar-placeholder {
  width: 50px;
  height: 50px;
  background-color: #c92c3c;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: 600;
  font-size: 20px;
  color: #ffff;
  flex-shrink: 0;
}

.name-order {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-width: 0;
  /* Allows text overflow ellipsis to work */
}

.user-name {
  font-weight: 600;
  font-size: 18px;
  color: #333;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.order-details-line {
  font-size: 14px;
  color: #777;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.date-time-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
  margin-bottom: 5px;
  font-size: 14px;
  color: #555;
  font-weight: 500;
}

.date-time-info strong {
  font-weight: 600;
  color: #333;
  font-size: 15px;
}

/* Items Section */
.items-section {
  flex-grow: 1;
  padding-right: 5px;
  /* For scrollbar */
  max-height: 60px;
  /* Fixed height for 2 items initially */
  overflow-y: hidden;
  position: relative;
}

.items-section.expanded {
  max-height: unset;
  /* Allow full height when expanded */
  overflow-y: auto;
  /* Show scrollbar if needed */
}

.item-entry {
  margin-bottom: 8px;
  font-size: 12px;
  color: #333;
  line-height: 1.4;
  display: flex;
  flex-direction: column;
  border-bottom: 1px solid #f3f1f1;
  padding-bottom: 5px;
}

.item-entry:last-of-type {
  border-bottom: none;
  padding-bottom: 0;
}

.item-main-line {
  display: flex;
  justify-content: space-between;
  font-weight: 600;
  margin-bottom: 2px;
}

.item-name {
  flex-grow: 1;
  white-space: nowrap;
  overflow: hidden;
  font-weight: bold;
}
.item-list {
  border-bottom: #666;
}
.item-qty {
  flex-shrink: 0;
  margin-left: 10px;
}

.item-topping {
  font-size: 10px;
  color: #666;
  padding-left: 10px;
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.more-items {
  text-align: center;
  font-size: 14px;
  color: #888;
  margin-top: 5px;
  cursor: pointer;
  font-weight: 500;
  text-decoration: underline;
}

/* Total Section */
.total-section {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding-top: 15px;
  border-top: 1px solid #eee;
  margin-top: auto;
}

.total-label {
  font-weight: 600;
  font-size: 18px;
  color: #333;
}

.total-amount {
  font-weight: 700;
  font-size: 16px;
  color: #c92c3c;
}

/* Status Update Section */
.status-update-section {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-top: 10px;
}

.status-dropdown {
  flex-grow: 1;
  padding: 10px 15px;
  border: 1px solid #d9d9d9;
  border-radius: 8px;
  font-size: 15px;
  color: #333;
  background-color: #f8f8f8;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13.2-5.4H18.6a17.6%2017.6%200%200%200-13.2%205.4A17.6%2017.6%200%200%200%200%2082.5c0%205.8%202.2%2011.3%206.4%2015.5l128%20127.9c3.2%203.2%207%204.9%2011.3%204.9s8.1-1.6%2011.3-4.9l128-127.9c4.2-4.2%206.4-9.7%206.4-15.5a17.6%2017.6%200%200%200-5.2-12.9z%22%2F%3E%3C%2Fsvg%3E');
  background-repeat: no-repeat;
  background-position: right 10px top 50%;
  background-size: 12px auto;
  cursor: pointer;
}

.status-dropdown:focus {
  outline: none;
  border-color: #c92c3c;
}

/* Action Buttons */
.buttons-section {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  flex-wrap: wrap;
}

.btn {
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition:
    background-color 0.3s ease,
    transform 0.1s ease;
  flex-grow: 1;
  min-width: 120px;
}

.btn:active {
  transform: translateY(1px);
}

.btn-details {
  background-color: #f0f4f7;
  color: #4a6787;
}

.btn-details:hover {
  background-color: #e2eaf0;
}

.btn-pay {
  background-color: #c92c3c;
  color: #fff;
}

.btn-pay:hover {
  background-color: #f03c4e;
}
</style>
