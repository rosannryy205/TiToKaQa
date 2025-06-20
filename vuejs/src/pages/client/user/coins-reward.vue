<template>
  <div class="container py-3 position-relative col-12 col-md-8 col-lg-9">
    <!-- Danh mục -->
    <div class="mb-5 pt-5">
      <div class="d-flex align-items-center mb-3">
        <h5 class="fw-bold mb-0 title-cate-discount">Chọn danh mục bạn quan tâm</h5>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button
          @click="onSelectCategory({ id: null })"
          :class="[
            'voucher-brand-btn d-flex align-items-center',
            selectedCategory === null ? 'active' : '',
          ]"
        >
          Tất cả
        </button>
        <button
          @click="onSelectCategory(category)"
          v-for="category in categories"
          :key="category.id"
          :class="[
            'voucher-brand-btn d-flex align-items-center',
            selectedCategory === category.id ? 'active' : '',
          ]"
        >
          <img
            :src="`/img/food/imgmenu/${category.images}`"
            class="category-icon me-2"
            :alt="category.name"
          />
          {{ category.name }}
        </button>
      </div>
    </div>
    <div>
      <div class="d-flex align-items-center mb-2">
        <h5 class="fw-bold mb-0 title-discount-hot">Mã giảm giá nổi bật</h5>
      </div>
      <div class="row g-3">
        <div class="col-md-6" v-for="discount in filteredDiscounts" :key="discount.id">
          <div class="d-flex shadow-sm bg-white rounded overflow-hidden" style="min-height: 110px">
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
            <div class="flex-grow-1 px-3 py-2" style="width: 72%">
              <div class="fw-bold mb-1 text-truncate">Mã: {{ discount.name }}</div>
              <div
                class="text-muted small mb-1 text-truncate d-block"
                style="max-width: 100%; overflow: hidden"
              >
                <i class="bi bi-clock me-1"></i>{{ discount.custom_condition_note }}
              </div>

              <div class="text-muted small mb-1 text-truncate" style="max-width: 100%">
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
                  :class="
                    hasVoucher(discount.code) ? 'btn-sm has-voucher' : 'btn-sm btn-outline-danger'
                  "
                  :disabled="hasVoucher(discount.code)"
                >
                  {{ hasVoucher(discount.code) ? 'Đã đổi' : 'Đổi ngay' }}
                </button>
              </div>
            </div>
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
import { onMounted, ref, watch, computed } from 'vue'
import { Modal } from 'bootstrap'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import { useUserStore } from '@/stores/userAuth'
import { FoodList } from '@/stores/food'
import Swal from 'sweetalert2'
import { Discounts } from '@/stores/discount'
//=======================
// State & Store
//=======================
const userStore = useUserStore()
const token = userStore.token

const { getCategory, categories } = FoodList.setup()
const {
    getImageByType,
    formatCurrency  
    } = Discounts()
//=======================
// Danh mục & Lọc mã giảm giá theo danh mục
//=======================
const selectedCategory = ref(null)

const onSelectCategory = async (category) => {
  selectedCategory.value = category.id
  await getPointExchangeDiscounts()
}

const filteredDiscounts = computed(() => {
  if (!selectedCategory.value) return pointsExchangeDiscounts.value
  return pointsExchangeDiscounts.value.filter(
    (discount) => discount.category_id === selectedCategory.value,
  )
})

//=======================
// Danh sách mã giảm giá đổi xu
//=======================
const pointsExchangeDiscounts = ref([])

const getPointExchangeDiscounts = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/discounts', {
      params: { source: 'point_exchange' },
      headers: {
        Authorization: `Bearer ${token}`,
        category_id: selectedCategory.value || undefined,
      },
    })
    pointsExchangeDiscounts.value = response.data
  } catch (err) {
    toast.error('Không thể tải mã giảm giá đổi xu!', err)
  }
}

//=======================
// Modal điều kiện voucher
//=======================
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
//=======================
// redeemDiscount - Đổi mã giảm giá bằng xu
//=======================
const redeemDiscount = async (discountId, code = '', points = 0) => {
  if (!discountId) {
    toast.warning('Không tìm thấy mã giảm giá!')
    return
  }

  const confirmResult = await Swal.fire({
    title: 'Xác nhận đổi mã giảm giá?',
    html: `<p>Bạn sẽ dùng <strong>${points} xu</strong> để đổi mã <strong>${code}</strong>.</p><p>Hành động không thể hoàn tác!</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Đổi ngay',
    cancelButtonText: 'Huỷ',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    reverseButtons: true,
    focusCancel: true,
  })

  if (!confirmResult.isConfirmed) return

  try {
    const token = localStorage.getItem('token')

    const response = await axios.post(
      'http://127.0.0.1:8000/api/redeem-discount',
      { discount_id: discountId },
      {
        headers: {
          Authorization: `Bearer ${token}`,
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
        timerProgressBar: true,
      })
      await fetchUserVouchers()
    } else {
      await Swal.fire({
        icon: 'warning',
        title: 'Không thể đổi mã!',
        text: response.data.message || 'Vui lòng thử lại sau.',
        confirmButtonColor: '#e3342f',
      })
    }
  } catch (error) {
    console.error(error)

    const errorMessage = error.response?.data?.message || 'Đổi mã thất bại, vui lòng thử lại sau!'

    await Swal.fire({
      icon: 'error',
      title: 'Lỗi hệ thống!',
      text: errorMessage,
      confirmButtonColor: '#e3342f',
    })
  }
}

//=======================
// userVouchers - Kiểm tra mã đã đổi
//=======================
const userVouchers = ref([])

const fetchUserVouchers = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/user-vouchers', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    userVouchers.value = response.data.map((v) => v.code)
  } catch (err) {
    toast.error('Không thể tải kho voucher của bạn!', err)
  }
}

const hasVoucher = (code) => {
  return userVouchers.value.includes(code)
}

//=======================
// onMounted
//=======================
onMounted(async () => {
  await getPointExchangeDiscounts()
  await fetchUserVouchers()
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
  .voucher-brand-btn {
    font-size: 13px;
    padding: 6px 10px;
  }

  .category-icon {
    width: 18px;
    height: 18px;
  }

  .voucher-card img {
    height: 120px;
  }
}
.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ddd;
}

.coins-small {
  width: 24px;
  height: 24px;
}

.user-coins-box {
  background: #fff;
  padding: 6px 12px;
  border-radius: 999px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1;
}

@media (max-width: 576px) {
  .user-avatar {
    width: 28px;
    height: 28px;
  }

  .coins-small {
    width: 20px;
    height: 20px;
  }

  .user-coins-box {
    font-size: 14px;
    padding: 5px 10px;
  }
}
.btn-sm {
  color: #c92c3c;
  border: 1px solid #c92c3c;
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
</style>
