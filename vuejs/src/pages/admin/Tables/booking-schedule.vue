<template>
  <div class="d-flex justify-content-between mb-3">
    <h2>Lịch đặt bàn</h2>
       <router-link :to="{ name: 'insert-reservation' }" class="btn btn-danger1">
      + Thêm đơn đặt bàn
    </router-link>
  </div>
  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-sm-6 col-md-4" v-for="order in orderOfTable" :key="order.order_id">
        <div class="card custom-card shadow-sm">
          <div class="card-body p-3">
            <h6 class="card-title mb-2 text-truncate">
              {{ order.guest_name }} - {{ order.guest_phone }}
            </h6>
            <p class="card-text small text-muted mb-1">
              <i class="fa fa-calendar"></i>
              {{ formatDate(order.reserved_from) }} |
              <i class="bi bi-clock"></i
              >{{ formatTime(order.reserved_from) }} |
              <i class="bi bi-people"></i> {{ order.guest_count }}
            </p>
            <p class="card-text small text-muted mb-1">
              Bàn số: {{ order.tables?.map((t) => `${t.table_number}`).join(', ') }}
            </p>
            <p class="fw-bold text-danger mb-2">{{ formatNumber(order.total_price) }}VND</p>
            <div class="d-flex justify-content-between align-items-center rounded-0">
              <div class="form-group rounded-0">
                <select
                  v-model="order.order_status"
                  class="form-control rounded-0"
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
                  <option
                    value="Đã hủy"
                    :disabled="!canSelectStatus(order.order_status, 'Đã hủy')"
                  >
                    Đã hủy
                  </option>
                </select>
              </div>
              <div class="dropdown">
                <button
                  class="btn btn-light btn-sm border dropdown-toggle"
                  type="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  @click="toggleDropdown(order.id)"
                >
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul
                  class="dropdown-menu dropdown-menu-end"
                  :class="{ 'd-block': activeDropdownId === order.id }"
                >
                  <li>
                    <router-link
                      :to="{name: 'admin-tables-list', params: { orderId: order.id }}"
                      class="dropdown-item1"
                      >Chuyển bàn</router-link
                    >
                  </li>
                  <li>
                    <router-link
                      :to="{ name: 'list-food', params: { id: order.id } }"
                      class="dropdown-item1"
                      v-if="
                        order.reservation_status !== 'Hoàn thành' &&
                        order.reservation_status !== 'Đã hủy'
                      "
                      >Chọn món</router-link
                    >
                  </li>
                  <li>
                    <router-link
                      :to="{ name: 'admin-orders-detail', params: { id: order.id } }"
                      class="dropdown-item1"
                      >Chi tiết</router-link
                    >
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal xếp bàn
    <div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title me-5" id="tableModalLabel">Sơ Đồ Bàn</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row row-cols-2 row-cols-md-4 g-4">
              <div class="col" v-for="table in availableTables" :key="table.id">
                <div class="table-card p-3 text-center" :id="table.id"
                  :class="{ selected: selectedTableIds.includes(table.id) }" @click="toggleTable(table.id)">
                  <div class="icon-wrap mb-2">
                    <i class="bi bi-person-fill"></i>
                  </div>
                  <h5 class="table-number">Bàn {{ table.table_number }}</h5>
                  <p class="status-text">{{ table.status }} - {{ table.capacity }} người</p>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary" @click="saveTableAssignment">Lưu</button>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { Info } from '@/stores/info-order-reservation'
import { toast } from 'vue3-toastify'
import { useRoute } from 'vue-router'

export default {
  setup() {
    const { formatDate, formatTime, formatNumber } = Info.setup()

    const orderOfTable = ref([])
    const selectedOrderId = ref(null)
    const selectedTableIds = ref([])
    const route = useRoute()
    const orderId = route.params.orderId



    const getOrderOfTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/order-tables')
        orderOfTable.value = res.data.orders
      } catch (error) {
        console.log(error)
      }
    }

    // const availableTables = ref([])

    const selectOrder = (id) => {
      selectedOrderId.value = id
      selectedTableIds.value = []
    }

    // const fetchAvailableTables = async () => {
    //   try {
    //     await getInfo('order', selectedOrderId.value)

    //     const reservedTo = new Date(info.value.reservations_time.replace(' ', 'T'))
    //     reservedTo.setHours(reservedTo.getHours() + 2)
    //     const reserved_to = formatDateTime(reservedTo)

    //     const res = await axios.post('http://127.0.0.1:8000/api/available-tables', {
    //       order_id: selectedOrderId.value,
    //       reserved_from: info.value.reservations_time,
    //       reserved_to: reserved_to,
    //     })

    //     availableTables.value = res.data.tables;
    //     console.log(reserved_to);

    //   } catch (error) {
    //     alert('Lỗi khi lấy danh sách bàn có thể đặt')
    //     console.error('Lỗi:', error)
    //   }
    // }

    // const toggleTable = (id) => {
    //   if (selectedTableIds.value.includes(id)) {
    //     selectedTableIds.value = selectedTableIds.value.filter((tid) => tid !== id)
    //   } else {
    //     selectedTableIds.value.push(id)
    //   }
    // }
    // const formatDateTime = (date) => {
    //   const pad = (n) => n.toString().padStart(2, '0')
    //   return (
    //     `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ` +
    //     `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
    //   )
    // }

    // const areTablesConsecutive = () => {
    //   const numbers = availableTables.value
    //     .filter(table => selectedTableIds.value.includes(table.id))
    //     .map(table => table.table_number)
    //     .sort((a, b) => a - b)

    //   if (numbers.length <= 1) return true

    //   for (let i = 1; i < numbers.length; i++) {
    //     if (numbers[i] !== numbers[i - 1] + 1) {
    //       return false
    //     }
    //   }
    //   return true
    // }

    // const saveTableAssignment = async () => {
    //   if (!areTablesConsecutive()) {
    //     alert('Vui lòng chọn các bàn có số liền kề nhau!')
    //     return
    //   }

    //   await getInfo('order', selectedOrderId.value)

    //   const reservedTo = new Date(info.value.reservations_time.replace(' ', 'T'))
    //   reservedTo.setHours(reservedTo.getHours() + 2)
    //   const reserved_to = formatDateTime(reservedTo)

    //   try {
    //     await axios.post('http://127.0.0.1:8000/api/set-up/order-tables', {
    //       order_id: selectedOrderId.value,
    //       table_ids: selectedTableIds.value,
    //       reserved_from: info.value.reservations_time,
    //       reserved_to,
    //     })
    //     alert('Bàn đã được xếp thành công')
    //   } catch (error) {
    //     alert('Lỗi khi xếp bàn: ' + error.response.data.message)
    //   }
    // }

    const activeDropdownId = ref(null)

    const toggleDropdown = (id) => {
      activeDropdownId.value = activeDropdownId.value === id ? null : id
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
        'Đã hủy'
      ];

      const currentIndex = statusOrder.indexOf(currentStatus);
      const optionIndex = statusOrder.indexOf(optionStatus);

      if (currentIndex === -1 || optionIndex === -1) return false;

      if (optionStatus === currentStatus) return true;

      if (currentStatus === 'Hoàn thành' || currentStatus === 'Đã hủy') return false;

      if (optionIndex === currentIndex + 1) return true;

      if (optionStatus === 'Đã hủy' && currentStatus !== 'Đã hủy') return true;

      return false;
    }


    onMounted(() => {
      getOrderOfTable()
      // setInterval(() => {
      //   axios.get('http://127.0.0.1:8000/api/auto-cancel-orders')
      // }, 6000)
    })

    return {
      formatDate,
      formatTime,
      formatNumber,
      orderOfTable,
      selectedOrderId,
      selectedTableIds,
      selectOrder,
      // toggleTable,
      // saveTableAssignment,
      // availableTables,
      toggleDropdown,
      activeDropdownId,
      updateStatus,
      canSelectStatus,
      orderId
    }
  },
}
</script>
<style scoped>
.left-side {
  width: 50%;
  float: left;
}

.right-side {
  width: 45%;
  float: right;
}

table {
  width: 100%;
}

table th,
table td {
  text-align: left;
  padding: 8px;
}

table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.table-card.selected {
  border: 2px solid #24aeb1;
  background-color: #e8f9f8;
}

.custom-card {
  overflow: visible !important;
}

.card-title {
  font-weight: bold;
}

.bi-person-fill {
  color: #c62c37;
  font-size: 30px;
}

.table-card {
  position: relative;
  background: #f8f9fa;
  border-radius: 10px;
  border: 1px solid #dee2e6;
  padding: 20px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.table-number {
  color: black;
  font-weight: bold;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.status-text {
  margin-top: 10px;
}

.area {
  color: #6c757d;
}

.nav-tabs .nav-link {
  cursor: pointer;
}

.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.table td,
.table th {
  vertical-align: middle;
}

.btn-sm {
  padding: 4px 8px;
  font-size: 14px;
}

.btn-danger {
  padding: 4px 8px;
}

.d-flex .w-50 {
  flex: 1;
}

.dropdown-item1 {
  text-align: left;
  color: #333;
  padding: 6px 10px;
  font-weight: normal;
  text-decoration: none;
  border: none;
  font-size: 16px;
  display: block;
  background-color: transparent;
  transition:
    background-color 0.3s,
    color 0.3s;
  cursor: pointer;
}

.dropdown-item1:hover {
  background-color: #f0f0f0;
  color: #800020;
}
</style>
