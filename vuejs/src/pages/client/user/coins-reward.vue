<template>
  <div class="container py-3 position-relative col-12 col-md-8 col-lg-9">
    <!-- Danh mục -->
    <div class="mb-5">
      <div class="d-flex align-items-center mb-3">
        <h5 class="fw-bold mb-0 title-cate-discount">Chọn danh mục bạn quan tâm</h5>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button
          @click="selectAll"
          :class="[
            'voucher-brand-btn d-flex align-items-center',
            selectedCategory === null && !showExpiredOnly ? 'active' : '',
          ]"
        >
          Tất cả
        </button>
        <button
          @click="selectFreeshipOnly"
          :class="[
            'voucher-brand-btn d-flex align-items-center btn-sm',
            showOnlyFreeship ? 'active' : '',
          ]"
        >
          Freeship
        </button>
        <button
          @click="onSelectCategory(category)"
          v-for="category in categories"
          :key="category.id"
          :class="[
            'voucher-brand-btn d-flex align-items-center btn-sm',
            selectedCategory === category.id ? 'active' : '',
          ]"
        >
          <img
               :src="getImageUrl(category.images)"
            class="category-icon me-2"
            :alt="category.name"
          />
          {{ category.name }}
        </button>
        <button
          @click="selectExpiredOnly"
          :class="[
            'voucher-brand-btn d-flex align-items-center btn-sm',
            showExpiredOnly ? 'active' : '',
          ]"
        >
          Mã hết hạn
        </button>
      </div>
    </div>

    <!-- Tìm kiếm -->
    <div>
      <div class="d-flex align-items-center mb-3" style="gap: 10px">
        <input
          v-model="voucherCode"
          type="text"
          class="form-control"
          placeholder="Tìm mã voucher tại đây"
          style="max-width: 400px; font-size: 14px; border-radius: 0.25rem"
        />
      </div>

      <div class="d-flex align-items-center mb-3 mt-4" v-if="!showExpiredOnly">
        <h5 class="fw-bold mb-0 title-discount-hot mb-2">Mã giảm giá nổi bật</h5>
      </div>

      <!-- Danh sách mã -->
      <div class="row g-3">
        <div class="col-md-6" v-for="discount in filteredDiscounts" :key="discount.id">
          <div
            class="d-flex shadow-sm bg-white rounded overflow-hidden position-relative"
            style="min-height: 110px"
            :class="{ 'expired-discount': isExpired(discount) }"
          >
            <!-- Cột trái -->
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

            <!-- Cột phải -->
            <div class="flex-grow-1 px-3 py-2" style="width: 72%">
              <div class="fw-bold mb-1 text-truncate">Mã: {{ discount.name }}</div>
              <div class="text-muted small mb-1 text-truncate d-block">
                <i class="bi bi-clock me-1"></i>{{ discount.custom_condition_note }}
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
                <div class="fw-bold coins-exchange d-flex align-items-center">
                  {{ formatCurrency(discount.cost) }}
                  <img class="coins ms-1" src="/img/xubac.png" alt="coin" />
                </div>
                <button
                  class="btn"
                  @click="redeemDiscount(discount.id, discount.code, discount.cost)"
                  :class="[
                    'btn-sm',
                    isExpired(discount)
                      ? 'btn-secondary text-muted'
                      : hasVoucher(discount.code)
                        ? 'has-voucher'
                        : 'btn-outline-danger',
                  ]"
                  :disabled="isExpired(discount) || hasVoucher(discount.code)"
                >
                  {{
                    isExpired(discount)
                      ? 'Hết hạn'
                      : hasVoucher(discount.code)
                        ? 'Đã đổi'
                        : 'Đổi ngay'
                  }}
                </button>
              </div>
            </div>
            <div
              v-if="isExpired(discount)"
              class="expired-overlay position-absolute top-0 start-0 w-100 h-100"
            ></div>
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
            <h6 class="modal-title fw-bold me-5" id="voucherConditionModalLabel">
              Điều kiện voucher
            </h6>
            <button
              type="button"
              class="btn-close"
              @click="hideConditionModal"
              aria-label="Close"
            ></button>
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
  </div>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import { Modal } from 'bootstrap'
import axios from 'axios'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'

import { useUserStore } from '@/stores/userAuth'
import { FoodList } from '@/stores/food'
import { Discounts } from '@/stores/discount'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
const userStore = useUserStore()
const { getCategory, categories } = FoodList.setup()
const { getImageByType, formatCurrency, fetchUserDiscounts, userDiscounts } = Discounts()

const pointsExchangeDiscounts = ref([])
const selectedCategory = ref(null)
const showExpiredOnly = ref(false)
const showOnlyFreeship = ref(false)
const voucherCode = ref('')

const conditionModalRef = ref(null)
let conditionModalInstance = null
const selectedVoucherCondition = ref('')
const selectedVoucherName = ref('')
const getImageUrl = (image) => `${STORAGE_URL}/img/food/${image}`
const getPointExchangeDiscounts = async () => {
  try {
    const response = await axios.get(`${API_URL}/discounts`, {
      params: { source: 'point_exchange' },
      headers: {
        Authorization: `Bearer ${userStore.token}`,
        category_id: selectedCategory.value || undefined,
      },
    })
    pointsExchangeDiscounts.value = response.data
  } catch (err) {
    toast.error('Không thể tải mã giảm giá đổi xu!')
  }
}

const onSelectCategory = async (category) => {
  selectedCategory.value = category.id
  showExpiredOnly.value = false
  showOnlyFreeship.value = false
  await getPointExchangeDiscounts()
}

const selectAll = () => {
  selectedCategory.value = null
  showExpiredOnly.value = false
  showOnlyFreeship.value = false
  getPointExchangeDiscounts()
}

const selectFreeshipOnly = () => {
  selectedCategory.value = null
  showExpiredOnly.value = false
  showOnlyFreeship.value = true
  getPointExchangeDiscounts()
}

const selectExpiredOnly = () => {
  selectedCategory.value = null
  showExpiredOnly.value = true
  showOnlyFreeship.value = false
  getPointExchangeDiscounts()
}

// Kiểm tra hết hạn
const isExpired = (discount) => {
  return discount.end_date && new Date(discount.end_date) < new Date()
}

// Lọc danh sách mã
const filteredDiscounts = computed(() => {
  const keyword = voucherCode.value.toLowerCase()

  return pointsExchangeDiscounts.value.filter((discount) => {
    const matchCategory = !selectedCategory.value || discount.category_id === selectedCategory.value
    const matchKeyword = !voucherCode.value || discount.name.toLowerCase().includes(keyword)
    const expired = isExpired(discount)
    const matchExpired = showExpiredOnly.value ? expired : !expired
    const matchFreeship = !showOnlyFreeship.value || discount.discount_type === 'freeship'
    return matchCategory && matchKeyword && matchExpired && matchFreeship
  })
})

// Đổi mã
const redeemDiscount = async (discountId, code = '', points = 0) => {
  if (!discountId) return toast.warning('Không tìm thấy mã giảm giá!')

  const confirm = await Swal.fire({
    title: 'Xác nhận đổi mã giảm giá?',
    html: `<p>Dùng <strong>${points} xu</strong> để đổi mã <strong>${code}</strong>.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Đổi ngay',
    cancelButtonText: 'Huỷ',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
  })

  if (!confirm.isConfirmed) return

  try {
    const response = await axios.post(
      `${API_URL}/redeem-discount`,
      { discount_id: discountId },
      {
        headers: {
          Authorization: `Bearer ${userStore.token}`,
          Accept: 'application/json',
        },
        withCredentials: true,
      },
    )

    const isSuccess = response.data?.status?.toString().toLowerCase() === 'success'

    if (isSuccess) {
      await Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Đổi mã thành công!',
        showConfirmButton: false,
        timer: 2000,
      })
      await fetchUserDiscounts()
    } else {
      await Swal.fire({
        icon: 'warning',
        title: 'Không thể đổi mã!',
        text: response.data.message || 'Vui lòng thử lại.',
      })
    }
  } catch (error) {
    console.error(error)
    await Swal.fire({
      icon: 'error',
      title: 'Lỗi hệ thống!',
      text: error.response?.data?.message || 'Đổi mã thất bại!',
    })
  }
}

// Kiểm tra user đã có voucher chưa
const hasVoucher = (code) => {
  return userDiscounts.value.some((v) => v.code === code)
}

// Modal điều kiện voucher
const showConditionModal = (condition, name) => {
  selectedVoucherCondition.value = condition
  selectedVoucherName.value = name
  conditionModalInstance?.show()
}

const hideConditionModal = () => {
  conditionModalInstance?.hide()
}

// Mounted
onMounted(async () => {
  await getPointExchangeDiscounts()
  await fetchUserDiscounts()
  getCategory()

  if (conditionModalRef.value) {
    conditionModalInstance = new Modal(conditionModalRef.value)
  }
})
</script>


<style scoped>
.voucher-brand-btn {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 8px 14px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s ease;
}

.voucher-brand-btn:hover {
  background: #f0f0f0;
}

.voucher-brand-btn.active {
  background-color: #c92c3c;
  color: white;
}

.btn-sm {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}

.btn-sm:hover {
  background-color: #c92c3c;
  color: white;
}

.has-voucher {
  color: #007d00;
  border: 1px solid #007d00;
}

.expired-discount {
  opacity: 0.6;
  pointer-events: none;
  filter: grayscale(0.6);
}

.expired-overlay {
  background-color: rgba(255, 255, 255, 0.05);
  z-index: 2;
}

.coins {
  width: 15px;
  height: 15px;
  margin: 6px 5px 5px 5px;
}

.category-icon {
  width: 20px;
  height: 20px;
  object-fit: contain;
}

.title-cate-discount,
.title-discount-hot {
  color: #c92c3c;
}
#app
  > div
  > div.container.mt-5.fade-in
  > div
  > div.container.py-3.position-relative.col-12.col-md-8.col-lg-9
  > div:nth-child(2)
  > div.row.g-3 {
  max-height: 61vh;
  overflow-y: auto;
  padding: 6px;
}
</style>
