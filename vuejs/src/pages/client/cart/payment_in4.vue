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
              <select v-model="selectedProvince" @change="onProvinceChange" class="form-control-customer">
                <option :value="null" disabled selected> Chọn tỉnh / thành phố</option>
                <option v-for="province in provinces" :key="province.code" :value="province"> {{ province.name }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <select v-model="selectedDistrict" @change="onDistrictChange" class="form-control-customer">
                <option :value="null" disabled selected>Chọn quận / huyện</option>
                <option v-for="district in districts" :key="district.code" :value="district">{{ district.name }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <select v-model="selectedWard" class="form-control-customer">
                <option :value="null" disabled selected>Chọn xã / phường</option>
                <option v-for="ward in wards" :key="ward.code" :value="ward">{{ ward.name }}</option>
              </select>
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
              <img :src="getImageUrl(item.image)" alt="" class="me-3 rounded" width="80" height="80" />
              <div class="flex-grow-1">
                <strong>{{ item.name }}</strong>
                <div>Loại: {{ item.type }}</div>
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
import { FoodList } from '@/stores/food'
// import { Payment } from '@/stores/payment'
import { Discounts } from '@/stores/discount'
import { onMounted } from 'vue'
import numeral from 'numeral'
import { ref } from 'vue'
import axios from 'axios'
import { User } from '@/stores/user'


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


    const selectedProvince = ref(null)
    const selectedDistrict = ref(null)
    const selectedWard = ref(null)

    const provinces = ref([])
    const districts = ref([])
    const wards = ref([])


    const user1 = ref(null)
 
    const note = ref('')
    const {
      user,
      form
    } = User.setup()

    const {
      cartKey,
      cartItems,
      discounts,
      discountInput,
      selectedDiscount,
      discountId,
      totalPrice,
      getAllDiscount,
      discountInputId,
      showMoreDiscounts,
      applyDiscountCode,
      handleDiscountInput,
      finalTotal,
      discountAmount,
      totalQuantity,
      totalPriceItem,
      loadCart,
    } = Discounts()

    const { isLoading } = FoodList.setup()

    // const updateCartStorage = () => {
    //   const cartKey = getCartKey()
    //   localStorage.setItem(cartKey, JSON.stringify(cartItems.value))
    // }



    

    



    const getProvinces = async () => {
      try {
        const res = await axios.get(`https://provinces.open-api.vn/api/?depth=1`)
        provinces.value = res.data
      } catch (error) {
        console.error('Lỗi lấy tỉnh thành: ', error)
      }
    }

    const onProvinceChange = async () => {
      selectedDistrict.value = null
      selectedWard.value = null
      districts.value = []
      wards.value = []

      if (selectedProvince.value) {
        try {
          const res = await axios.get(`https://provinces.open-api.vn/api/p/${selectedProvince.value.code}?depth=2`)
          districts.value = res.data.districts
        } catch (error) {
          console.error('Lỗi khi lấy quận/huyện:', error)
        }
      }
    }
    const onDistrictChange = async () => {
      selectedWard.value = null
      wards.value = []

      if (selectedDistrict.value) {
        try {
          const res = await axios.get(`https://provinces.open-api.vn/api/d/${selectedDistrict.value.code}?depth=2`)
          wards.value = res.data.wards
        } catch (error) {
          console.error('Lỗi khi lấy xã/phường:', error)
        }
      }
    }
    // const finalTotal = computed(() => {
    //   return Math.max(totalPrice.value - discountAmount.value, 0)
    // })

    const paymentMethod = ref('')

    const check_out = async () => {
      try {
        if (!paymentMethod.value) {
          alert('Vui lòng chọn phương thức thanh toán!')
          return
        }
        const fullAddress = `${form.value.address}, ${selectedWard.value?.name || ''}, ${selectedDistrict.value?.name || ''}, ${selectedProvince.value?.name || ''}`;
        const orderData = {
          user_id: user1.value ? user1.value.id : null,
          guest_name: form.value.fullname,
          guest_email: form.value.email,
          guest_phone: form.value.phone,
          guest_address: fullAddress,
          note: note.value || '',
          total_price: totalPrice.value,
          money_reduce: discountAmount.value,
          final_price: finalTotal.value,
          discount_id: discountId.value || null,
          order_detail: cartItems.value.map(item => ({
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: item.type,
            toppings: item.toppings.map(t => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price
            }))
          }))
        }

        const response = await axios.post('http://127.0.0.1:8000/api/order', orderData)

        if (response && response.data) {
          const { status, order_id } = response.data;
          if (!status || !order_id) {
            alert('Đặt hàng thất bại!');
            return;
          }
          localStorage.setItem('order_id', order_id);
        } else {
          alert('Không nhận được dữ liệu từ server.');
          return;
        }

        if (paymentMethod.value === 'Thanh toán VNPAY' || paymentMethod.value === 'Thanh toán MOMO') {
          const paymentRes = await axios.post('http://127.0.0.1:8000/api/payment', {
            order_id: localStorage.getItem('order_id'),
            amount: finalTotal.value,
          })
          if (paymentRes.data.payment_url) {
            localStorage.setItem('payment_method', paymentMethod.value)
            localStorage.removeItem(cartKey)
            window.location.href = paymentRes.data.payment_url
          } else {
            alert('Không tạo được link thanh toán.')
          }
          return
        }
        if (paymentMethod.value === 'Thanh toán COD') {
          await new Promise(resolve => setTimeout(resolve, 300))
          await axios.post('http://127.0.0.1:8000/api/vnpay-return', {
            order_id: localStorage.getItem('order_id'),
            amount_paid: finalTotal.value,
            payment_method: 'Thanh toán COD',
            payment_status: 'Chưa thanh toán',
            payment_type: 'Thanh toán toàn bộ'
          })
          alert('Đặt hàng thành công!')
          localStorage.setItem('payment_method', paymentMethod.value)
          // localStorage.removeItem(cartKey)
          // router.push('/payment-result')
        }

      } catch (error) {
        console.error('Lỗi xảy ra:', error.message);
        alert('Lỗi khi gửi đơn hàng. Vui lòng thử lại!');
      }
    }

    const submitOrder = async () => {
      isLoading.value = true
      try {
        console.log('✅ form gửi đi:', form.value)
        await check_out()
        console.log('✅ check_out đã được gọi xong')
      } catch (error) {
        console.error('❌ Lỗi khi gọi check_out:', error)
      } finally {
        isLoading.value = false
      }
    }

    onMounted(() => {
      getProvinces()
      loadCart()
      
    })

    return {
      user,
      totalPriceItem,
      totalQuantity,
      check_out,
      form,
      // handleSubmit,
      submitOrder,
      isLoading,
      paymentMethod,
      note,

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
      discountAmount,
      discountInputId,

      provinces,
      districts,
      wards,
      selectedProvince,
      selectedDistrict,
      selectedWard,
      onDistrictChange,
      onProvinceChange,
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
