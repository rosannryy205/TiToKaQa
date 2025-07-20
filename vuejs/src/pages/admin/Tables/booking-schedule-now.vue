<template>
  <h3 class="p-4 ps-0 pb-0">Danh sách đặt bàn ngày {{ formatDate(selectedDateInput) }}</h3>
  <button class="btn btn-add me-3" @click="openQrScanner" v-if="hasPermission('edit_booking')">
    + Quét mã
  </button>

  <div v-if="showQrScanner" class="qr-scanner-modal-overlay">
    <div class="qr-scanner-modal">
      <div class="modal-header">
        <h5 class="modal-title">Quét mã đặt bàn</h5>
        <button type="button" class="btn-close" @click="closeQrScanner"></button>
      </div>
      <div class="modal-body">
        <div id="qr-reader" style="width: 100%"></div>
        <div v-if="qrScanError" class="alert alert-danger mt-3">Lỗi quét mã: {{ qrScanError }}</div>
      </div>
    </div>
  </div>
  <div v-if="hasPermission('view_booking')" class="table-responsive mt-3 d-none d-lg-block">
    <table class="table table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Mã đặt bàn</th>
          <th>Khách hàng</th>
          <th>Thời gian</th>
          <th>Bàn số</th>
          <th>Số khách</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>

      <tbody>
        <template v-if="orderOfTable && orderOfTable.length > 0">
          <tr v-for="order in orderOfTable" :key="order.id">
            <td>{{ order.reservation_code }}</td>
            <td>
              {{ order.guest_name }}
              <br />
              <span class="text-muted">{{ order.guest_phone }}</span>
            </td>

            <td>{{ formatTime(order.reserved_from) }}</td>
            <td>
              <span v-for="(t, index) in order.tables" :key="t.id">
                {{ t.table_number }}<span v-if="index < order.tables.length - 1">, </span>
              </span>
            </td>
            <td>{{ order.guest_count }}</td>
            <td>
              <select v-model="order.order_status" class="form-select rounded-0"
                @change="updateStatus(order.id, order.order_status)" :disabled="!hasPermission('edit_booking')">
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
            </td>
            <td>
              <div v-if="
                hasPermission('edit_booking') &&
                order.order_status !== 'Đã hủy' &&
                order.order_status !== 'Hoàn thành'
              ">
                <router-link :to="`/admin/choose-list-food/${info.id}`" class="btn edit-button me-2">Chọn
                  món</router-link>
                <router-link :to="`/admin/tables/${info.id}`" class="btn edit-button me-2">Chuyển bàn</router-link>
                <router-link :to="`/admin/tables-setup/${info.id}`" class="btn edit-button me-2">Xếp bàn</router-link>
              </div>
            </td>
          </tr>
        </template>
        <tr v-else-if="isLoading">
          <td colspan="9" class="text-center">
            <div class="loader-wrapper">
              <div class="loader"></div>
            </div>
          </td>
        </tr>
        <tr v-else>
          <td colspan="9" class="text-center">Không có đơn đặt bàn nào cho ngày này.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div v-if="hasPermission('view_booking')" class="d-block d-sm-none mt-3">
    <template v-if="orderOfTable && orderOfTable.length > 0">
      <div class="card mb-3" v-for="order in orderOfTable" :key="order.id">
        <div class="row g-0 align-items-center">
          <div class="col-12">
            <div class="card-body p-2">
              <h6 class="card-title"><span class="fw-bold">Mã đặt bàn:</span> {{ order.id }}</h6>
              <div class="text-muted">{{ order.guest_name }} - {{ order.guest_phone }}</div>
              <div class="text-muted">
                <span class="label fw-bold">Thời gian:</span> {{ formatTime(order.reserved_from) }}
              </div>
              <div class="text-muted">
                <span class="label fw-bold">Bàn số:</span>
                <span v-for="(t, index) in order.tables" :key="t.id">
                  {{ t.table_number }}<span v-if="index < order.tables.length - 1">, </span>
                </span>
              </div>
              <div class="text-muted">
                <span class="label fw-bold">Số khách:</span> {{ order.guest_count }}
              </div>
              <div class="text-muted">
                <span class="label fw-bold">Trạng thái:</span><br />
                <select v-model="order.order_status"
                  class="form-select rounded-0 form-select-sm d-inline-block w-auto mt-1"
                  @change="updateStatus(order.id, order.order.order_status)" :disabled="!hasPermission('edit_booking')">
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
              <div v-if="
                hasPermission('edit_booking') &&
                order.order_status !== 'Đã hủy' &&
                order.order_status !== 'Hoàn thành'
              " class="mt-1">
                <router-link :to="`/admin/choose-list-food/${info.id}`" class="btn edit-button me-1">Chọn
                  món</router-link>
                <router-link :to="`/admin/tables/${info.id}`" class="btn edit-button me-1">Chuyển bàn</router-link>
                <router-link :to="`/admin/tables-setup/${info.id}`" class="btn edit-button">Xếp bàn</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div v-else-if="isLoading" class="text-center p-5">
      <div class="loader-wrapper">
        <div class="loader"></div>
      </div>
    </div>
    <div v-else class="text-center p-3">Không có đơn đặt bàn nào cho ngày này.</div>
  </div>
  <div v-if="isLoading && (!orderOfTable || orderOfTable.length === 0)" class="text-center p-5">
    <div class="loader-wrapper">
      <div class="loader"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { onMounted } from 'vue'
import axios from 'axios'
import { Info } from '@/stores/info-order-reservation'
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'
import { Permission } from '@/stores/permission'
import { Html5QrcodeScanner } from 'html5-qrcode'
import { nextTick } from 'vue'

const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions, isLoadingPermissions } = Permission(userId)

const { formatDate, formatTime, formatNumber, info, getInfo } = Info.setup()
const router = useRouter()
const isLoading = ref(false)

const showQrScanner = ref(false)
const qrScanner = ref(null)
const qrScanResult = ref(null) // Vẫn giữ biến này để lưu kết quả, nhưng chưa xử lý
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

const onScanSuccess = async (decodedText, decodedResult) => {
  console.log(`Đã quét thành công: ${decodedText}`)
  qrScanResult.value = decodedText
  closeQrScanner()

  const match = decodedText.match(/\/history-order-detail\/(\d+)/)
  if (match && match[1]) {
    const orderId = match[1]

    try {
      await axios.post('http://127.0.0.1:8000/api/reservation-update-status', {
        id: orderId,
        order_status: 'Khách đã đến',
      })

      toast.success(`Xác nhận khách đã đến cho đơn #${orderId}`)
      await getOrderOfTable()
    } catch (err) {
      toast.error('Có lỗi khi cập nhật trạng thái đơn hàng')
      console.error(err)
    }
  } else {
    toast.error('QR code không hợp lệ')
  }
}

const onScanError = (errorMessage) => {
  console.warn(`Lỗi khi quét QR: ${errorMessage}`)
  qrScanError.value = errorMessage
}

const orderOfTable = ref([])
const getOrderOfTable = async (selectedDate) => {
  isLoading.value = true
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/order-tables')

    const today = new Date()
    const year = today.getFullYear()
    const month = String(today.getMonth() + 1).padStart(2, '0')
    const day = String(today.getDate()).padStart(2, '0')
    const todayString = `${year}-${month}-${day}`

    orderOfTable.value = response.data.orders.filter((order) => {
      const orderDate = order.reserved_from.split(' ')[0]
      return orderDate === (selectedDate || todayString)
    })
    console.log('Đơn hàng đã lọc cho ngày hôm nay:', orderOfTable.value)

    orderOfTable.value.sort((a, b) => {
      const timeA = new Date(a.reserved_from.replace(' ', 'T')).getTime()
      const timeB = new Date(b.reserved_from.replace(' ', 'T')).getTime()

      if (timeA === timeB) {
        const tableNumA = a.tables.length > 0 ? a.tables[0].table_number : 9999
        const tableNumB = b.tables.length > 0 ? b.tables[0].table_number : 9999
        return tableNumA - tableNumB
      }
      return timeA - timeB
    })
  } catch (error) {
    console.error('Lỗi khi lấy dữ liệu đơn đặt bàn:', error)
    toast.error('Không thể tải dữ liệu đơn đặt bàn.')
  } finally {
    isLoading.value = false
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

  if (optionStatus === 'Khách đã đến' && currentIndex < statusOrder.indexOf('Khách đã đến')) {
    return true
  }

  if (optionIndex === currentIndex + 1) return true

  if (optionStatus === 'Đã hủy') return true

  return false
}

const selectedDateInput = ref('')

onMounted(async () => {
  selectedDateInput.value = new Date().toISOString().split('T')[0]
})
const initialTablesLoaded = ref(false)

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
</script>

<style scoped>
.card-custom {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  padding: 0 20px;
  margin-top: 5px;
}

.item-name {
  font-weight: bold;
  font-size: 14px;
}

.total-price {
  font-size: 14px;
  text-align: right;
  color: #c92c3c;
}

.item-details {
  font-size: 11px;
  color: #6c757d;
}

.ban {
  background-color: #ffffff;
  font-weight: bold;
}

.title {
  font-size: 12px;
  text-align: left;
  margin-bottom: 2px;
}

.time-badge {
  color: #8d8a8a;
  font-size: small;
}

.event-detail-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  background-color: rgba(0, 0, 0, 0.5);
  /* Thêm lớp mờ cho overlay */
}

/* Styles for QR Scanner Modal */
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

.event-detail-popup {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 600px;
  overflow: hidden;
  font-family: Arial, sans-serif;
  color: #333;
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  /* Tăng padding để đẹp hơn */
  background-color: #c92c3c;
  border-bottom: 1px solid #eee;
}

.popup-title {
  font-size: 1.1em;
  /* Tăng kích thước chữ */
  font-weight: 600;
  margin: 0;
  color: #ffffff;
}

.event-detail-popup.show {
  opacity: 1;
  transform: translateY(0);
}

a {
  text-decoration: none;
}

.btn-add {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.8em;
  cursor: pointer;
  color: #ffffff;
  padding: 0 5px;
}

.popup-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.info-item {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  font-size: 0.95em;
  color: #1d1d1d;
}

@media (max-width: 767.98px) {
  .edit-button {
    font-size: 14px;
  }
}

.info-block {
  display: flex;
  /* Sử dụng flexbox cho info-block */
  align-items: center;
  gap: 8px;
}

.info-item i {
  margin-right: 12px;
  font-size: 1.1em;
}

.info-item span {
  flex-grow: 1;
}

.popup-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
  border-top: 1px solid #eee;
  background-color: #f5f5f5;
}

.popup-actions button {
  padding: 8px 18px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
  transition:
    background-color 0.2s,
    color 0.2s;
}

.edit-button {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}

.edit-button:hover {
  background-color: #c92c3c;
  color: white;
}

.delete-button {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}

.delete-button:hover {
  background-color: #c92c3c;
  color: white;
}

.fc-theme-standard .fc-scrollgrid {
  border: none;
}

.loader-wrapper {
  height: 50vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* loader */
.loader {
  width: 50px;
  --b: 8px;
  aspect-ratio: 1;
  border-radius: 50%;
  padding: 1px;
  background: conic-gradient(#0000 10%, #f03355) content-box;
  -webkit-mask:
    repeating-conic-gradient(#0000 0deg, #000 1deg 20deg, #0000 21deg 36deg),
    radial-gradient(farthest-side, #0000 calc(100% - var(--b) - 1px), #000 calc(100% - var(--b)));
  -webkit-mask-composite: destination-in;
  mask-composite: intersect;
  animation: l4 1s infinite steps(10);
}

@keyframes l4 {
  to {
    transform: rotate(1turn);
  }
}

td,
.fc-timegrid-slot,
.fc-scrollgrid-sync-table td {
  background-color: transparent !important;
}

.popup-fade-enter-active,
.popup-fade-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
}

.popup-fade-enter-from,
.popup-fade-leave-to {
  transform: translateY(-20px);
}

.popup-fade-enter-to,
.popup-fade-leave-from {
  transform: translateY(0px);
}

/* Responsive styles for smaller screens (mobile cards) */
.card-body .label {
  min-width: 80px;
  /* Đảm bảo các label align đều */
  display: inline-block;
}

.card-body .form-select {
  max-width: fit-content;
  /* Giới hạn chiều rộng của select */
}
</style>
