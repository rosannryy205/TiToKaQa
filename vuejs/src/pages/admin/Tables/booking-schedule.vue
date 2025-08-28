<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div v-if="isLoading" class="loader-wrapper1">
            <div class="loader"></div>
          </div>
          <div class="d-flex mb-2">
            <div>
              <h4>Lịch đặt bàn</h4>
              <div class="d-flex">
                <router-link :to="{ name: 'insert-reservation-admin' }" class="btn btn-outline-danger me-2"
                  v-if="hasPermission('create_booking')">
                  + Thêm đơn đặt bàn
                </router-link>
                <input type="date" id="datePicker" v-model="selectedDateInput" @change="handleDateInputChange"
                  class="form-control rounded" style="width: 280px;">
              </div>
            </div>
          </div>

          <FullCalendar :options="calendarOptions" v-if="hasPermission('view_booking')" ref="fullCalendarRef" />
          <transition name="popup-fade">
            <div v-show="showDetailPopup" class="event-detail-popup-overlay" @click="closeDetailPopup">
              <div class="event-detail-popup" @click.stop>
                <!-- Header -->
                <div class="popup-header">
                  <h3 class="popup-title">Chi tiết đơn hàng #{{ info.id }}</h3>
                  <button class="close-button" @click="closeDetailPopup">&times;</button>
                </div>
                <!-- Body -->
                <div class="popup-body row">
                  <!-- Cột trái: Thông tin + Món ăn -->
                  <div class="col-md-7 popup-content" v-if="info">
                    <!-- Thông tin chung -->
                    <div class="info-section">
                      <div class="info-item">
                        <div class="info-block">
                          <i class="bi bi-table"></i>Ngày đặt:
                          <span v-for="(t, index) in info.tables" :key="index">
                            {{ formatDate(t.reserved_from)
                            }}<span v-if="index < info.tables.length - 1">, </span>
                          </span>
                        </div>
                        <div class="info-block">
                          <i class="bi bi-clock"></i>Giờ đặt:
                          <span v-for="(t, index) in info.tables" :key="index">
                            {{ formatTime(t.reserved_from) }} - {{ formatTime(t.reserved_to)
                            }}<span v-if="index < info.tables.length - 1">, </span>
                          </span>
                        </div>
                      </div>
                      <div class="info-item">
                        <div class="info-block">
                          <i class="fa-solid fa-calendar"></i>
                          <span>Bàn số:
                            <span v-for="(t, index) in info.tables" :key="index">
                              {{ t.table_number }}<span v-if="index < info.tables.length - 1">, </span>
                            </span>
                          </span>
                        </div>
                        <div class="info-block">
                          <i class="bi bi-people"></i>
                          <span>Lượng khách:
                            {{ info.guest_count }}
                          </span>
                        </div>
                      </div>
                      <div class="info-item">
                        <div class="info-block">
                          <i class="fa-solid fa-user"></i>
                          <span>Khách hàng: {{ info.guest_name }}</span>
                        </div>
                        <div class="info-block">
                          <i class="bi bi-telephone"></i>
                          <span>SĐT: {{ info.guest_phone }}</span>
                        </div>
                      </div>
                      <div class="info-item">
                        <div class="info-block">
                          <i class="bi bi-envelope"></i>
                          <span>Email: {{ info.guest_email }}</span>
                        </div>
                        <div class="info-block">
                          <i class="bi bi-card-heading"></i>
                          <span>Ghi chú: {{ info.note || 'Không có' }}</span>
                        </div>
                      </div>
                      <div class="info-item">
                        <div class="info-block">
                          <i class="bi bi-cash-coin"></i>
                          <span>Phí giữ bàn: {{ formatNumber(info.table_fee) }} VNĐ</span>
                        </div>
                        <div class="info-block">
                          <i class="bi bi-card-list"></i>
                          <span>Trạng thái đơn:
                            <select v-model="tempStatus" class="p-1 border rounded-0"
                              @change="updateStatus(info.id, tempStatus)" :disabled="!hasPermission('edit_booking')">
                              <option value="Chờ xác nhận"
                                :disabled="!canSelectStatus(info.order_status, 'Chờ xác nhận')">
                                Chờ xác nhận
                              </option>
                              <option value="Đã xác nhận"
                                :disabled="!canSelectStatus(info.order_status, 'Đã xác nhận')">
                                Đã xác nhận
                              </option>
                              <option value="Đang xử lý" :disabled="!canSelectStatus(info.order_status, 'Đang xử lý')">
                                Đang xử lý
                              </option>
                              <option value="Khách đã đến"
                                :disabled="!canSelectStatus(info.order_status, 'Khách đã đến')">
                                Khách đã đến
                              </option>
                              <option value="Hoàn thành" :disabled="canSelectStatus(info.order_status, 'Hoàn thành')">
                                Hoàn thành
                              </option>
                              <option value="Đã hủy" :disabled="!canSelectStatus(info.order_status, 'Đã hủy')">
                                Đã hủy
                              </option>
                            </select>
                          </span>
                        </div>
                      </div>
                      <div class="info-item">
                        <div class="info-block">
                          <span>
                            <i class="bi bi-cash-stack"></i>Tổng tiền món: {{ formatNumber(info.total_price) }} VNĐ
                          </span>
                        </div>
                        <div class="info-block">
                          <span>
                            <i class="bi bi-cash-stack"></i>Đã thanh toán: {{ formatNumber(info.total_paid) }} VNĐ
                          </span>
                        </div>
                      </div>
                      <div class="info-item">
                         <div class="info-block fw-bold text-danger">
                          <span>
                            <i class="bi bi-cash-stack"></i>Tổng tiền đơn:
                            {{ formatNumber(Number(info.total_price) + Number(info.table_fee)) }} VNĐ
                          </span>
                        </div>
                      </div>
                    </div>
                    <!-- Món đã đặt -->
                    <div class="order-items mt-2">
                      <h6><i class="bi bi-journals me-2"></i>Các món đã đặt</h6>
                      <div class="card-custom p-2" style="max-height: 220px; overflow-y: auto; overflow-x: hidden;">
                        <div v-for="item in info.details" :key="item.id"
                          class="row align-items-center py-2 border-bottom">
                          <div class="col-6 text-truncate pe-2">
                            <div class="fw-bold text-truncate" :title="item.food_name">{{ item.food_name }}</div>
                            <ul v-if="item.toppings?.length" class="small text-muted ps-3 mb-0">
                              <li v-for="topping in item.toppings" :key="topping.food_toppings_id">
                                + {{ topping.topping_name }} ({{ Number(topping.price).toLocaleString() }} đ)
                              </li>
                            </ul>
                          </div>
                          <div class="col-2 text-center">{{ item.quantity }}</div>
                          <div class="col-4 text-end fw-bold">
                            {{ formatNumber(calculateTotalItemPrice(item)) }} VNĐ
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Cột phải: Thanh toán -->
                  <div class="col-md-5 p-3 border-start">
                    <!-- Timeline -->
                    <div v-if="info.payment_info?.length">
                      <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Lịch sử thanh toán</h6>
                      <div class="timeline">
                        <div v-for="p in info.payment_info" :key="p.payment_id" class="timeline-item">
                          <div class="timeline-dot" :class="{ 'bg-success': p.payment_status === 'Đã thanh toán' }">
                          </div>
                          <div class="timeline-content">
                            <div class="fw-bold">Phương thức thanh toán: {{ p.payment_method }}</div>
                            <div>Giá tiền: {{ formatNumber(p.amount_paid) }} VNĐ</div>
                            <div>Thời gian: <small class="text-muted">{{ p.payment_time }}</small></div>
                            <div>Trạng thái:
                              <span :class="{ 'text-success': p.payment_status === 'Đã thanh toán' }">
                                {{ p.payment_status }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Chọn thanh toán -->
                    <div class="payment-section mt-4" v-if="info.order_status !== 'Hoàn thành'">
                      <h6 class="mb-3">Phương thức thanh toán</h6>
                      <div class="d-flex justify-content-around mb-3 flex-wrap gap-2">
                        <button class="btn btn-payment" :class="{ active: paymentMethod === 'COD' }"
                          @click="paymentMethod = 'COD'" :disabled="info.order_status !== 'Khách đã đến'">
                          <img src="/img/cod.png" class="payment-icon mb-1" /> <br />Tiền mặt
                        </button>
                        <button class="btn btn-payment" :class="{ active: paymentMethod === 'VNPAY' }"
                          @click="paymentMethod = 'VNPAY'" :disabled="info.order_status !== 'Khách đã đến'">
                          <img src="/img/Logo-VNPAY-QR-1 (1).png" class="payment-icon mb-1" /> <br />QR Code
                        </button>
                      </div>
                      <button class="btn btn-danger w-100 p-2" @click="payMoney"
                        :disabled="info.order_status !== 'Khách đã đến'">Thanh toán</button>
                    </div>
                  </div>
                </div>

                <!-- Footer Actions -->
                <div class="popup-actions" v-if="hasPermission('edit_booking') && info.order_status !== 'Hoàn thành'">
                  <router-link :to="`/admin/choose-list-food/${info.id}`" class="btn edit-button">Chọn món</router-link>
                  <router-link :to="`/admin/tables-change/${info.id}`" class="btn edit-button">Chuyển bàn</router-link>
                  <router-link :to="`/admin/tables-setup/${info.id}`" class="btn edit-button">Xếp bàn</router-link>
                </div>
              </div>
            </div>
          </transition>

        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, watch } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import viLocale from '@fullcalendar/core/locales/vi'
import { onMounted } from 'vue'
import axios from 'axios'
import { Info } from '@/stores/info-order-reservation'
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router'
import { Permission } from '@/stores/permission'
import { API_URL, STORAGE_URL } from '@/config'

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

const tables = ref([])
const getTable = async () => {
  if (!isLoading.value) {
    isLoading.value = true;
  }

  try {
    const res = await axios.get(`${API_URL}/all-tables`)
    tables.value = res.data;

    calendarOptions.value.resources = tables.value.map((table) => ({
      id: table.id,
      title: 'Bàn ' + table.table_number,
      capacity: 'Sức chứa ' + table.capacity,
      extendedProps: {
        tableNumber: table.table_number,
      }
    }));
    isLoading.value = false

  } catch (error) {
    console.log(error)
  } finally {
    isLoading.value = false;
  }

}

const orderOfTable = ref([])
const getOrderOfTable = async () => {
  if (!isLoading.value) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get(`${API_URL}/order-tables`)
    orderOfTable.value = res.data.orders
    calendarOptions.value.events = orderOfTable.value.map((order) => ({
      id: order.id,
      resourceId: order.tables.map((t) => `${t.id}`),
      start: order.reserved_from,
      end: order.reserved_to,
      extendedProps: {
        person_name: order.guest_name,
        person_phone: order.guest_phone,
        person_email: order.guest_email,
        event_color: '#fff5f5',
        status: order.order_status,
        total_price: formatNumber(order.total_price),
        total_quantity: order.total_quantity,
      },
    }))
  } catch (error) {
    console.log(error)
  } finally {
    isLoading.value = false
  }
}

const paymentMethod = ref('')
const payMoney = async () => {
  if (!info.value.id) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      title: 'Không tìm thấy ID đơn hàng!',
      icon: 'error',
      showConfirmButton: false,
      timer: 1500,
      timerProgressBar: true,
    })
    return
  }
  isLoading.value = true
  try {
    if (!paymentMethod.value) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        title: 'Vui lòng chọn phương thức thanh toán!',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      });
      return;
    }
    if (paymentMethod.value === 'VNPAY') {
      isLoading.value = false
      const paymentRes = await axios.post(`${API_URL}/payments/vnpay-init`, {
        order_id: info.value.id,
        amount: info.value.total_price,
        return_url: 'http://localhost:5173/admin/tables/booking-schedule',
      })
      if (paymentRes.data.payment_url) {
        Swal.fire({
          title: 'Đang chuyển hướng sang VNPAY...',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading()
            setTimeout(() => {
              window.location.href = paymentRes.data.payment_url
            }, 1000)
          },
        })
      }
      else {
        throw new Error('Không tạo được link thanh toán VNPAY!')
      }
      return
    }
    if (paymentMethod.value === 'MOMO') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        title: 'Chức năng thanh toán MoMo đang được phát triển!',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      })
      return
    }
    if (paymentMethod.value === 'COD') {
      await new Promise((resolve) => setTimeout(resolve, 300))
      await axios.post(`${API_URL}/admin/payments/cod-payment`, {
        order_id: info.value.id,
        amount_paid: info.value.total_price,
      })

      await axios.post(`${API_URL}/reservation-update-status`, {
        id: info.value.id,
        order_status: 'Hoàn thành',
        payment_status: 'Đã thanh toán'
      })

      info.value.order_status = 'Hoàn thành'

      Swal.fire({
        toast: true,
        position: 'top-end',
        title: 'Thanh toán bàn bằng tiền mặt thành công!',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      })
    }
    router.push({ path: '/admin/tables/booking-schedule', query: { order_id: info.value.id } })
      .then(() => window.location.reload());
  } catch (error) {
    console.log(error)
    Swal.fire({
      toast: true,
      position: 'top-end',
      title: 'Lỗi: Thanh toán thất bại!',
      icon: 'error',
      showConfirmButton: false,
      timer: 1500,
      timerProgressBar: true,
    })
    return
  } finally {
    isLoading.value = false
  }
}

function renderEventContent(arg) {
  const { event } = arg
  return {
    html: `
  <div class="custom-event-content" style="background-color: ${event.extendedProps.event_color};">
    <div class="title">Đơn hàng  ${event.id} - ${event.extendedProps.status}</div>
    <div class="event-person-name"><i class="fa fa-user"></i> ${event.extendedProps.person_name} - ${event.extendedProps.person_phone || event.extendedProps.person_email}</div>
    <div class="event-person-name"><i class="fa fa-user"></i>SL món: ${event.extendedProps.total_quantity} </div>
    <div class="event-person-name"><i class="bi bi-people"></i>Tổng tiền: ${event.extendedProps.total_price}</div>
  </div>
`,
  }
}
const showDetailPopup = ref(false)
const tempStatus = ref(info.value.order_status)
const getInfoDetail = async (arg) => {
  const { event } = arg
  await getInfo('order', event.id)
  showDetailPopup.value = true
  tempStatus.value = info.value.order_status
  console.log(info.value)
}

const calculateTotalItemPrice = (item) => {
  const basePrice = item.price * item.quantity
  const toppingTotal =
    (item.toppings?.reduce((sum, topping) => sum + Number(topping.price), 0) || 0) * item.quantity
  return basePrice + toppingTotal
}

const closeDetailPopup = () => {
  showDetailPopup.value = false
}

const updateStatus = async (id, status) => {
  const oldStatus = info.value.order_status
  try {
    const result = await Swal.fire({
      title: `Bạn có chắc chắn muốn cập nhật sang trạng thái ${status}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Xác nhận',
      cancelButtonText: 'Hủy',
      allowOutsideClick: false,
      allowEscapeKey: false,
    })
    if (result.isConfirmed) {
      await axios.post(`${API_URL}/reservation-update-status`, {
        id: id,
        order_status: status,
      })
      info.value.order_status = status
      tempStatus.value = status
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Cập nhật thành công!',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      })
      await getOrderOfTable()
    } else {
      tempStatus.value = oldStatus
    }
  } catch (error) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Có lỗi xảy ra',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    })
    console.log(error)
    tempStatus.value = oldStatus
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


const selectedDateInput = ref('');
const fullCalendarRef = ref(null);
const handleDateInputChange = () => {
  if (selectedDateInput.value) {
    if (fullCalendarRef.value) {
      const calendarApi = fullCalendarRef.value.getApi();
      calendarApi.gotoDate(selectedDateInput.value);
    }
  }
};

const handleDateClick = (clickDate) => {
  // const date = formatDateTime(clickDate.dateStr)
  // const date = formatDateTime1(clickDate.dateStr)
  router.push({
    name: 'insert-reservation-admin-date',
    params: { date: clickDate.dateStr } // chỉ lấy YYYY-MM-DD
  })
}

onMounted(async () => {

  selectedDateInput.value = new Date().toISOString().split('T')[0];
  // console.log(calendarOptions.value.events)
  // console.log(calendarOptions.value.resources)
  const params = new URLSearchParams(window.location.search)
  const hasVnpParams = params.has('vnp_TransactionStatus') || params.has('vnp_TxnRef')
  isLoading.value = true
  try {
    const res = await axios.get(`${API_URL}/admin/payments/vnpay-return`, { params })
    if (hasVnpParams) {
      if (res.data.success) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Thanh toán thành công!',
          showConfirmButton: false,
          timer: 2000,
        })
      } else {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Thanh toán thất bại hoặc có lỗi!',
          showConfirmButton: false,
          timer: 2000,
        })
      }
    }
  } catch (error) {
    if (hasVnpParams) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Lỗi xác minh thanh toán!',
        showConfirmButton: false,
        timer: 2000,
      })
    }
  }
  finally {
    isLoading.value = false
  }
})
const initialTablesLoaded = ref(false);


watch(
  [permissions, isLoadingPermissions],
  async ([newPermissions, newIsLoadingPermissions]) => {
    if (newPermissions.length > 0 && !newIsLoadingPermissions && !initialTablesLoaded.value) {
      await getTable();
      await getOrderOfTable();
      initialTablesLoaded.value = true;
    }
  },
  { immediate: true }
);


const calendarOptions = ref({
  plugins: [
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    resourceTimeGridPlugin,
    resourceTimelinePlugin,
  ],
  locale: viLocale,
  initialView: 'resourceTimelineDay',
  initialDate: new Date().toISOString().split('T')[0],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'resourceTimelineDay,timeGridWeek,dayGridMonth',
  },
  views: {
    resourceTimelineWeek: {
      type: 'resourceTimeline',
      duration: { days: 7 }, // tuần
      slotDuration: '01:00:00', // khoảng cách thời gian
    },
    resourceTimelineDay: {
      type: 'resourceTimeline',
      duration: { days: 1 },
      slotDuration: '00:30:00',
    },
  },
  slotLabelInterval: '00:30:00',
  slotDuration: '00:30:00',
  slotMinTime: '08:00:00',
  slotMaxTime: '21:00:00',
  allDaySlot: false,
  nowIndicator: true,
  slotLabelFormat: {
    hour: 'numeric',
    minute: '2-digit',
    omitZeroMinute: false,
  },
  timeZone: 'Asia/Ho_Chi_Minh',
  resourceAreaWidth: '150px',
  resources: [],
  resourceLabelContent: (resources) => {
    const title = resources.resource.title
    const capacity = resources.resource.extendedProps?.capacity
    return {
      html: `<div class="ban">${title}<br/><small class="time-badge">${capacity}</small></div>`,
    }
  },
  eventContent: renderEventContent,
  editable: false, // Không cho phép kéo thả sự kiện
  selectable: true,
  eventStartEditable: false, // Không cho phép thay đổi thời gian bắt đầu
  eventDurationEditable: true, // Không cho phép thay đổi thời lượng
  dateClick: handleDateClick,
  eventClick: getInfoDetail,
  resourceOrder: (resourceA, resourceB) => {
    const tableNumA = resourceA.extendedProps?.tableNumber || 0;
    const tableNumB = resourceB.extendedProps?.tableNumber || 0;

    return tableNumA - tableNumB;
  },
})
</script>

<style>
/* ======================== CARD ITEM ======================== */
.card-custom {
  border: 1px solid #eee;
  border-radius: 6px;
  padding: 12px;
  margin-top: 6px;
  background: #fff;
}

.item-name {
  font-weight: bold;
  font-size: 14px;
}

.item-details {
  font-size: 12px;
  color: #6c757d;
}

.total-price {
  text-align: right;
  font-size: 14px;
  font-weight: 600;
  color: #c92c3c;
}

.ban {
  background-color: #ffffff;
  /* border: 1px solid #ffffff; */
  font-weight: bold;
  /* border-radius: 5px; */
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

/* ======================== POPUP ======================== */
.event-detail-popup-overlay {
  position: fixed;
  inset: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1000;
}

.event-detail-popup {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  width: 90%;
  max-width: 1000px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  font-family: Arial, sans-serif;
}

/* Header */
.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background: #c92c3c;
  color: #fff;
}

.popup-title {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  font-size: 22px;
  color: #fff;
  cursor: pointer;
}

/* Body chia 2 cột */
.popup-body {
  display: flex;
  flex: 1;
  overflow: hidden;
}

.popup-content {
  flex: 2;
  padding: 20px;
  overflow-y: auto;
  border-right: 1px solid #eee;
}

.payment-timeline {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #fafafa;
}

/* Footer */
.popup-actions {
  padding: 12px 20px;
  border-top: 1px solid #eee;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

/* ======================== TIMELINE ======================== */
.timeline-item {
  position: relative;
  margin-bottom: 20px;
  padding-left: 28px;
}

.timeline-dot {
  position: absolute;
  top: 4px;
  left: 0;
  width: 12px;
  height: 12px;
  background: #c92c3c;
  border-radius: 50%;
}

.timeline-content {
  font-size: 14px;
  color: #333;
}

.timeline-content span {
  color: #c92c3c;
  font-weight: 500;
}

.event-detail-popup.show {
  opacity: 1;
  transform: translateY(0);
}

a {
  text-decoration: none;
}

.info-section {
  position: relative;
  left: 10px;
}

.info-item {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  /* khoảng cách giữa 2 cột */
  font-size: 0.95em;
  color: #1d1d1d;

}

.info-block {
  align-items: center;
  gap: 8px;
  padding: 5px;
}

.info-item i {
  margin-right: 12px;
  font-size: 1.1em;
}

.info-item span {
  flex-grow: 1;
}

.edit-button {
  color: #c92c3c;
  border: 1px solid #c92c3c;
  background-color: #fff;
  padding: 6px 14px;
  border-radius: 6px;
  height: 35px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.edit-button:hover {
  background-color: #c92c3c;
  color: #fff;
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

.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}

.fc .fc-toolbar-title {
  font-size: 1.5em;
  font-weight: 600;
  color: #333;
}

.fc-button-group .fc-button {
  background-color: transparent;
  border: 1px solid #ccc;
  color: #555;
  border-radius: 4px;
  padding: 6px 12px;
  margin-right: -1px;
}

.fc-button-group .fc-button:hover {
  background-color: #f0f0f0;
}

.fc-button-group .fc-button.fc-button-active {
  background-color: #007bff;
  border-color: #007bff;
  color: white;
  box-shadow: none;
}

.fc-button-group .fc-button:first-child {
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.fc-button-group .fc-button:last-child {
  border-top-right-radius: 4px;
  border-bottom-right-radius: 4px;
}

/* Header của lịch (thứ trong tuần, giờ) */
.fc-timegrid-axis-frame {
  display: flex;
}

.fc .fc-col-header-cell-cushion {
  display: block;
  font-size: 1.1em;
  text-decoration: none;
  color: #000000;
}

/* Nhãn thời gian ở trục Y (ví dụ: "8am", "9am") */
.fc .fc-timegrid-slot-label-frame {
  color: #000000;
  font-size: 0.9em;
  text-align: center;
  font-weight: 500;
}

.fc-timegrid-slot-label {
  visibility: visible !important;
}

/* Các ô thời gian và đường kẻ trong lưới lịch */
.fc .fc-timegrid-slot {
  height: 30px;
  border-bottom: 1px dashed #eee;
}

.fc .fc-timegrid-slot.fc-timegrid-slot-minor {
  border-top: none;
  height: 15px;
}

.fc .fc-timegrid-divider {
  display: none;
}

.loader-wrapper1 {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
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

/* Đường kẻ thời gian hiện tại (Now Indicator) */
.fc-timegrid-now-indicator-line {
  border-top: 1px solid rgb(226, 26, 26) !important;
  z-index: 2;
}

.fc-timegrid-now-indicator-arrow {
  color: red !important;
  border-left: none !important;
  z-index: 2;
}

.fc-timegrid-now-indicator-border {
  border-color: red !important;
  z-index: 2;
}

.fc .fc-timegrid-slot-label-cushion {
  padding: 0px 0px;
}

.fc-timegrid-now-indicator-line+.fc-timegrid-now-indicator-label {
  background-color: red;
  color: white;
  border-radius: 3px;
  padding: 2px 5px;
  font-size: 0.8em;
  position: absolute;
  left: 0;
  margin-top: -10px;
  z-index: 3;
}

.fc-event {
  border-radius: 6px;
  padding: 0px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: normal;
  position: relative;
  font-size: 0.8em;
  line-height: 1.3;
  margin-bottom: 2px;
  background: none !important;
  border: none !important;
  color: inherit !important;
}

.custom-event-content {
  height: 100%;
  width: 100%;
  padding: 6px 8px;
  border-radius: 2px;
  display: flex;
  flex-direction: column;
  color: #333;
  gap: 2px;
  border-left: 8px solid #c92c3c;
  cursor: pointer;
}

.fc-datagrid-cell-frame {
  height: none !important;
}

.fc-license-message {
  display: none;
}

.custom-event-content .event-person-name {
  display: inline-flex;
  align-items: center;
  font-size: 0.85em;
  color: #3d3c3c;
}

.custom-event-content .event-person-name i {
  margin-right: 5px;
}

.fc-event-time {
  display: none;
}

.fc-event-title {
  display: none;
}

.fc .fc-timegrid-col {
  border-right: 1px solid #eee;
}

.fc .fc-timegrid-col-frame {
  border-left: none;
}

.fc-timegrid-bg {
  display: none;
}

.fc .fc-day-today {
  background-color: #fcfcfc;
}

.fc-timegrid-cols .fc-day:last-child {
  border-right: none;
}

.fc .fc-timeline-slot-cushion {
  padding: 4px 10px;
  white-space: nowrap;
  text-decoration: none;
  color: black;
  text-align: center;
}

.fc .fc-timeline-header-row-chrono .fc-timeline-slot-frame {
  justify-content: center;
}

/* ==================================================================== */
/* CSS riêng cho Resource View */
/* ==================================================================== */

/* Tùy chỉnh cột tiêu đề tài nguyên (cột "Bàn") */
.fc .fc-resource-timegrid-sidebar {
  background-color: #f8f8f8;
  /* Nền giống header ngày */
  border-right: 1px solid #eee;
  /* Viền phải */
}

/* Tiêu đề của mỗi tài nguyên (ví dụ: "Bàn 1") */
.fc .fc-resource-timegrid-label-cell {
  padding: 8px 10px;
  font-weight: 600;
  color: #333;
  text-align: left;
  /* Căn trái cho tiêu đề bàn */
  border-bottom: 1px solid #eee;
  /* Viền dưới */
  display: flex;
  align-items: center;
  min-height: 40px;
  /* Đảm bảo đủ cao cho nội dung */
}

/* Phần chia giữa cột tài nguyên và các cột ngày */
.fc-resource-timegrid-separator {
  background-color: #eee;
  width: 1px;
  /* Chiều rộng đường kẻ */
}

/* Để tiêu đề tài nguyên không bị cắt nếu có nhiều dòng */
.fc .fc-resource-timegrid-label-cell .fc-resource-label-text {
  white-space: normal;
  word-wrap: break-word;
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

/* ======================== RESPONSIVE ======================== */
@media (max-width: 768px) {
  .popup-body {
    flex-direction: column;
  }

  .popup-content {
    border-right: none;
    border-bottom: 1px solid #eee;
  }

  .payment-timeline {
    border-top: 1px solid #eee;
  }
}
</style>
