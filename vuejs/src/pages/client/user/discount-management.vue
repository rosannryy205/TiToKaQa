<template>
  <div class="container col-12 col-md-8 col-lg-9 py-4">
    <h4 class="fw-bold mb-3">Kho mã giảm giá</h4>
    <div class="d-flex align-items-center mb-3" style="gap: 10px">
      <input
        v-model="voucherCode"
        type="text"
        class="form-control"
        placeholder="Nhập mã voucher tại đây"
        style="max-width: 400px; font-size: 14px; border-radius: 0.25rem"
      />
      <button class="btn btn-save-discount px-4" @click="handleVoucherCode">Lưu</button>
    </div>
    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4">
      <div
        v-for="(tab, index) in tabs"
        :key="index"
        @click="activeTab = index"
        :class="['tab-item', { active: activeTab === index }]"
        style="cursor: pointer"
      >
        {{ tab.label }}
        <span v-if="tab.count" class="text-secondary">({{ tab.count }})</span>
      </div>
    </div>

    <div class="row g-3">
      <div
        v-for="discount in filterUserDiscount"
        :key="discount.discount_user_id || discount.id"
        class="col-md-6"
      >
        <div
          v-if="activeTab === 4"
          class="d-flex align-items-center bg-white shadow-sm rounded p-3 w-100"
        >
          <i class="bi bi-ticket-perforated text-danger fs-4 me-3"></i>
          <div class="flex-grow-1" style="min-width: 0">
            <div class="fw-semibold mb-1 text-truncate d-block">
              {{ getVoucherHistoryLabel(discount.source || discount.pivot?.source) }}
              <span class="text-primary">"{{ discount.name }}"</span>
            </div>

            <div class="text-muted small">
  🕒 {{ formatDate(discount.exchanged_at_iso || discount.exchanged_at) || 'Không rõ' }}
</div>
          </div>
        </div>
        <div
          v-else
          class="d-flex shadow-sm bg-white rounded overflow-hidden"
          :class="{ 'expired-discount': isExpired(discount) }"
          style="min-height: 110px"
        >
          <div
            class="text-white d-flex flex-column justify-content-center align-items-center"
            :style="`background-color: ${discount.discount_type === 'freeship' ? '#00bfa5' : '#f44336'}; width: 28%`"
          >
            <img :src="getImageByType(discount.discount_type)" alt="icon" style="width: 40px" />
            <div class="fw-bold small mt-2 text-center" style="font-size: 12px">
              {{ discount.discount_type === 'freeship' ? 'FREESHIP' : 'GIẢM GIÁ' }}
            </div>
          </div>

          <div class="flex-grow-1 px-3" style="width: 72%">
            <div class="fw-bold mb-1 text-truncate" :title="discount.code">
              Mã: {{ discount.code }}
            </div>
            <div class="text-muted small mb-1 text-truncate d-block" :title="discount.name">
              {{ discount.name }}
            </div>
            <div class="text-muted small mb-1 text-truncate d-block">
              <i class="bi bi-clock me-1"></i>
              Hạn dùng:
              {{ formatDate(discount.expiry_at || discount.end_date || discount.pivot?.expiry_at) || '—' }}
            </div>

            <!-- Điều kiện -->
            <div class="text-muted small mb-1 text-truncate">
              <a
                href="#"
                class="ms-1 text-primary"
                @click.prevent="showConditionModal(discount.condition, discount.name)"
              >
                Điều kiện
              </a>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div
                class="fw-bold coins-exchange d-flex align-items-center"
                :class="{ invisible: activeTab !== 4 }"
              >
                {{ formatCurrency(discount.cost) }}
                <img class="coins ms-1" src="/img/xubac.png" alt="coin" />
              </div>
              <router-link
                v-if="!isExpired(discount)"
                to="/food"
                class="btn btn-outline-danger btn-sm float-end"
              >
                Dùng Ngay
              </router-link>
              <button v-else class="btn btn-outline-danger btn-sm float-end" disabled>
                Hết hạn
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="filterUserDiscount.length === 0" class="col-12">
        <div class="text-center text-muted py-5">
          <i class="bi bi-ticket-perforated fs-1 mb-3 d-block"></i>
          <p class="fw-bold mb-1">Kho đang trống</p>
          <p class="mb-0">
            Vui lòng đổi mã bằng
            <img src="/img/xubac.png" alt="Tcoin" style="width: 16px; vertical-align: text-bottom" />
            Tcoin
          </p>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal điều kiện -->
  <div
    class="modal fade"
    id="voucherConditionModal"
    tabindex="-1"
    aria-labelledby="voucherConditionModalLabel"
    aria-hidden="true"
    ref="conditionModalRef"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title fw-bold me-5">Điều kiện voucher</h6>
          <button type="button" class="btn-close" @click="hideConditionModal"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0 text-dark fs-5 text-center">Mã: {{ selectedVoucherName }}</p>
        </div>
        <div class="modal-body">
          <p class="mb-0 small text-danger text-center">
            {{ selectedVoucherCondition || 'Không có điều kiện cụ thể.' }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import { Modal } from 'bootstrap'
import axios from 'axios'
import { Discounts } from '@/stores/discount'
import { useUserStore } from '@/stores/userAuth'
import Swal from 'sweetalert2'
import { API_URL } from '@/config'

const {
  getImageByType,
  formatCurrency,
  userDiscounts,     // từ store: đã normalizeUser
  discounts,         // từ store: đã normalizeSystem
  getAllDiscount,
  fetchUserDiscounts,
} = Discounts()

const userStore = useUserStore()
const systemDiscounts = ref([])

const voucherCode = ref('')
const activeTab = ref(0) // 0: tất cả | 1: salefood | 2: freeship | 3: theo danh mục | 4: lịch sử | 5: hết hạn

// ===== Helpers thời gian
const isExpired = (d) => {
  // Ưu tiên BE nếu đã trả is_expired
  if (typeof d?.is_expired === 'boolean') return d.is_expired
  const deadline = d?.expiry_at ?? d?.end_date
  return deadline ? new Date(deadline).getTime() < Date.now() : false
}

const formatDate = (dateStr) => {
  if (!dateStr) return null
  const date = new Date(dateStr)
  return date.toLocaleString('vi-VN', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

// ===== Tập dữ liệu người dùng
const allUser = computed(() => userDiscounts.value ?? [])
const userActive  = computed(() => allUser.value.filter(d => !isExpired(d)))
const userByCategory = computed(() => userActive.value.filter(d => d.category_id != null))
const userExpired = computed(() => allUser.value.filter(isExpired))
const userSalefood   = computed(() => userActive.value.filter(d => d.discount_type === 'salefood'))
const userFreeship   = computed(() => userActive.value.filter(d => d.discount_type === 'freeship'))

const userHistory = computed(() =>
  (userDiscounts.value ?? []).filter(d => !!d.exchanged_at || !!d.source)
)

// Tabs + counts động
const tabs = computed(() => ([
  { label: 'Tất cả',            count: userActive.value.length },
  { label: 'Mã Giảm Món',       count: userSalefood.value.length },
  { label: 'Mã FreeShip',       count: userFreeship.value.length },
  { label: 'Mã Theo Danh Mục',  count: userByCategory.value.length },
  { label: 'Lịch Sử Đổi Mã',    count: userHistory.value.length },
  { label: 'Mã hết hạn',        count: userExpired.value.length },
]))


// Danh sách hiển thị theo tab
const filterUserDiscount = computed(() => {
  switch (activeTab.value) {
    case 1: return userSalefood.value
    case 2: return userFreeship.value
    case 3: return userByCategory.value
    case 4: return userHistory.value
    case 5: return userExpired.value
    default: return userActive.value
  }
})

// ===== Điều kiện hiển thị (build nếu BE chưa trả)
function buildConditionText(d) {
  if (!d) return ''
  const parts = []
  if (d.min_order_value) parts.push(`ĐH tối thiểu ${Number(d.min_order_value).toLocaleString('vi-VN')}đ`)
  if (d.discount_type === 'salefood') {
    parts.push(d.discount_method === 'percent'
      ? `Giảm ${d.discount_value}%`
      : `Giảm ${Number(d.discount_value).toLocaleString('vi-VN')}đ`)
  }
  if (d.discount_type === 'freeship') {
    parts.push(d.discount_method === 'percent'
      ? `Giảm phí ship ${d.discount_value}%`
      : `Giảm phí ship ${Number(d.discount_value).toLocaleString('vi-VN')}đ`)
  }
  if (d.category_id != null) parts.push('Áp dụng theo danh mục')
  return parts.join(' • ')
}

const conditionModalRef = ref(null)
let conditionModalInstance = null
const selectedVoucherCondition = ref('')
const selectedVoucherName = ref('')

const showConditionModal = (conditionFromBE, name, discountObj = null) => {
  selectedVoucherCondition.value = conditionFromBE || buildConditionText(discountObj)
  selectedVoucherName.value = name
  conditionModalInstance?.show()
}
const hideConditionModal = () => conditionModalInstance?.hide()

// ===== Lưu/đổi mã theo code
const handleVoucherCode = async () => {
  const code = voucherCode.value.trim().toUpperCase()
  if (!code) {
    return Swal.fire({
      icon: 'warning',
      title: 'Vui lòng nhập mã voucher!',
      toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true,
    })
  }

  // đã có trong kho cá nhân?
  const exists = (userDiscounts.value || []).find(d => (d.code || '').toUpperCase() === code)
  if (exists) {
    return Swal.fire({
      icon: 'info',
      title: 'Bạn đã có mã này rồi!',
      toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true,
    })
  }

  // tìm trong hệ thống
  const found = (systemDiscounts.value || []).find(d => (d.code || '').toUpperCase() === code)
  if (!found) {
    return Swal.fire({
      icon: 'error',
      title: 'Không tìm thấy mã trong hệ thống!',
      toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true,
    })
  }

  const confirm = await Swal.fire({
    title: 'Bạn chắc chắn muốn lưu/đổi mã này?',
    text: `Mã: ${code}`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Xác nhận',
    cancelButtonText: 'Hủy',
  })
  if (!confirm.isConfirmed) return

  try {
    const res = await axios.post(
      `${API_URL}/redeem-discount`,
      { discount_id: found.id },               // normalizeSystem.id = discount gốc
      { headers: { Authorization: `Bearer ${userStore.token}` } },
    )

    if (res.data?.status) {
      await fetchUserDiscounts()
      voucherCode.value = ''
      Swal.fire({
        toast: true, position: 'top-end', icon: 'success',
        title: 'Đổi/Lưu mã thành công!', showConfirmButton: false, timer: 1500, timerProgressBar: true,
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: res.data?.message || 'Đổi/Lưu mã thất bại!',
        toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true,
      })
    }
  } catch (err) {
    console.error(err)
    Swal.fire({
      icon: 'error',
      title: err?.response?.data?.message || 'Lỗi khi gửi yêu cầu!',
      toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true,
    })
  }
}

// ===== Nhãn lịch sử
const getVoucherHistoryLabel = (source) => {
  switch ((source || '').toLowerCase()) {
    case 'point_exchange':
    case 'tpoint':
      return 'Bạn đã đổi mã bằng Tcoin'
    case 'discount':
      return 'Bạn đã lưu mã'
    case 'lucky_wheel':
      return 'Nhận từ vòng quay'
    case 'redeem_code':
      return 'Nhập mã đổi thưởng'
    case 'system_grant':
      return 'Hệ thống tặng'
    default:
      return 'Bạn đã nhận mã'
  }
}

// ===== Lifecycle
onMounted(async () => {
  // nạp mã hệ thống (store đã lọc hiệu lực theo start/end)
  await getAllDiscount({ source: 'system' })
  systemDiscounts.value = discounts.value.slice()

  // nạp kho mã cá nhân (store đã normalize: category_id là Number hoặc null; exchanged_at/source nằm trên root)
  await fetchUserDiscounts()

  // init modal
  if (conditionModalRef.value) {
    conditionModalInstance = new Modal(conditionModalRef.value)
  }
})
</script>




<style scoped>
.category-icon {
  width: 50px;
  height: 50px;
  object-fit: contain;
}
.voucher-card {
  padding: 5px;
}

.voucher-card img {
  width: auto;
  height: 120px;
  display: block;
  margin: 0 auto 10px auto;
}

.voucher-card .card-body {
  padding: 5px;
}

.voucher-card h6 {
  font-size: 14px;
  margin-bottom: 6px;
}

.coins-exchange {
  color: rgb(119, 119, 119) !important;
}

.coins {
  width: 15px !important;
  height: 15px !important;
  margin: 6px 5px 5px 5px !important;
}
.title-cate-discount,
.title-discount-hot {
  color: #c92c3c;
}

@media (max-width: 576px) {
  .category-icon {
    width: 18px;
    height: 18px;
  }

  .voucher-card img {
    height: 120px;
  }
}
.coins-small {
  width: 24px;
  height: 24px;
}
@media (max-width: 576px) {
  .coins-small {
    width: 20px;
    height: 20px;
  }
}
.btn-sm {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}
.btn-save-discount {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}
.btn-save-discount:hover {
  background-color: #c92c3c;
  color: white;
}
.has-voucher {
  color: #007d00;
  border: 1px solid #007d00;
}
.btn-sm:hover {
  background-color: #c92c3c;
  color: white;
}
.voucher-brand-btn.active {
  background-color: #c92c3c;
  color: white;
}

.btn-save-discount {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}
.btn-save-discount:hover {
  background-color: #c92c3c;
  color: white;
}
.expired-discount {
  opacity: 0.6;
  pointer-events: none;
  filter: grayscale(0.5);
}
/**tab4 */
.voucher-row-thin {
  border: 1px solid #f0f0f0;
  border-radius: 8px;
  background-color: #fafafa;
}
.voucher-list-wrapper {
  max-height: 500px;
  overflow-y: auto;
  padding-right: 5px;
}

#app>div>div.container.mt-5.fade-in>div>div.container.col-12.col-md-8.col-lg-9.py-4>div.row.g-3 {
  max-height: 80vh;
  overflow-y: auto;
  padding: 6px;
}
/**an text */
.voucher-title {
  max-width: 100%;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  display: block;
}
.order-tabs {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  white-space: nowrap;
}

.order-tabs::-webkit-scrollbar {
  display: none;
}

.tab-item {
  flex: 0 0 auto;
  /* QUAN TRỌNG: không co lại */
  padding: 0.6rem 1.2rem;
  white-space: nowrap;
  border-radius: 8px;
  font-weight: 500;
  font-size: 1rem;
  letter-spacing: 0.5px;
  color: #6c757d;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: #f8f9fa;
}

.tab-item.active {
  border-bottom-color: #c92c3c;
  color: #c92c3c;
  background-color: #fff;
  font-weight: 600;
}
@media (max-width: 768px) {
  .tab-item {
    padding: 0.75rem 1.2rem;
    font-size: 0.95rem;
    letter-spacing: 0.4px;
  }
}
</style>
