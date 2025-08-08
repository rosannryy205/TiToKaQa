<template>
  <div class="flash-sale-page">
    <div class="flash-sale-container">
      <!-- Header -->
      <div class="flash-sale-header">
        <div class="header-left">
          <h1 class="title">🔥 TITOKAQA FLASH SALE</h1>
          <div class="countdown">
            <span class="countdown-label">Kết thúc trong</span>
            <div class="countdown-timer">
              <span class="timer-box">{{ countdown.h }}</span
              >: <span class="timer-box">{{ countdown.m }}</span
              >:
              <span class="timer-box">{{ countdown.s }}</span>
            </div>
          </div>
        </div>
        <a href="#" class="view-all-btn">Xem tất cả ></a>
      </div>

      <!-- Product List -->
      <div class="product-list">
        <div class="product-card" v-for="(food, index) in flashsaleFoods" :key="index"
        :class="{ 'sold-out': food.is_sold_out }">
          <div class="product-image-wrapper">
            <img :src="getImageUrl(food.image)" :alt="food.name" />
            <button class="cart-icon-btn" @click="addToCartFlashSale(food)">
              <i class="bi bi-cart"></i>
            </button>
          </div>
          <div class="product-info">
            <h3 class="product-name text-center">{{ food.name }}</h3>
            <div class="price mb-2">
              <strong>{{ formatCurrency(food.flash_sale_price) }}</strong>
              <div class="original-price-wrapper">
                <del>{{ formatCurrency(food.price) }}</del>
                <span class="product-badge-inline">-{{ food.discount_percent }}%</span>
              </div>
            </div>

            <div class="progress-bar">
              <span class="progress-bar-sold">{{ food.progress_label }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRoute } from 'vue-router'
const flashsaleFoods = ref([])
const countdown = ref({ h: '00', m: '00', s: '00' })
let intervalId = null

const getImageUrl = (image) => `/img/food/${image}`
const formatCurrency = (value) => Number(value).toLocaleString('vi-VN') + 'đ'

const getFlashSaleFoods = async () => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/flash-sale/foods`)
    const foods = (res.data.data || []).map(item => ({
      ...item,
      type: 'Food'
    }))

    flashsaleFoods.value = foods.map((item) => {
      const discount = Math.round(((item.price - item.flash_sale_price) / item.price) * 100)
      const sold = item.flash_sale_sold || 0
      const quantity = item.flash_sale_quantity || 1
      const percent = Math.min(Math.round((sold / quantity) * 100), 100)
      const isSoldOut = sold >= quantity
      const result = {
        ...item,
        discount_percent: discount,
        progress: percent,
        progress_label: percent >= 100 ? 'Đã bán hết' : `Đã bán ${sold}`,
        is_sold_out: isSoldOut,
      }
      console.log('🔥 Flash sale food:', result)
      return result
    })

    startCountdown()
  } catch (error) {
    console.error('❌ Lỗi khi lấy dữ liệu flash sale:', error)
  }
}

const startCountdown = () => {
  if (!flashsaleFoods.value.length) return
  const endTime = new Date(flashsaleFoods.value[0].flash_sale_end)

  clearInterval(intervalId)

  intervalId = setInterval(() => {
    const now = new Date()
    const distance = endTime.getTime() - now.getTime()

    if (distance <= 0) {
      clearInterval(intervalId)
      countdown.value = { h: '00', m: '00', s: '00' }
    } else {
      const hours = Math.floor(distance / (1000 * 60 * 60))
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
      const seconds = Math.floor((distance % (1000 * 60)) / 1000)

      countdown.value = {
        h: String(hours).padStart(2, '0'),
        m: String(minutes).padStart(2, '0'),
        s: String(seconds).padStart(2, '0'),
      }
    }
  }, 1000)
}

const route = useRoute()
const orderId = route.params.orderId

const addToCartFlashSale = (food) => {
  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id || 'guest'
  const cartKey = orderId ? `cart_${userId}_reservation_${orderId}` : `cart_${userId}`

  const cart = JSON.parse(localStorage.getItem(cartKey)) || []
  const now = new Date()
  const flashSaleEnd = new Date(food.flash_sale_end)
  const isFlashSaleTime = now < flashSaleEnd

  const hasFlashSaleItem = cart.some(
    (item) => item.id === food.id && item.is_flash_sale === true
  )
  const isFlashSale = isFlashSaleTime && !hasFlashSaleItem

  const cartItem = {
    id: food.id,
    name: food.name,
    image: food.image,
    price: isFlashSale ? food.flash_sale_price : food.price,
    original_price: food.price,
    toppings: [],
    quantity: 1,
    type: food.type,
    category_id: food.category_id,
    is_flash_sale: isFlashSale,
    flash_sale_end: isFlashSale ? food.flash_sale_end : null,
  }

  const existingItemIndex = cart.findIndex(
    (item) => item.id === cartItem.id && item.is_flash_sale === cartItem.is_flash_sale
  )

  if (existingItemIndex !== -1) {
    cart[existingItemIndex].quantity += 1
  } else {
    cart.push(cartItem)
  }

  localStorage.setItem(cartKey, JSON.stringify(cart))
  if (!isFlashSale) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'warning',
      title: 'Sản phẩm đã vượt số lượng Flash Sale – giá trở về gốc!',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true,
    })
  } else {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã thêm món vào giỏ hàng!',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    })
  }
}


onMounted(() => {
  getFlashSaleFoods()
})

onUnmounted(() => {
  clearInterval(intervalId)
})
</script>

<style scoped>
:root {
  --primary-color: #d9363e;
  --secondary-color: #ff6a00;
  --background-color: #f7f8fa;
  --card-background: #ffffff;
  --text-primary: #212121;
  --text-secondary: #757575;
}

.flash-sale-page {
  background-color: var(--background-color);
  padding: 16px 0 40px;
}

.flash-sale-container {
  max-width: 1200px;
  margin: auto;
  padding: 0 12px;
}

/* Header */
.flash-sale-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.title {
  font-size: 24px;
  color: var(--primary-color);
  margin: 0;
  font-weight: 800;
}

.countdown {
  display: flex;
  align-items: center;
  gap: 8px;
}

.countdown-label {
  font-size: 14px;
  color: var(--text-primary);
}

.countdown-timer {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: bold;
  color: var(--primary-color);
}

.timer-box {
  background: var(--primary-color);
  color: white;
  padding: 4px 6px;
  border-radius: 4px;
  font-size: 14px;
  min-width: 24px;
  text-align: center;
}

.view-all-btn {
  color: var(--primary-color);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  white-space: nowrap;
}
.time-slots-wrapper {
  overflow-x: auto;
  padding-bottom: 10px;
  margin-bottom: 16px;
}
.time-slots-wrapper::-webkit-scrollbar {
  display: none;
}
.time-slots-wrapper {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.time-slots {
  display: flex;
  gap: 12px;
}

.slot {
  flex: 0 0 auto;
  padding: 8px 16px;
  border-radius: 8px;
  text-align: center;
  background-color: var(--card-background);
  cursor: pointer;
  border: 1px solid #e0e0e0;
  transition: all 0.2s ease;
}

.slot.active {
  border-color: var(--primary-color);
  background-color: #fff0f1;
}

.slot-time {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
}

.slot.active .slot-time {
  color: var(--primary-color);
}

.slot-status {
  font-size: 12px;
  color: var(--text-secondary);
}
.slot.active .slot-status {
  color: var(--primary-color);
  font-weight: 500;
}
.product-list {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}

@media (max-width: 1024px) {
  .product-list {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .product-list {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
}
.product-card {
  background: var(--card-background);
  border-radius: 8px;
  overflow: hidden;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
}

.product-image-wrapper {
  position: relative;
  width: 100%;
  padding-top: 100%;
}

.product-image-wrapper img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 8px 12px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.product-name {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
  margin: 0 0 8px 0;
  line-height: 1.4;
  height: 39.2px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.original-price-wrapper {
  display: flex;
  align-items: center;
  gap: 6px;
}

.product-badge-inline {
  color: #c92c3c;
  font-size: 1rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.progress-bar {
  width: 100%;
  height: 18px;
  background-color: #ffe8e9;
  border-radius: 9px;
  position: relative;
  overflow: hidden;
  margin-top: auto;
}

.progress-bar-fill {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  border-radius: 9px;
  background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
}

.progress-bar-sold {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  text-align: center;
  color: #c92c3c;
  font-size: 11px;
  font-weight: bold;
  line-height: 18px;
  text-transform: uppercase;
}
@media (max-width: 768px) {
  .title {
    font-size: 20px;
  }
  .header-left {
    gap: 8px;
    flex-direction: column;
    align-items: flex-start;
  }
}
@media (max-width: 480px) {
  .product-list {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
  .product-info {
    padding: 8px;
  }
  .product-name {
    font-size: 13px;
    height: 36.4px;
  }
  .price strong {
    font-size: 16px;
  }
}
del {
  text-decoration: line-through;
}
.cart-icon-btn {
  position: absolute;
  top: 0px;
  right: 0px;
  background-color: #c92c3c;
  color: white;
  border: none;
  border-radius: 50%;
  width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  cursor: pointer;
  z-index: 1;
  transition: background-color 0.3s ease;
}

.cart-icon-btn:hover {
  background-color: #a82330;
}

.product-image-wrapper {
  position: relative;
}
/**disable card */
.product-card.sold-out {
  opacity: 0.6;
  pointer-events: none;
  position: relative;
}

.product-card.sold-out::after {
  content: 'Đã bán hết';
  position: absolute;
  top: 45%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 6px 12px;
  font-size: 16px;
  border-radius: 4px;
}

</style>
