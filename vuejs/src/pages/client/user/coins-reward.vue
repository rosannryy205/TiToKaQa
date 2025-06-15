<template>
  <div class="container py-3 position-relative">
    <div class="position-absolute top-0 end-0 mt-3 me-2 d-flex align-items-center user-coins-box">
      <span class="fw-semibold text-dark me-1"> {{ formatCurrency(1500) }}</span>
      <img src="/img/item/coins.gif" class="coins-small" alt="xu" />
    </div>
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
      <p class="text-muted">
        Những voucher hấp dẫn đang được người dùng lựa chọn nhiều nhất.
      </p>

      <div class="row gy-4">
        <div
          class="col-6 col-md-4 col-lg-3"
          v-for="discount in discounts"
          :key="discount.id"
        >
          <div class="voucher-card border-0 shadow-sm">
            <img
              :src="getImageByType(discount.discount_type)"
              class="card-img-top rounded-top"
              :alt="discount.name"
            />
            <div class="card-body">
              <h6
                class="fw-semibold mb-1 text-truncate"
                style="max-width: 100%"
              >
                Mã: {{ discount.name }}
              </h6>
              <p class="text-danger small mb-1">
                Giảm đến
                {{
                  discount.discount_method === 'percent'
                    ? `${discount.discount_value}% (tối đa ${formatCurrency(discount.max_discount_amount)}đ)`
                    : `${formatCurrency(discount.discount_value)}đ`
                }}
              </p>
              <p class="fw-bold mb-2 coins-exchange d-flex align-items-center">
                {{ formatCurrency(discount.cost) }}
                <img class="coins ms-1 align-items-center" src="/img/xubac.png" alt="coin" />
              </p>
              <button class="btn btn-outline-success w-100">
                Đổi ngay
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { FoodList } from '@/stores/food'
import { Discounts } from '@/stores/discount'

const { getCategory, categories } = FoodList.setup()
const { discounts, getAllDiscount } = Discounts()

const getImageByType = (type) => {
  return type === 'freeship'
    ? '/img/freeship-icon.png'
    : '/img/discount-icon.png'
}
const formatCurrency = (num) => {
  if (!num) return '0'
  return Number(num).toLocaleString('vi-VN')
}

onMounted(async () => {
  await getAllDiscount({ source: 'point_exchange' })
  getCategory
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

.voucher-card h6{
  font-size: 14px;
  margin-bottom: 6px;
}
.coins-exchange{
  color: rgb(119, 119, 119) !important;
}
.coins{
  width: 15px !important;
  height: 15px !important;
  margin: 6px 5px 5px 5px !important;
}
.title-cate-discount,
.title-discount-hot{
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

</style>
