<template v-if="hasPermission('view_booking')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <h4 class="pb-2">Đơn hiện thời</h4>
          <router-link to="/admin/order-create">
            <button class="btn add-order-fixed-btn me-3">+ Thêm đơn hàng</button>
          </router-link>
          <button class="btn add-order-fixed-btn" @click="openQrScanner" v-if="hasPermission('edit_booking')">
            + Quét mã
          </button>

          <div v-if="showQrScanner" class="qr-scanner-modal-overlay">
            <div class="qr-scanner-modal">
              <div class="modal-header">
                <h5 class="modal-title">Quét mã đặt bàn</h5>
                <button type="button" class="btn-close" @click="closeQrScanner">x</button>
              </div>
              <div class="modal-body">
                <div id="qr-reader" style="width: 100%"></div>
                <!-- <div v-if="qrScanError" class="alert alert-danger mt-3">Lỗi quét mã: {{ qrScanError }}</div> -->
              </div>
            </div>
          </div>


          <!-- Modal hiển thị đơn sau khi quét QR -->
          <div v-if="showScannedOrderPopup" class="qr-scanner-modal-overlay">
            <div class="qr-scanner-modal">
              <div class="modal-header">
                <h5 class="modal-title">Thông tin đơn đặt bàn</h5>
                <button type="button" class="btn-close" @click="showScannedOrderPopup = false"></button>
              </div>
              <div class="modal-body">
                <template v-if="scannedOrder">
                  <h6><span class="fw-bold">Mã đặt bàn:</span> {{ scannedOrder.id }}</h6>
                  <div class="text-muted">{{ scannedOrder.guest_name }} - {{ scannedOrder.guest_phone }}</div>
                  <div class="text-muted">
                    <span class="label fw-bold">Thời gian:</span> {{ formatTime(scannedOrder.reserved_from) }}
                  </div>
                  <div class="text-muted">
                    <span class="label fw-bold">Bàn số:</span>
                    <span v-for="(t, index) in scannedOrder.tables" :key="t.id">
                      {{ t.table_number }}<span v-if="index < scannedOrder.tables.length - 1">, </span>
                    </span>
                  </div>
                  <div class="text-muted">
                    <span class="label fw-bold">Số khách:</span> {{ scannedOrder.guest_count }}
                  </div>
                  <div class="text-muted mb-2">
                    <span class="label fw-bold">Trạng thái:</span> {{ scannedOrder.order_status }}
                  </div>

                  <div v-if="hasPermission('edit_booking') &&
                    scannedOrder.order_status !== 'Đã hủy' &&
                    scannedOrder.order_status !== 'Hoàn thành' &&
                    scannedOrder.order_status !== 'Khách đã đến'">
                    <button class="add-order-fixed-btn" @click="updateStatus(scannedOrder.id, 'Khách đã đến')">Khách đã
                      đến</button>

                  </div>
                </template>
                <template v-else>
                  <p class="text-danger">Không tìm thấy dữ liệu đơn hàng.</p>
                </template>
              </div>
            </div>
          </div>



          <div class="tab-content">
            <div class="tab-pane active" id="all-content" role="tabpanel" aria-labelledby="tab-all">
              <div class="order-cards-grid">
                <article class="order-card-container" v-for="(order, index) in orderOfTable" :key="order.id"
                  v-show="order.order_status == 'Khách đã đến'">
                  <header class="order-header">
                    <div class="user-info">
                      <div class="avatar-placeholder">{{ ++index }}</div>
                      <div class="name-order">
                        <h3 class="user-name">{{ order.guest_name }}</h3>
                        <p class="order-details-line">
                          #{{ order.id }}
                          <span v-if="order.tables">/ Bàn: {{order.tables?.map((t) => `${t.table_number}`).join(', ')}}</span>
                        </p>
                      </div>
                    </div>
                  </header>



                  <div class="date-time-info">
                    <div>
                      Thời gian tiếp nhận:
                      <strong>{{
                        formatTime(order.check_in_time ? order.check_in_time : order.order_time)
                      }}h</strong>
                    </div>
                  </div>

                  <div class="food-list-scroll">
                    <div v-for="food in order.details" :key="food.food_id" class="food-item">
                      <div class="flex-grow-1">
                        <div class="fw-semibold">{{ food.food_name }}</div>

                        <div class="text-muted small" v-if="food.toppings && food.toppings.length">
                          <div v-for="(topping, i) in food.toppings" :key="i">
                            + {{ topping.topping_name || 'Tên topping không có' }} ({{
                              formatNumber(topping.price)
                            }}
                            VNĐ)
                          </div>
                        </div>
                        <div v-else class="text-muted small">Không có topping</div>
                      </div>
                    </div>
                  </div>

                  <div class="status-update-section pt-1">
                    <select v-model="order.order_status" class="status-dropdown"
                      @change="updateStatus(order.id, order.order_status)">
                      <option value="Chờ xác nhận" :disabled="!canSelectStatus(order.order_status, 'Chờ xác nhận')">
                        Chờ xác nhận
                      </option>
                      <option value="Đã xác nhận" :disabled="!canSelectStatus(order.order_status, 'Đã xác nhận')">
                        Đã xác nhận
                      </option>
                      <option value="Đang xử lý" :disabled="!canSelectStatus(order.order_status, 'Đang xử lý')">
                        Đang xử lý
                      </option>
                      <option value="Khách đã đến" :disabled="!canSelectStatus(order.order_status, 'Khách đã đến')">
                        Khách đã đến
                      </option>
                      <option value="Hoàn thành" :disabled="!canSelectStatus(order.order_status, 'Hoàn thành')">
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

                  <hr />

                  <div class="buttons-section">
                    <router-link :to="`/admin/choose-list-food/${order.id}`" class="btn btn-details">Thêm
                      món</router-link>
                    <button class="btn btn-pay">Thanh toán</button>
                  </div>
                </article>

                <article class="order-card-container" v-for="(order, index) in orderTakeAway" :key="order.id">
                  <header class="order-header">
                    <div class="user-info">
                      <div class="avatar-placeholder">{{ ++index }}</div>
                      <div class="name-order">
                        <h3 class="user-name">{{ order.guest_name }}</h3>
                        <p class="order-details-line">Mã đơn #{{ order.id }}</p>
                      </div>
                    </div>
                  </header>

                  <div class="date-time-info">
                    <div>
                      Thời gian tiếp nhận:
                      <strong>{{
                        formatTime(order.order_time ? order.order_time : order.check_in_time)
                      }}h</strong>
                    </div>
                  </div>

                  <div class="food-list-scroll">
                    <div v-for="food in order.details" :key="food.food_id" class="food-item">
                      <div class="flex-grow-1">
                        <div class="fw-semibold">{{ food.food_name }}</div>

                        <div class="text-muted small" v-if="food.toppings && food.toppings.length">
                          <div v-for="(topping, i) in food.toppings" :key="i">
                            + {{ topping.topping_name || 'Tên topping không có' }} ({{
                              formatNumber(topping.price)
                            }}
                            VNĐ)
                          </div>
                        </div>
                        <div v-else class="text-muted small">Không có topping</div>
                      </div>
                    </div>
                  </div>

                  <div class="status-update-section pt-1">
                    <select v-model="order.order_status" class="status-dropdown"
                      @change="updateStatusOrder(order.id, order.order_status)">
                      <option value="Đã xác nhận" :disabled="!canSelectStatusOrder(order.order_status, 'Đã xác nhận')">
                        Đã xác nhận
                      </option>
                      <option value="Đang xử lý" :disabled="!canSelectStatusOrder(order.order_status, 'Đang xử lý')">
                        Đang xử lý
                      </option>
                      <option value="Hoàn thành" :disabled="!canSelectStatusOrder(order.order_status, 'Hoàn thành')">
                        Hoàn thành
                      </option>
                      <option value="Đã hủy" :disabled="!canSelectStatusOrder(order.order_status, 'Đã hủy')">
                        Đã hủy
                      </option>
                    </select>
                  </div>

                  <div class="total-section">
                    <div class="total-label">Tổng tiền</div>
                    <div class="total-amount">{{ formatNumber(order.total_price) }}VNĐ</div>
                  </div>
                </article>
              </div>
            </div>

            <div class="tab-pane" id="pending-content" role="tabpanel" aria-labelledby="tab-pending"></div>
            <div class="tab-pane" id="confirmed-content" role="tabpanel" aria-labelledby="tab-confirmed"></div>
            <div class="tab-pane" id="in-progress-content" role="tabpanel" aria-labelledby="tab-in-progress"></div>
            <div class="tab-pane" id="delivering-content" role="tabpanel" aria-labelledby="tab-delivering"></div>
            <div class="tab-pane" id="completed-content" role="tabpanel" aria-labelledby="tab-completed"></div>
            <div class="tab-pane" id="failed-content" role="tabpanel" aria-labelledby="tab-failed"></div>
            <div class="tab-pane" id="canceled-content" role="tabpanel" aria-labelledby="tab-canceled"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'
import { Info } from '@/stores/info-order-reservation'
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'
import { Permission } from '@/stores/permission'
import { Html5QrcodeScanner } from 'html5-qrcode'
import { nextTick } from 'vue'
export default {
  setup() {
    const { formatNumber, formatTime } = Info.setup()
    const userId = ref(null)
    const userString = localStorage.getItem('user')
    if (userString) {
      const user = JSON.parse(userString)
      if (user && user.id !== undefined) {
        userId.value = user.id
      }
    }
    const { hasPermission, permissions, isLoadingPermissions } = Permission(userId)

    const router = useRouter()

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

    const orderTakeAway = ref([])
    const getOrderTakeAway = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/get_all_orders')
        orderTakeAway.value = res.data.orders.filter((order) => order.type_order === 'takeaway')
      } catch (error) {
        console.log(error)
      }
    }
    const canSelectStatusOrder = (currentStatus, optionStatus) => {
      const statusOrder = ['Đã xác nhận', 'Đang xử lý', 'Hoàn thành', 'Đã hủy']

      const currentIndex = statusOrder.indexOf(currentStatus)
      const optionIndex = statusOrder.indexOf(optionStatus)

      if (currentIndex === -1 || optionIndex === -1) return false

      if (optionStatus === currentStatus) return true

      if (currentStatus === 'Hoàn thành' || currentStatus === 'Đã hủy') return false

      if (optionIndex === currentIndex + 1) return true

      if (optionStatus === 'Đã hủy' && currentStatus !== 'Đã hủy') return true

      return false
    }

    const updateStatusOrder = async (id, status) => {
      try {
        if (confirm(`Bạn có chắc chắn muốn cập nhật sang trạng thái ${status}`)) {
          await axios.put(`http://127.0.0.1:8000/api/update/${id}/status`, {
            order_status: status,
          })
          toast.success('Cập nhật thành công')
          await getOrderTakeAway()
        }
      } catch (error) {
        toast.error('Có lỗi xảy ra')
        console.log(error)
      }
    }

    const showQrScanner = ref(false)
    const qrScanner = ref(null)
    const qrScanResult = ref(null)
    const qrScanError = ref(null)

    const openQrScanner = async () => {
      showQrScanner.value = true
      qrScanResult.value = null
      qrScanError.value = null

      await nextTick() // Đảm bảo DOM đã được render
      qrScanner.value = new Html5QrcodeScanner(
        'qr-reader',
        { fps: 10, qrbox: { width: 250, height: 250 } }, // 10 khung hình/giây, hộp quét 250x250px
        /* verbose= */ false, // Không hiển thị log chi tiết của thư viện
      )
      qrScanner.value.render(onScanSuccess, onScanError)
    }

    const closeQrScanner = () => {
      showQrScanner.value = false
      qrScanner.value = null
    }
    const scannedOrder = ref(null);
    const showScannedOrderPopup = ref(false);

    const onScanSuccess = (decodedText) => {
      closeQrScanner()
      toast.success(`Quét thành công`);

      const match = decodedText.match(/\/(\d+)$/); // Lấy ID từ URL
      if (!match) {
        toast.error("QR không hợp lệ. Không tìm thấy ID đơn hàng.");
        return;
      }
      const scannedId = parseInt(match[1]);
      const foundOrder = orderOfTable.value.find(order => order.id === scannedId);
      if (foundOrder) {
        scannedOrder.value = foundOrder;
        showScannedOrderPopup.value = true;
      } else {
        toast.error("Không tìm thấy đơn hàng này trong danh sách.");
      }
    };


    const onScanError = (errorMessage) => {
      console.warn(`Lỗi khi quét QR: ${errorMessage}`)
      qrScanError.value = errorMessage
    }

    const initialTablesLoaded = ref(false)
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

    watch(
      [permissions, isLoadingPermissions],
      async ([newPermissions, newIsLoadingPermissions]) => {
        if (newPermissions.length > 0 && !newIsLoadingPermissions && !initialTablesLoaded.value) {
          await getOrderOfTable()
          initialTablesLoaded.value = true
        }
      },
      { immediate: true },
    )


    const updateStatus = async (id, status) => {
      try {
        if (confirm(`Bạn có chắc chắn muốn cập nhật sang trạng thái ${status}`)) {
          await axios.post('http://127.0.0.1:8000/api/reservation-update-status', {
            id: id,
            order_status: status,
          })
          toast.success('Cập nhật thành công')
          showScannedOrderPopup.value = false;
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
      const params = new URLSearchParams(window.location.search)
      const hasVnpParams = params.has('vnp_TransactionStatus') || params.has('vnp_TxnRef')

      axios
        .get('http://127.0.0.1:8000/api/payments/vnpay-return', { params })
        .then((res) => {
          if (hasVnpParams) {
            if (res.data.success) {
              toast.success('Thanh toán thành công!')
            } else {
              toast.error('Thanh toán thất bại hoặc có lỗi!')
            }
          }
        })
        .catch(() => {
          if (hasVnpParams) {
            toast.error('Lỗi xác minh thanh toán!')
          }
        })
        .finally(() => {
          getOrderOfTable()
          getOrderTakeAway()
          setInterval(updateTimers, 1000)
        })
    })

    return {
      orderOfTable,
      formatNumber,
      timers,
      formatTime,
      canSelectStatus,
      canSelectStatusOrder,
      updateStatus,
      updateStatusOrder,
      orderTakeAway,
      userId,
      router,
      hasPermission,
      openQrScanner,
      qrScanError,
      qrScanResult,
      qrScanner,
      showQrScanner,
      closeQrScanner,
      showScannedOrderPopup,
      scannedOrder
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

.qr-scanner-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  /* Đảm bảo modal hiện trên các phần tử khác */
}

.qr-scanner-modal {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 500px;
  /* Giới hạn chiều rộng của modal */
  max-height: 90%;
  /* Giới hạn chiều cao */
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.qr-scanner-modal .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
  background-color: #f8f9fa;
}

.qr-scanner-modal .modal-title {
  font-size: 1.2em;
  font-weight: 600;
  margin: 0;
}

.qr-scanner-modal .btn-close {
  background: none;
  border: none;
  font-size: 1.5em;
  cursor: pointer;
  padding: 0;
}

.qr-scanner-modal .modal-body {
  padding: 20px;
  flex-grow: 1;
  overflow-y: auto;
  /* Cho phép cuộn nếu nội dung dài */
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Html5-qrcode specific styles (adjust if needed) */
#qr-reader video {
  width: 100% !important;
  height: auto !important;
  border-radius: 8px;
}

#qr-reader__dashboard_section_csr,
#qr-reader__dashboard_section_start {
  display: none !important;
  /* Ẩn các nút điều khiển mặc định của thư viện */
}

#qr-reader__dashboard_section {
  padding: 0 !important;
  /* Loại bỏ padding thừa */
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

.food-list-scroll {
  max-height: 90px;
  min-height: 90px;
  /* Có thể chỉnh: 150px, 250px, hoặc 30vh tuỳ ý */
  overflow-y: auto;
  margin-top: 0.5rem;
  padding-right: 0.5rem;
}

/* Tuỳ chọn: Tùy chỉnh thanh cuộn trên Chrome/Edge */
.food-list-scroll::-webkit-scrollbar {
  width: 6px;
}

.food-list-scroll::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 3px;
}

.add-order-fixed-btn {
  padding: 10px 16px;
  border-radius: 5px;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  margin-bottom: 10px;
}
</style>
