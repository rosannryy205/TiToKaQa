<template>
  <div class="container-sm">
    <div class="row row-custom">
      <div class="col-7 col-in4-customer">
        <div class="box-in4-customer">
          <div class="title-in4">
            <h3>Thông tin đặt hàng</h3>
          </div>
          <div class="body-in4-customer">
            <form @submit.prevent="check_out">
              <div class="input-in4-customer">
                <input type="text" placeholder="Tên của bạn">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Số điện thoại">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Địa chỉ">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Tỉnh thành">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Quận huyện">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Phường xã">
              </div>
              <div class="input-in4-customer">
                <textarea name="" id="" placeholder="Ghi chú"></textarea>
              </div>
              <div class="btn-complete">
                <router-link to="/cart"><span><i class="bi bi-chevron-left"></i>Quay về trang giỏ hàng</span></router-link>
                <button class="btn btn-complete-order">Đặt hàng</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col col-in4-payment">
        <div class="box-in4-payment">
          <div class="title-in4-payment ">
            <h3>Đơn hàng ({{ totalQuantity }} sản phẩm)</h3>
          </div>
          <hr>
          <div class="list-product">
            <div class="item" v-for="(item, index) in cartItems" :key="index">
              <div class="left-item">
                <img :src="getImageUrl(item.image)" height="100" width="100" alt="">
                <div class="in4-item">
                  <span>{{ item.name }}</span>
                  <span>{{ item.spicyLevel }}</span>
                  <p class="text-muted mb-2" v-if="item.toppings && item.toppings.length">
                        <span v-for="(topping, index) in item.toppings" :key="index">
                          {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ <br>
                        </span>
                  </p>
                  <p v-else>Không cócó</p>
                  <span>Số lượng: {{ item.quantity }}</span>
                  <span>Giá: {{ formatNumber(item.price) }} VNĐ</span>
                </div>
              </div>
              <div class="price-item">
                {{ formatNumber(totalPriceItem(item)) }} VNĐ
              </div>
            </div>
          </div>
          <hr>
          <div class="in4-ship">
            <div class="content-ship">
              <span>Tạm tính</span>
              <span><strong>{{ formatNumber(totalPrice) }} VNĐ</strong></span>
            </div>
            <div class="content-ship">
              <span>Giảm giá</span>
              <span><strong>0 VND</strong></span>
            </div>
            <div class="content-ship">
              <span>Phí vận chuyển</span>
              <span><strong>0 VND</strong></span>
            </div>
            <hr>
            <div class="content-ship">
              <span>Tổng cộng</span>
              <span><strong>{{ formatNumber(totalPrice) }} VNĐ</strong></span>
            </div>
          </div>
          <div class="discount-code">
            <label for="discount">Mã giảm giá:</label>
            <div class="input-group">
              <input type="text" id="discount" placeholder="Nhập mã giảm giá...">
              <button>Áp dụng</button>
            </div>
          </div>
          <hr>
          <div class="title-in4">
            <h5>Phương thức thanh toán</h5>
          </div>
          <div class="payment-method">
            <div class="content"><span class="left-content"><input type="radio"> Thanh toán qua VNPAY</span><img
                src="/img/Logo-VNPAY-QR-1 (1).png" height="20px" width="60px" alt=""></div>
            <div class="content"><span class="left-content"><input type="radio"> Thanh toán qua Momo</span><img
                src="/img/momo.png" height="20px" width="20px" alt=""></div>
            <div class="content"><span class="left-content"><input type="radio" checked> Thanh toán khi nhận hàng
                (COD)</span><img src="/img/cod.png" height="30px" width="30px" alt=""></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
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
    const cartItems = ref([])

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
      totalQuantity
    }
  }
}
</script>
