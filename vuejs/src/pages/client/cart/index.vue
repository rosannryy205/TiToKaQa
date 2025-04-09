<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

onMounted(() => {
  const token = localStorage.getItem('token');
  if (!token) {
    alert('Bạn cần đăng nhập để vào giỏ hàng!');
    router.push('/home'); // chuyển hướng bằng Vue Router
  }
});
</script>

<template>
  <div class="container-sm">
    <span>Trang chủ / Giỏ hàng</span>
    <h3 class="mb-4">Giỏ hàng của bạn</h3>
    <div class="row">
      <!-- Danh sách sản phẩm -->
      <div class="col-12 col-lg-8 mb-4">
        <!-- Sản phẩm -->
        <div class="card mb-3" v-for="(item, index) in cartItems" :key="index">
          <div class="card-body d-flex align-items-center flex-wrap">
            <i class="bi bi-x-circle me-3 mb-2" @click="removeItem(index)"></i>
            <img :src="getImageUrl(item.image)" class="cart-img me-3 mb-2" alt="Mì kim chi Nha Trang" />
            <div class="flex-grow-1 mb-2">
              <h5 class="mb-1 product-title"><strong>{{ item.name }}</strong></h5>
              <p class="text-muted mb-2">{{ item.spicyLevel }}</p>
              <p class="text-muted mb-2">
                Topping:
                <span v-if="item.toppings && item.toppings.length">
                  <ul>
                    <li v-for="(topping, index) in item.toppings" :key="index">
                      {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ
                    </li>
                  </ul>
                </span>
                <span v-else>Không có</span>
              </p>
              <p class="text-muted mb-2">Số lượng: {{ item.quantity }}</p>
              <p class="mb-0 "><strong>Giá:</strong>{{ formatNumber(item.price) }} VNĐ</p>
            </div>
            <div class="text-center me-3 mb-2">
              <div class="qty-control border rounded px-2 py-1">
                <button class="btn btn-sm btn-outline-secondary" @click="decreaseQuantity(index)">-</button>
                <span>{{ item.quantity }}</span>
                <button class="btn btn-sm btn-outline-secondary" @click="increaseQuantity(index)">+</button>
              </div>
            </div>
            <div class="mb-2 price">
              <strong>{{ formatNumber(totalPriceItem(item)) }} VNĐ VNĐ</strong>
            </div>
          </div>
        </div>

      </div>

      <!-- Thông tin thanh toán -->
      <div class="col-12 col-lg-4">
        <div class="payment-box">
          <h5 class="mb-3">Thông tin thanh toán</h5>
          <div class="d-flex justify-content-between">
            <span>Giá sản phẩm</span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span>Vận chuyển</span>
            <span>Tính khi thanh toán</span>
          </div>
          <hr />
          <div class="d-flex justify-content-between">
            <span><strong>Tổng tiền thanh toán</strong></span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <router-link to="/payment_if">
            <button class="btn btn-checkout w-100 mt-4">Thanh toán ngay</button>
          </router-link>

          <div class="mt-4 d-flex align-items-center flex-wrap">
            <i class="bi bi-telephone-fill me-2 fs-4"></i>
            <div>
              <small>Hotline hỗ trợ (8h – 22h)</small><br />
              <strong class="text-danger">09123456789</strong>
            </div>
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
        const basePrice = item.price * item.quantity
        const toppingPrice = item.toppings.reduce((tsum, topping) => tsum + (topping.price * topping.quantity),0)
        return sum + (basePrice + toppingPrice)
      }, 0)
    })

    const totalPriceItem = (item) => {
      const itemPrice = item.price * item.quantity;
      const toppingPrice = item.toppings.reduce((sum, topping) => sum + (topping.price * item.quantity), 0);
      return itemPrice + toppingPrice;
    };


    const updateCartStorage = () => {
      localStorage.setItem('cart', JSON.stringify(cartItems.value))
    }

    const decreaseQuantity = (index) => {
      if (cartItems.value[index].quantity > 1) {
        cartItems.value[index].quantity--
        updateCartStorage()
      }
    }

    const increaseQuantity = (index) => {
      cartItems.value[index].quantity++
      updateCartStorage()
    }

    const removeItem = (index) => {
      cartItems.value.splice(index, 1)
      localStorage.setItem('cart', JSON.stringify(cartItems.value))
    }


    onMounted(() => {
      loadCart()
    })

    return {
      cartItems,
      totalPrice,
      increaseQuantity,
      decreaseQuantity,
      removeItem,
      totalPriceItem
    }
  }
}
</script>

