<template>
  <div class="container col-12 col-md-8 col-lg-9 py-4">
    <h4 class="fw-bold mb-3">Kho Voucher</h4>

    <!-- Nhập mã voucher -->
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

    <!-- Tabs -->
    <div class="d-flex border-bottom mb-3" style="gap: 20px; font-size: 14px">
      <div
        v-for="(tab, index) in tabs"
        :key="index"
        @click="activeTab = index"
        class="pb-2 position-relative"
        :class="{ 'fw-bold text-danger': activeTab === index, 'text-muted': activeTab !== index }"
        style="cursor: pointer"
      >
        {{ tab.label }}
        <span v-if="tab.count" class="text-secondary">({{ tab.count }})</span>
        <span
          v-if="activeTab === index"
          class="position-absolute start-0 bottom-0 w-100"
          style="height: 2px; background-color: #d9363e"
        ></span>
      </div>
    </div>
    <div class="row g-3">
  <div
    v-for="discount in filterUserDiscount"
    :key="discount.id"
    class="col-md-6"
  >
    <div
      v-if="activeTab === 4"
      class="d-flex align-items-center bg-white shadow-sm rounded p-3 w-100"
    >
      <i class="bi bi-ticket-perforated text-danger fs-4 me-3"></i>
      <div class="flex-grow-1">
        <div class="fw-semibold mb-1">
          {{ getVoucherHistoryLabel(discount.pivot?.source) }}
          <span class="text-primary">"{{ discount.name }}"</span>
        </div>
        <div class="text-muted small">
          🕒 {{ formatDate(discount.pivot?.exchanged_at) || 'Không rõ' }}
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
        :style="`background-color: ${
          discount.discount_type === 'freeship' ? '#00bfa5' : '#f44336'
        }; width: 28%`"
      >
        <img :src="getImageByType(discount.discount_type)" alt="icon" style="width: 40px" />
        <div class="fw-bold small mt-2 text-center" style="font-size: 12px">
          {{ discount.discount_type === 'freeship' ? 'FREESHIP' : 'GIẢM GIÁ' }}
        </div>
      </div>

      <div class="flex-grow-1 px-3 py-2" style="width: 72%">
        <div class="fw-bold mb-1 text-truncate">Mã: {{ discount.name }}</div>
        <div class="text-muted small mb-1 text-truncate d-block">
          <i class="bi bi-clock me-1"></i>Hạn dùng: {{ discount.pivot.expiry_at }}
        </div>
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
          <button
            class="btn btn-outline-danger btn-sm float-end"
            :disabled="isExpired(discount)"
          >
            {{ isExpired(discount) ? 'Hết hạn' : 'Dùng Ngay' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Trống -->
  <div v-if="filterUserDiscount.length === 0" class="col-12">
    <div class="text-center text-muted py-5">
      <i class="bi bi-ticket-perforated fs-1 mb-3 d-block"></i>
      <p class="fw-bold mb-1">Kho đang trống</p>
      <p class="mb-0">
        Vui lòng đổi mã bằng
        <img
          src="/img/xubac.png"
          alt="Tcoin"
          style="width: 16px; vertical-align: text-bottom"
        />
        Tcoin
      </p>
    </div>
  </div>
</div>

  </div>
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

const {
  getImageByType,
  formatCurrency,
  userDiscounts,
  getAllDiscount,
  fetchUserDiscounts,
  discounts,
} = Discounts()

const userStore = useUserStore()
const systemDiscounts = ref([])

const voucherCode = ref('')
const activeTab = ref(0)
const tabs = [
  { label: 'Tất cả', count: 0 },
  { label: 'Mã Giảm Món', count: 0 },
  { label: 'Mã FreeShip', count: 0 },
  { label: 'Mã Theo Danh Mục', count: 0 },
  { label: 'Lịch Sử Đổi Mã', count: 0 },
  { label: 'Mã hết hạn', count: 0 },
]

const isExpired = (discount) => {
  const expiry = discount.pivot?.expiry_at
  return expiry && new Date(expiry) < new Date()
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

const filterUserDiscount = computed(() => {
  const currentTab = activeTab.value
  const all = userDiscounts.value || []

  switch (currentTab) {
    case 1:
      return all.filter((d) => d.discount_type === 'salefood' && !isExpired(d))
    case 2:
      return all.filter((d) => d.discount_type === 'freeship' && !isExpired(d))
    case 3:
      return all.filter((d) => d.category_id !== null && !isExpired(d))
    case 4:
      return all.filter((d) => d.pivot?.exchanged_at && !isExpired(d))
    case 5:
      return all.filter((d) => isExpired(d))
    default:
      return all.filter((d) => !isExpired(d))
  }
})

const handleVoucherCode = async () => {
  const code = voucherCode.value.trim().toUpperCase()
  if (!code) return alert('Vui lòng nhập mã voucher!')
  const exists = userDiscounts.value.find((d) => d.code.toUpperCase() === code)
  if (exists) return alert('Bạn đã có mã này rồi!')
  const found = systemDiscounts.value.find((d) => d.code.toUpperCase() === code)
  if (!found) return alert('Không tìm thấy mã trong hệ thống!')

  try {
    const res = await axios.post(
      'http://127.0.0.1:8000/api/redeem-discount',
      { discount_id: found.id },
      {
        headers: { Authorization: `Bearer ${userStore.token}` },
      },
    )
    if (res.data.status) {
      userDiscounts.value.push(res.data.data)
      alert('Đổi mã thành công!')
      voucherCode.value = ''
    } else {
      alert(res.data.message || 'Đổi mã thất bại!')
    }
  } catch (err) {
    console.error(err)
    alert('Lỗi khi gửi yêu cầu đổi mã!')
  }
}

const getVoucherHistoryLabel = (source) => {
  switch (source) {
    case 'discount':
      return 'Bạn đã lưu mã'
    case 'tpoint':
      return 'Bạn đã đổi mã'
    case 'lucky_wheel':
      return 'Nhận từ vòng quay mã'
    default:
      return 'Bạn đã nhận mã'
  }
}

const conditionModalRef = ref(null)
let conditionModalInstance = null
const selectedVoucherCondition = ref('')
const selectedVoucherName = ref('')

const showConditionModal = (condition, name) => {
  selectedVoucherCondition.value = condition
  selectedVoucherName.value = name
  conditionModalInstance?.show()
}
const hideConditionModal = () => {
  conditionModalInstance?.hide()
}

onMounted(async () => {
  await getAllDiscount({ source: 'system' })
  systemDiscounts.value = discounts.value
  await fetchUserDiscounts()
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
#app
  > div
  > div.container.mt-5.fade-in
  > div
  > div.container.col-12.col-md-8.col-lg-9.py-4
  > div.row.g-3 {
  max-height: 80vh;
  overflow-y: auto;
  padding: 6px;
}
</style>
