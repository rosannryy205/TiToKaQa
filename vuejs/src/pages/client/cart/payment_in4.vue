<template>
  <div class="container-sm py-4">
    <div class="row gx-5">
      <!-- Customer Info -->
      <div class="col-md-7">
        <div class="p-4 border rounded shadow-sm bg-white">
          <h4 class="mb-4">Thông tin đặt hàng</h4>
          <form @submit.prevent="check_out">
            <div class="mb-3">
              <input v-model="guest_name" type="text" class="form-control" placeholder="Tên của bạn">
            </div>
            <div class="mb-3">
              <input v-model="guest_email" type="email" class="form-control" placeholder="Email của bạn">
            </div>
            <div class="mb-3">
              <input v-model="guest_phone" type="text" class="form-control" placeholder="Số điện thoại">
            </div>
            <div class="mb-3">
              <input v-model="guest_address" type="text" class="form-control" placeholder="Địa chỉ">
            </div>
            <div class="mb-3">
              <textarea v-model="note" class="form-control" rows="3" placeholder="Ghi chú"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <router-link to="/cart" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Quay về giỏ hàng
              </router-link>
              <button type="submit" class="btn btn-primary">Đặt hàng</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Payment Info -->
      <div class="col-md-5">
        <div class="p-4 border rounded shadow-sm bg-white">
          <h4 class="mb-3">Đơn hàng ({{ totalQuantity }} sản phẩm)</h4>
          <hr>
          <div class="list-product-scroll mb-3">
          <div v-for="(item, index) in cartItems" :key="index" class="d-flex mb-3">
            <img :src="getImageUrl(item.image)" alt="" class="me-3 rounded" width="80" height="80">
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

          <hr>

          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính</span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Giảm giá</span>
            <strong>0 VNĐ</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Phí vận chuyển</span>
            <strong>0 VNĐ</strong>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-3">
            <span><strong>Tổng cộng</strong></span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>

          <div class="mb-3">
            <label for="discount" class="form-label">Mã giảm giá</label>
            <div class="input-group">
              <input type="text" id="discount" class="form-control" placeholder="Nhập mã giảm giá...">
              <button class="btn btn-outline-primary">Áp dụng</button>
            </div>
          </div>

          <div>
            <h6 class="mb-2">Phương thức thanh toán</h6>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment" id="vnpay">
              <label class="form-check-label d-flex align-items-center" for="vnpay">
                <span class="me-2">Thanh toán qua VNPAY</span>
                <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="">
              </label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment" id="momo">
              <label class="form-check-label d-flex align-items-center" for="momo">
                <span class="me-2">Thanh toán qua Momo</span>
                <img src="/img/momo.png" height="20" width="20" alt="">
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="cod" checked>
              <label class="form-check-label d-flex align-items-center" for="cod">
                <span class="me-2">Thanh toán khi nhận hàng (COD)</span>
                <img src="/img/cod.png" height="30" width="30" alt="">
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
import axios from 'axios'
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
import { computed } from 'vue'
export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0.00')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },
  setup() {
    const router = useRouter()

    const cartItems = ref([])
    const guest_name = ref('')
    const guest_email = ref('')
    const guest_phone = ref('')
    const guest_address = ref('')
    const note = ref('')

    const user= JSON.parse(localStorage.getItem('user')) || {}
    if(user){
        guest_name.value = user.fullname || '',
        guest_email.value = user.email || '',
        guest_phone.value = user.phone || '',
        guest_address.value = user.address || ''
      }

    const check_out = async() => {
      try{
        const orderData = {
          user_id: user ? user.id : null,
          guest_name: guest_name.value,
          guest_email: guest_email.value,
          guest_phone: guest_phone.value,
          guest_address: guest_address.value,
          note: note.value,
          total_price: totalPrice.value,
          order_detail: cartItems.value.map(item => ({
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: 'food',
            toppings: item.toppings.map(t=>({
              food_toppings_id: t.food_toppings_id,
              price: t.price
            }))
          }))
        }

        const response= await axios.post('http://127.0.0.1:8000/api/order',orderData)
        if(response.data.status){
          alert('Đặt hàng thành công')
          localStorage.removeItem('cart');
          router.push('/cart')
        } else {
          alert('Đặt hàng thật bại!')
        }
      } catch(error){
        console.error(error)
        alert('Lỗi khi gửi đơn hàng')
      }
    }


    const loadCart = () => {
      const storedCart = localStorage.getItem('cart')
      if (storedCart) {
        cartItems.value = JSON.parse(storedCart)
      }
    }

    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        const basePrice = Number(item.price) * item.quantity
        const toppingPrice = item.toppings.reduce((tsum, topping) => {
          return tsum + (Number(topping.price) * item.quantity)
        }, 0)
        return sum + basePrice + toppingPrice
      }, 0)
    })


    const totalPriceItem = (item) => {
      const itemPrice = Number(item.price) * item.quantity;
      const toppingPrice = item.toppings.reduce((sum, topping) => {
        return sum + (Number(topping.price) * item.quantity);
      }, 0);
      return itemPrice + toppingPrice;
    };

    const totalQuantity = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    })



    const updateCartStorage = () => {
      localStorage.setItem('cart', JSON.stringify(cartItems.value))
    }




    onMounted(() => {
      loadCart()
    })

    return {
      cartItems,
      totalPrice,
      totalPriceItem,
      totalQuantity,
      guest_name,
      guest_email,
      guest_phone,
      guest_address,
      note,
      check_out,
    }
  }
}
</script>
