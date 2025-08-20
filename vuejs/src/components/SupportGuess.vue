<template>
  <div>
    <div class="popup-mid-right" v-if="visible">
      <div class="popup-inner">
        <button class="popup-close" @click="closePopup">×</button>

        <a class="plain-btn" @click="openModal">
          <img src="/img/support_guess.png" alt="Support" class="wiggle-image" />
        </a>

        <a class="popup-button" @click="openModal">HỖ TRỢ ⚠️</a>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="modal-overlay" @click.self="handleModalClose">
      <div class="modal-card">
        <div class="modal-header">
          <h5 class="m-0">Tra cứu đơn hàng</h5>
        </div>

        <div class="modal-body">
          <!-- Form -->
          <div class="form-grid">
            <div>
              <label class="form-label">Số điện thoại</label>
              <input
                type="tel"
                class="form-control"
                placeholder="Số điện thoại đặt hàng"
                v-model.trim="phone"
                @keyup.enter="findOrder(code, phone)"
              />
            </div>
            <div>
              <label class="form-label">Mã đơn</label>
              <input
                type="text"
                class="form-control"
                placeholder="VD: SD0REBAZAN"
                v-model.trim="code"
                @keyup.enter="findOrder(code, phone)"
              />
            </div>
          </div>

          <div class="actions">
            <button type="button" class="btn btn-outline btn-sm" @click="findOrder(code, phone)">
              <span v-if="!loading">Tra cứu</span>
              <span v-else>Đang tra cứu…</span>
            </button>
            <button class="btn btn-light" @click="handleModalClose">Đóng</button>
          </div>

          <div v-if="error" class="alert alert-danger mt-2">{{ error }}</div>
          <div v-else-if="loading" class="mt-2 text-muted">Đang tải kết quả…</div>

          <section v-if="searchedByCode && order" class="order-detail-card mt-3">
            <div class="od-top">
              <div>
                <div class="code">#{{ order.code ?? order.order_code }}</div>
                <div class="meta">
                  <span
                    >Trạng thái: <strong>{{ order.status }}</strong></span
                  >
                </div>
                <div class="meta">
                  <span>Đặt lúc: {{ formatDate(order.order_time) }}</span>
                </div>
              </div>
              <div class="right-actions">
                <div class="price text-dark fw-bold">{{ formatVND(order.total_price) }}</div>
              </div>
            </div>

            <ul class="od-items">
              <li v-for="(d, idx) in order.details" :key="idx" class="od-row">
                <img v-if="getItemImage(d)" :src="getItemImage(d)" :alt="d.name" class="od-thumb" />
                <div v-else class="od-thumb od-thumb--empty"></div>

                <div class="od-info">
                  <div class="od-name">{{ d.name }}</div>
                </div>

                <div class="od-qty">× {{ d.quantity }}</div>
              </li>
            </ul>

            <div class="od-sum text-dark fw-bold">
              <div>
                Phí ship: <strong>{{ formatVND(order.shipping_fee || 0) }}</strong>
              </div>
              <div>
                Tổng thanh toán: <strong>{{ formatVND(order.total_price || 0) }}</strong>
              </div>
            </div>
            <div class="od-actions">
              <button
                type="button"
                class="btn btn-outline btn-sm"
                :disabled="loading || cancelBusy || !canCancel(order)"
                @click="cancelOrder(order)"
              >
                Huỷ đơn
              </button>
            </div>
          </section>

          <!-- Danh sách khi tra bằng PHONE -->
          <div class="d-none d-md-block" v-if="!searchedByCode">
            <template v-if="orders && orders.length">
              <table class="table w-100 custom-borderless-table">
                <thead class="table-secondary">
                  <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="o in orders" :key="o.id">
                    <td>{{ o.id }}</td>
                    <td>{{ formatDate(o.order_time ?? o.created_at) }}</td>
                    <td>{{ formatVND(o.total_price) }}</td>
                    <td class="text-primary">{{ o.status }}</td>
                    <td>
                      <button class="btn btn-outline btn-sm" @click="openOrder(o)">Xem</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </template>
            <template v-else>
              <div class="text-center"><span>Vui lòng nhập mã hoặc SĐT để tra cứu</span></div>
            </template>
          </div>
        </div>

        <div class="modal-footer">
          <small class="text-muted">Cần hỗ trợ? Liên hệ fanpage hoặc hotline của chúng tôi.</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const API_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'
const ASSET_BASE = import.meta.env.VITE_ASSET_BASE || 'http://127.0.0.1:8000'
const CANCELLABLE = ['Chờ xác nhận', 'Đã xác nhận']

// UI & state
const isModalOpen = ref(false)
const visible = ref(true)

const code = ref('')
const phone = ref('')
const orders = ref([])
const order = ref(null)
const searchedByCode = ref(false)
const loading = ref(false)
const error = ref('')
const cancelBusy = ref(false)

// helpers
function formatVND(v) {
  return Number(v || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })
}
function formatDate(iso) {
  const d = new Date(iso)
  return isNaN(d) ? '' : d.toLocaleString('vi-VN')
}
const getImageUrl = (image) => (image ? `${ASSET_BASE}/storage/img/food/${image}` : null)
function getItemImage(d) {
  const img = d?.food?.image || d?.foods?.image || d?.combo?.image || d?.combos?.image || d?.image
  return getImageUrl(img)
}
function getStatus(o) {
  return (o?.status ?? o?.order_status ?? '').trim()
}
function canCancel(o) {
  return CANCELLABLE.includes(getStatus(o))
}

// actions
async function findOrder(codeArg, phoneArg) {
  if (loading.value) return
  const c = (codeArg ?? code.value)?.trim()
  const p = (phoneArg ?? phone.value)?.trim()
  if (!c && !p) return

  // reset input ngay khi bấm tra
  code.value = ''
  phone.value = ''

  try {
    loading.value = true
    error.value = ''

    if (c) {
      const { data } = await axios.get(`${API_URL}/orders/${encodeURIComponent(c)}`)
      order.value = data?.data ?? null
      searchedByCode.value = true
      orders.value = []
    } else {
      const { data } = await axios.get(`${API_URL}/orders/lookup`, {
        params: { phone: p, limit: 5 },
      })
      orders.value = Array.isArray(data?.data) ? data.data : []
      order.value = null
      searchedByCode.value = false
    }
  } catch (e) {
    console.error('lookup/show error:', e?.response?.status, e?.response?.data)
    error.value =
      e?.response?.data?.message ||
      (e?.response?.status ? `Lỗi ${e.response.status}` : 'Không kết nối được máy chủ')
    orders.value = []
    order.value = null
  } finally {
    loading.value = false
  }
}

async function openOrder(o) {
  try {
    loading.value = true
    error.value = ''
    const codeToUse = o?.code ?? o?.order_code
    if (!codeToUse) throw new Error('Thiếu mã đơn để xem chi tiết')
    const { data } = await axios.get(`${API_URL}/orders/${encodeURIComponent(codeToUse)}`)
    order.value = data?.data ?? null
    searchedByCode.value = true
  } catch (e) {
    console.error('show error:', e?.response?.status, e?.response?.data)
    error.value = e?.response?.data?.message || e?.message || 'Không lấy được chi tiết đơn'
  } finally {
    loading.value = false
  }
}

async function cancelOrder(o) {
  if (cancelBusy.value) return
  const codeToUse = o?.code ?? o?.order_code
  if (!codeToUse) {
    console.warn('Không có mã đơn:', o)
    return
  }
  if (!canCancel(o)) {
    return Swal.fire({
      icon: 'info',
      title: 'Không thể huỷ',
      html: `Trạng thái hiện tại: <b>${getStatus(o) || 'Không xác định'}</b><br>
             Chỉ huỷ khi: <b>${CANCELLABLE.join(' hoặc ')}</b>.`,
      confirmButtonColor: '#c92c3c',
    })
  }

  cancelBusy.value = true
  try {
    const { isConfirmed, value } = await Swal.fire({
      title: 'Huỷ đơn hàng',
      html: `<p style="margin:0 0 8px">Vui lòng gõ <b>xacnhan</b> để xác nhận huỷ đơn.</p>`,
      input: 'text',
      inputPlaceholder: 'Nhập "xacnhan"',
      showCancelButton: true,
      confirmButtonText: 'Huỷ đơn',
      cancelButtonText: 'Đóng',
      reverseButtons: true,
      confirmButtonColor: '#c92c3c',
      focusConfirm: false,
      preConfirm: async (txt) => {
        const v = String(txt || '')
          .trim()
          .toLowerCase()
        if (v !== 'xacnhan') {
          Swal.showValidationMessage('Bạn phải gõ chính xác "xacnhan"')
          return false
        }
        try {
          const res = await axios.post(
            `${API_URL}/orders/${encodeURIComponent(codeToUse)}/cancel`,
            { confirm: 'xacnhan' },
          )
          return res.data
        } catch (err) {
          const msg = err?.response?.data?.message || 'Huỷ đơn không thành công'
          Swal.showValidationMessage(msg)
          return false
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
      showLoaderOnConfirm: true,
    })

    if (isConfirmed && value) {
      order.value = value?.data ?? { ...o, status: 'Đã huỷ' }
      await Swal.fire({
        icon: 'success',
        title: 'Đã huỷ đơn',
        text: `Đơn #${codeToUse} đã được huỷ.`,
        confirmButtonColor: '#c92c3c',
        timer: 1800,
        showConfirmButton: true,
      })
      handleModalClose()
    }
  } finally {
    cancelBusy.value = false
  }
}

// modal helpers
function closePopup() {
  isModalOpen.value = false
  visible.value = false
}
function openModal() {
  resetModalState()
  isModalOpen.value = true
}
function resetModalState() {
  code.value = ''
  phone.value = ''
  orders.value = []
  order.value = null
  error.value = ''
  searchedByCode.value = false
  loading.value = false
}
function handleModalClose() {
  resetModalState()
  isModalOpen.value = false
}
</script>

<style scoped>
.btn-outline {
  background: #fff;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  transition: all 0.2s ease;
}

.btn-outline:hover {
  background: #c92c3c;
  color: #fff;
}
/* ===== Floating popup ===== */
.popup-mid-right {
  position: fixed;
  bottom: 15%;
  right: 20px;
  z-index: 10;
}
.popup-inner {
  position: relative;
  text-align: center;
  width: 120px;
  background: transparent;
}
.popup-close {
  cursor: pointer !important;
  z-index: 3;
  pointer-events: auto;
  position: absolute;
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
  background: white;
  border: 1px solid #ccc;
  color: #e74c3c;
  font-size: 14px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: default;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  line-height: 1;
}
.popup-button {
  margin-top: 6px;
  background-color: #c92c3c;
  color: white;

  padding: 6px 12px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 12px;
  text-decoration: none;
  display: inline-block;
  cursor: pointer;
}
@keyframes wiggle {
  0%,
  100% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(3deg);
  }
  50% {
    transform: rotate(-3deg);
  }
  75% {
    transform: rotate(2deg);
  }
}
.wiggle-image {
  animation: wiggle 1.5s infinite ease-in-out;
  transform-origin: bottom center;
  display: inline-block;
  width: 100%;
  height: auto;
  cursor: pointer;
}

/* ===== Modal ===== */
.modal-close {
  background: transparent;
  background-image: none;
  border: none;
  width: 32px;
  height: 32px;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  place-items: center;
  z-index: 1060;
  padding: 14px;
}
.modal-overlay.is-open {
  display: grid;
}
.modal-card {
  width: 100%;
  max-width: 720px;
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.modal-header,
.modal-footer {
  padding: 12px 16px;
  background: #fafafa;
  border-bottom: 1px solid #eee;
}
.modal-footer {
  border-top: 1px solid #eee;
  border-bottom: none;
}
.btn-close {
  border: none;
  font-size: 22px;
  cursor: default;
}
.modal-body {
  padding: 16px;
}

.form-grid {
  display: grid;
  gap: 12px;
}
@media (min-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
  }
}
.form-label {
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
}
.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f7f7f7;
  color: #888;
}
.actions {
  margin-top: 10px;
  display: flex;
  gap: 10px;
}

.btn-light {
  background: #fff;
  border-color: #ddd;
}

.alert {
  padding: 10px 12px;
  border-radius: 8px;
}
.alert-danger {
  background: #ffe0e0;
  color: #8a1f1f;
}
.alert-warning {
  background: #fff7e0;
  color: #8a6d1f;
}

.result-head {
  margin-top: 6px;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.order-item {
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 10px 12px;
}
.order-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.order-row .left {
  min-width: 0;
}
.code {
  color: #c92c3c;
  font-weight: 700;
}
.meta {
  display: flex;
  gap: 6px;
  align-items: center;
  color: #666;
  font-size: 13px;
  flex-wrap: wrap;
}
.price {
  font-weight: 700;
}
.order-details {
  margin-top: 8px;
  border-top: 1px dashed #eee;
  padding-top: 8px;
}
.items {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 6px;
}
.items li {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 10px;
  align-items: center;
}
.single-order .top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 8px;
}
.sum {
  margin-top: 10px;
  line-height: 1.6;
}
.order-detail-card {
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 12px;
}
.od-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.od-top .code {
  font-weight: 700;
}
.meta {
  display: flex;
  gap: 8px;
  align-items: center;
  color: #666;
  font-size: 13px;
  flex-wrap: wrap;
}
.right-actions .price {
  font-weight: 700;
}

.od-items {
  list-style: none;
  padding: 0;
  margin: 12px 0 0;
  display: grid;
  gap: 10px;
}
.od-row {
  display: grid;
  grid-template-columns: 64px 1fr auto;
  align-items: center;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px dashed #eee;
}
.od-row:last-child {
  border-bottom: none;
}

.od-thumb {
  width: 64px;
  height: 64px;
  object-fit: cover;
  border-radius: 8px;
  background: #f5f5f5;
}
.od-thumb--empty {
  display: block;
}
.od-info .od-name {
  font-weight: 600;
}
.od-qty {
  min-width: 56px;
  text-align: right;
  font-weight: 600;
  color: #333;
}
.od-actions {
  display: flex;
  justify-content: flex-end;
}
</style>
]
