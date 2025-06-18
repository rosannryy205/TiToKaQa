<template>
  <div class="container py-3 position-relative">
    <!-- Xu người dùng -->
    <div class="position-absolute top-0 end-0 mt-3 me-2 d-flex align-items-center user-coins-box">
      <span class="fw-semibold text-dark me-1">{{ formatCurrency(1500) }}</span>
      <img class="coins ms-1" src="/img/xubac.png" alt="coin" />
    </div>

    <!-- Danh mục -->
    <div class="mb-5 pt-5">
      <div class="d-flex align-items-center mb-3">
        <h5 class="fw-bold mb-0 title-cate-discount">Chọn danh mục bạn quan tâm</h5>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button
          v-for="category in categories"
          :key="category.id"
          class="voucher-brand-btn d-flex align-items-center"
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

    <!-- Mã giảm giá -->
    <div>
      <div class="d-flex align-items-center mb-2">
        <h5 class="fw-bold mb-0 title-discount-hot">Mã giảm giá nổi bật</h5>
      </div>
      <p class="text-muted">Những voucher hấp dẫn đang được người dùng lựa chọn nhiều nhất.</p>

      <div class="row g-3">
        <div class="col-md-6" v-for="discount in discounts" :key="discount.id">
          <div class="d-flex shadow-sm bg-white rounded overflow-hidden" style="min-height: 110px">
            <!-- Cột trái -->
            <div
              class="text-white d-flex flex-column justify-content-center align-items-center"
              :style="`background-color: ${discount.discount_type === 'freeship' ? '#00bfa5' : '#f44336'}; width: 30%`"
            >
              <img :src="getImageByType(discount.discount_type)" alt="icon" style="width: 60px" />
              <div class="fw-bold small mt-2 text-center">
                {{ discount.discount_type === 'freeship' ? 'FREESHIP' : 'GIẢM GIÁ' }}
              </div>
            </div>
            <!-- Cột phải -->
            <div class="flex-grow-1 px-3 py-2" style="width: 70%">
              <div class="fw-bold mb-1 text-truncate">Mã: {{ discount.name }}</div>
              <div class="text-muted small mb-1">
                Giảm:
                {{
                  discount.discount_method === 'percent'
                    ? `${discount.discount_value}% (tối đa ${formatCurrency(discount.max_discount_amount)}đ)`
                    : `${formatCurrency(discount.discount_value)}đ`
                }}
              </div>
              <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                <div>
                  <i class="bi bi-clock me-1"></i>
                  {{ discount.expiry || 'Hiệu lực đến cuối tháng' }}
                </div>
                <a
                  href="#"
                  class="text-primary"
                  @click.prevent="showConditionModal(discount.condition)"
                  >Điều kiện</a
                >
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bold coins-exchange d-flex align-items-center">
                  {{ formatCurrency(discount.cost) }}
                  <img class="coins ms-1" src="/img/xubac.png" alt="coin" />
                </div>
                <button class="btn btn-sm">Đổi ngay</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!---->
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
          <div class="modal-header py-2">
            <h6 class="modal-title fw-bold" id="voucherConditionModalLabel">Điều kiện voucher</h6>
            <button
              type="button"
              class="btn-close"
              @click="hideConditionModal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body py-3">
            <p class="mb-0 small text-muted">
              {{ selectedVoucherCondition || 'Không có điều kiện cụ thể.' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Modal } from 'bootstrap'
import { FoodList } from '@/stores/food'
import { Discounts } from '@/stores/discount'

const { getCategory, categories } = FoodList.setup()
const { discounts, getAllDiscount } = Discounts()

const getImageByType = (type) => {
  return type === 'freeship' ? '/img/freeship-icon.png' : '/img/discount-icon.png'
}
const formatCurrency = (num) => {
  if (!num) return '0'
  return Number(num).toLocaleString('vi-VN')
}

// Modal logic
const conditionModalRef = ref(null)
let conditionModalInstance = null
const selectedVoucherCondition = ref('')

const showConditionModal = (condition) => {
  selectedVoucherCondition.value = condition
  conditionModalInstance?.show()
}
const hideConditionModal = () => {
  conditionModalInstance?.hide()
}

onMounted(async () => {
  await getAllDiscount({ source: 'point_exchange' })
  getCategory
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
.btn-sm:hover {
  background-color: #c92c3c;
  color: white;
}
</style>
