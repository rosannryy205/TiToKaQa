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
              <input
                v-model="form.fullname"
                type="text"
                class="form-control"
                placeholder="Tên của bạn"
              />
            </div>
            <div class="mb-3">
              <input
                v-model="form.email"
                type="email"
                class="form-control"
                placeholder="Email của bạn"
              />
            </div>
            <div class="mb-3">
              <input
                v-model="form.phone"
                type="text"
                class="form-control"
                placeholder="Số điện thoại"
              />
            </div>
            <div class="mb-3">
              <input
                v-model="form.address"
                type="text"
                class="form-control"
                placeholder="Địa chỉ"
              />
            </div>
            <div class="mb-3">
              <textarea
                v-model="note"
                class="form-control"
                rows="3"
                placeholder="Ghi chú"
              ></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <router-link to="/cart" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Quay về giỏ hàng
              </router-link>
              <button type="submit" class="btn btn-check-out">Đặt hàng</button>
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
              <img
                :src="getImageUrl(item.image)"
                alt=""
                class="me-3 rounded"
                width="80"
                height="80"
              />
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
            <label for="discount" class="form-label">Mã giảm giá</label>
            <div class="input-group">
              <input
                v-model="discountInput"
                type="text"
                id="discount"
                class="form-control"
                placeholder="Nhập mã giảm giá..."
              />
              <button class="btn btn-outline-primary" @click="handleDiscountInput">Áp dụng</button>
            </div>
            <div v-if="selectedDiscount" class="text-green-600 mb-2">
              Mã <strong style="color: #c92c3c">{{ selectedDiscount }}</strong> đã được áp dụng ✅.
            </div>
          </div>

          <!---->
          <div class="discount-scroll-wrapper">
          <div v-for="discount in discounts" :key="discount.id">
            <div class="shopee-voucher d-flex align-items-center justify-content-between mb-2" @click="applyDiscountCode(discount.code)">
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
    <div
  class="voucher-status"
  :class="{ 'text-success': selectedDiscount === discount.code }"
>
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
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment" id="vnpay" />
              <label class="form-check-label d-flex align-items-center" for="vnpay">
                <span class="me-2">Thanh toán qua VNPAY</span>
                <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="" />
              </label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment" id="momo" />
              <label class="form-check-label d-flex align-items-center" for="momo">
                <span class="me-2">Thanh toán qua Momo</span>
                <img src="/img/momo.png" height="20" width="20" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="cod" checked />
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
import { useRouter } from 'vue-router'
import { User } from '@/stores/user'
import { FoodList } from '@/stores/food'
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import numeral from 'numeral'

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
    const router = useRouter()
    const user = JSON.parse(localStorage.getItem('user')) || {}

    const cartItems = ref([])
    const note = ref('')
    const { form } = User.setup()
    const { isLoading } = FoodList.setup()

    const loading = ref(false)
    const discounts = ref([])
    const discountInput = ref('')
    const moreDiscounts = ['HELLO50', 'NEWUSER', 'BUY2GET1', 'FLASH15', 'WELCOME30']
    const selectedDiscount = ref('')
    const showMoreDiscounts = ref(false)

    const getAllDiscount = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/discounts')
        discounts.value = res.data
      } catch (err) {
        console.error(err)
      }
    }

    const handleDiscountInput = () => {
      const code = discountInput.value.trim().toUpperCase()
      const discount = discounts.value.find((d) => d.code === code)
      if (discount) {
        applyDiscountCode(code)
        discountInput.value = ''
      } else {
        alert('Mã giảm giá không hợp lệ')
      }
    }
    const applyDiscountCode = (code) => {
      if (selectedDiscount.value === code) return
      selectedDiscount.value = code
      console.log(selectedDiscount.value)
      showMoreDiscounts.value = false
    }

    const discountAmount = computed(() => {
      const discount = discounts.value.find((d) => d.code === selectedDiscount.value)
      if (!discount) return 0

      if (discount.discount_method === 'percent') {
        return (totalPrice.value * discount.discount_value) / 100
      }
      if (discount.discount_method === 'fixed') {
        return discount.discount_value
      }

      return 0
    })

    const finalTotal = computed(() => {
      return Math.max(totalPrice.value - discountAmount.value, 0)
    })

    const loadCart = () => {
      const userId = user?.id || 'guest'
      const cartKey = `cart_${userId}`
      const storedCart = localStorage.getItem(cartKey)
      cartItems.value = storedCart ? JSON.parse(storedCart) : []
    }

    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        const base = item.price * item.quantity
        const toppings = item.toppings.reduce((tsum, t) => tsum + t.price * item.quantity, 0)
        return sum + base + toppings
      }, 0)
    })

    const totalPriceItem = (item) => {
      const itemPrice = item.price * item.quantity
      const toppingPrice = item.toppings.reduce((sum, t) => sum + t.price * item.quantity, 0)
      return itemPrice + toppingPrice
    }

    const totalQuantity = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    })

    const check_out = async () => {
      try {
        const orderData = {
          user_id: user.id || null,
          guest_name: form.value.fullname,
          guest_email: form.value.email,
          guest_phone: form.value.phone,
          guest_address: form.value.address,
          note: note.value,
          total_price: totalPrice.value,
          order_detail: cartItems.value.map((item) => ({
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: 'food',
            toppings: item.toppings.map((t) => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price,
            })),
          })),
        }

        const res = await axios.post('http://127.0.0.1:8000/api/order', orderData)
        if (res.data.status) {
          alert('Đặt hàng thành công')
          localStorage.removeItem(`cart_${user?.id || 'guest'}`)
          router.push('/cart')
        } else {
          alert('Đặt hàng thất bại!')
        }
      } catch (err) {
        console.error(err)
        alert('Lỗi khi gửi đơn hàng')
      }
    }

    const submitOrder = async () => {
      loading.value = true
      try {
        await check_out()
      } catch (error) {
        console.error('Lỗi khi đặt hàng:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      getAllDiscount()
      loadCart()
    })

    return {
      cartItems,
      totalPrice,
      totalPriceItem,
      totalQuantity,
      note,
      form,
      isLoading,
      loading,
      submitOrder,
      discounts,
      applyDiscountCode,
      moreDiscounts,
      showMoreDiscounts,
      selectedDiscount,
      finalTotal,
      discountAmount,
      discountInput,
      handleDiscountInput,
    }
  },
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
