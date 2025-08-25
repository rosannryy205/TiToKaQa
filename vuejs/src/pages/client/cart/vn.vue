<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>

  <div class="container-sm py-4">
    <div class="row gx-5">
      <!-- Customer Info -->
      <div class="col-md-7">
        <div class="p-4 border rounded shadow-sm bg-white">
          <h4 class="mb-4">Thông tin đặt hàng</h4>
          <form @submit.prevent="submitOrder">

            <div class="mb-3">
              <input v-model="form.fullname" type="text" class="form-control-customer" placeholder="Tên của bạn">
            </div>
            <div class="mb-3">
              <input v-model="form.email" type="email" class="form-control-customer" placeholder="Email của bạn">
            </div>
            <div class="mb-3">
              <input v-model="form.phone" type="text" class="form-control-customer" placeholder="Số điện thoại">
            </div>
            <div class="mb-3">
              <input v-model="form.address" type="text" class="form-control-customer" placeholder="Địa chỉ">
            </div>
            <div class="mb-3">
              <textarea v-model="note" class="form-control-customer" rows="3" placeholder="Ghi chú"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <router-link to="/cart" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Quay về giỏ hàng
              </router-link>
              <button type="submit" class="btn btn-check-out"
                :title="!paymentMethod ? 'Vui lòng chọn phương thức thanh toán' : ''">
                Đặt hàng
              </button>

            </div>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-md-5">
        <div class="p-4 border rounded shadow-sm bg-white">
          <h4 class="mb-3">Đơn hàng ({{ totalQuantity }} sản phẩm)</h4>
          <hr />

          <!-- Cart Items -->
          <div class="list-product-scroll mb-3">
            <div v-for="(item, index) in cartItems" :key="index" class="d-flex mb-3">
              <img :src="getImageUrl(item.image)" alt="" class="me-3 rounded" width="80" height="80" />
              <div class="flex-grow-1">
                <strong>{{ item.name }}</strong>
                <div>{{ item.spicyLevel }}</div>
                <div v-if="item.toppings.length" class="text-muted small">
                  <div v-for="(topping, i) in item.toppings" :key="i">
                    {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ
                  </div>
                </div>
                <div v-else class="text-muted small">Không có topping</div>
                <div>Số lượng: {{ item.quantity }}</div>
                <div>Giá: {{ formatNumber(item.price) }} VNĐ</div>
              </div>
              <div class="text-end ms-2">
                <strong>{{ formatNumber(totalPriceItem(item)) }} VNĐ</strong>
              </div>
            </div>
          </div>

          <hr />
          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính</span>{{ formatNumber(totalPrice) }} VNĐ
          </div>
          <div v-if="discountAmount > 0" class="d-flex justify-content-between mb-2">
            <span>Giảm Giá</span> - {{ formatNumber(discountAmount) }} VNĐ
          </div>
          <div style="color: #c92c3c" class="d-flex justify-content-between mb-2 fw-bold">
            <span>Tổng thanh toán:</span>{{ formatNumber(finalTotal) }} VNĐ
          </div>

          <!-- Discount Code Input -->
          <div class="mb-3">
            <div v-if="selectedDiscount" class="text-green-600 mb-2">
              Mã <strong style="color: #c92c3c">{{ selectedDiscount }}</strong> đã được áp dụng ✅.
            </div>
            <label for="discount" class="form-label">Mã giảm giá</label>
            <div class="input-group">
              <input v-model="discountInput" type="text" id="discount" class="form-control"
                placeholder="Nhập mã giảm giá..." />
              <button class="btn btn-outline-primary" @click="handleDiscountInput">Áp dụng</button>
            </div>
          </div>

          <!---->
          <div class="discount-scroll-wrapper">
            <div v-for="discount in discounts" :key="discount.id">
              <div class="shopee-voucher d-flex align-items-center justify-content-between mb-2"
                @click="applyDiscountCode(discount.code)">
                <div class="voucher-left d-flex align-items-center">
                  <div class="voucher-logo d-flex flex-column align-items-center justify-content-center">
                    <div class="logo-text">TITOKAQA</div>
                    <div class="logo-small">Mall</div>
                  </div>
                  <div class="voucher-info ps-3">
                    <div class="voucher-title">{{ discount.name }}</div>
                    <div class="voucher-title">Mã {{ discount.code }}</div>
                    <div class="voucher-time"><i class="fa-regular fa-clock me-1"></i>Hiệu lực sau: 2 ngày</div>
                  </div>
                </div>
                <div class="voucher-right text-end">
                  <div class="voucher-status" :class="{ 'text-success': selectedDiscount === discount.code }">
                    <span v-if="selectedDiscount === discount.code">Đã dùng ✅</span>
                    <span v-else>Dùng ngay</span>
                  </div>
                  <div class="voucher-tag">Mới!</div>
                </div>
              </div>
            </div>
          </div>
          <!-- Payment Methods -->
          <div>
            <h6 class="mb-2">Phương thức thanh toán</h6>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="vnpay" value="Thanh toán VNPAY"
                v-model="paymentMethod">
              <label class="form-check-label d-flex align-items-center" for="vnpay">
                <span class="me-2">Thanh toán qua VNPAY</span>
                <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="momo" value="Thanh toán MOMO"
                v-model="paymentMethod">
              <label class="form-check-label d-flex align-items-center" for="momo">
                <span class="me-2">Thanh toán qua Momo</span>
                <img src="/img/momo.png" height="20" width="20" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="cod" value="Thanh toán COD"
                v-model="paymentMethod">
              <label class="form-check-label d-flex align-items-center" for="cod">
                <span class="me-2">Thanh toán khi nhận hàng (COD)</span>
                <img src="/img/cod.png" height="30" width="30" alt="" />
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// import { User } from '@/stores/user'
import { FoodList } from '@/stores/food'
import { Payment } from '@/stores/payment'
import { Discounts } from '@/stores/discount'
import { onMounted } from 'vue'
import numeral from 'numeral'
import { useRouter } from 'vue-router'

export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },
  setup() {

    const { isLoading } = FoodList.setup()

    const {
      user,
      form,
      note,
      handleSubmit,
      paymentMethod,
      check_out,
      loadCart,
      totalPriceItem,
      totalQuantity,
      submitOrder
    } = Payment.setup()

    // const {
    //   user,
    //   form
    // } = User.setup()

    const {
      route,
      cartItems,
      totalPrice,
      finalTotal,
      discounts,
      discountInput,
      selectedDiscount,
      showMoreDiscounts,
      getAllDiscount,
      handleDiscountInput,
      applyDiscountCode,
      discountAmount
    } = Discounts.setup()


    const updateCartStorage = () => {
      const cartKey = getCartKey()
      localStorage.setItem(cartKey, JSON.stringify(cartItems.value))
    }

    onMounted(() => {
      loadCart()
      // if (user.value && user.value.id) {
      //   form.value.fullname = user.value.fullname || ''
      //   form.value.email = user.value.email || ''
      //   form.value.phone = user.value.phone || ''
      //   form.value.address = user.value.address || ''
      // }
    })

    return {
      user,
      totalPriceItem,
      totalQuantity,
      check_out,
      form,
      handleSubmit,
      submitOrder,
      isLoading,
      paymentMethod,
      note,

      updateCartStorage,
      cartItems,
      totalPrice,
      finalTotal,
      discounts,
      discountInput,
      selectedDiscount,
      showMoreDiscounts,
      getAllDiscount,
      handleDiscountInput,
      applyDiscountCode,
      discountAmount
    }
  }
}
</script>


<style>
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

.text-success {
  color: #28a745;
  font-weight: bold;
  border: solid #28a745 !important;
}
</style>
