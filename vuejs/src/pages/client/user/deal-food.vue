<template>
  <div class="container py-3 position-relative col-12 col-md-8 col-lg-9">
    <h4 class="fw-bold mb-3">Deal món đã nhận</h4>
    <div class="d-flex border-bottom mb-3" style="gap: 20px; font-size: 14px">
      <div
        v-for="(tab, index) in tabs"
        :key="index"
        @click="activeTab = index"
        class="pb-2 position-relative"
        :class="{ 'fw-bold text-danger': activeTab === index, 'text-muted': activeTab !== index }"
        style="cursor: pointer"
      >
        {{ tab.label }}
        <span v-if="tab.count" class="text-secondary">({{ tab.count }})</span>
        <span
          v-if="activeTab === index"
          class="position-absolute start-0 bottom-0 w-100"
          style="height: 2px; background-color: #d9363e"
        ></span>
      </div>
    </div>
    <div class="row g-3">
      <div
        class="col-md-6"
        v-for="(deal, idx) in filteredDealsFood"
        :key="deal.id || idx"
      >
        <div class="deal-card d-flex align-items-center rounded p-3 w-100 shadow-sm">
          <div class="me-3 d-flex align-items-center justify-content-center" style="width: 60px">
            <img
  :src="getImageSrc(deal.food_snapshot)"
  alt="food"
  class="rounded"
  style="width: 50px; height: 50px; object-fit: cover"
/>
          </div>
          <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1">
              {{ deal.name }} <span class="text-success">0đ</span>
            </div>
            <div class="text-muted small">
              Hạn sử dụng: {{ formatDate(deal.expired_at) || 'Không rõ' }}
            </div>
          </div>
          <button
            v-if="!isExpired(deal.expired_at)"
            class="btn btn-outline-danger btn-sm"
            @click="useDealNow(deal)"
          >
            Dùng Ngay
          </button>
          <button
            v-else
            class="btn btn-secondary btn-sm"
            disabled
          >
            Hết hạn
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userAuth'
import { API_URL, STORAGE_URL } from '@/config'

const dealsFood = ref([])
const activeTab = ref(0)
const router = useRouter()
const userStore = useUserStore()

const getImageSrc = (snap) => {
  const img = snap?.image_url ?? snap?.image
  if (!img) return '/img/placeholder-food.png'   
  if (/^https?:\/\//i.test(img)) return img      
  return `${STORAGE_URL}/img/food/${img}`         
}

const tabs = ref([
  { label: 'Tất cả', count: 0 },
  { label: 'Deal hết hạn', count: 0 }
])

const getDealsFood = async () => {
  try {
    const token = userStore.token || localStorage.getItem('token')
    const res = await axios.get(`${API_URL}/deals-food`, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    })
    const payload = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
    dealsFood.value = payload

    const now = new Date()
    const expiredCount = dealsFood.value.filter((d) => new Date(d.expired_at) < now).length
    tabs.value[0].count = dealsFood.value.length
    tabs.value[1].count = expiredCount
  } catch (e) {
    console.error('getDealsFood error:', e)
    dealsFood.value = []
    tabs.value[0].count = 0
    tabs.value[1].count = 0
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return null
  const date = new Date(dateStr)
  return date.toLocaleString('vi-VN', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

const isExpired = (expired_at) => {
  if (!expired_at) return false
  return new Date(expired_at) < new Date()
}

const filteredDealsFood = computed(() => {
  if (activeTab.value === 1) {
    return dealsFood.value.filter((d) => isExpired(d.expired_at))
  }
  return dealsFood.value
})

const useDealNow = (deal) => {
  const userId = userStore.user?.id || 'guest'
  const cartKey = `cart_${userId}`
  const snap = deal.food_snapshot
  if (!snap?.id) {
    alert('Thông tin món ăn không hợp lệ.')
    return
  }

  // chuẩn hoá dữ liệu
  const originalPrice = Number(snap.price_at_win ?? snap.price ?? 0)
  const name = snap.name ?? deal.name ?? 'Món ưu đãi'
  const image = snap.image_url ?? snap.image ?? null
  const categoryId = snap.category_id ?? null

  let cart = []
  try {
    cart = JSON.parse(localStorage.getItem(cartKey) || '[]')
  } catch {
    cart = []
  }

  const idx = cart.findIndex((item) => item.id === snap.id && item.type === 'food')

  if (idx !== -1) {
    cart[idx].quantity += 1
    cart[idx].free_quantity = (cart[idx].free_quantity ?? 0) + 1
  } else {
    cart.push({
      id: snap.id,
      name,
      image,                      
      price: 0,                   
      original_price: originalPrice,
      toppings: [],
      quantity: 1,
      type: 'food',                
      category_id: categoryId,
      free_quantity: 1,           
      is_deal: true,
      reward_id: deal.id,         
    })
  }

  localStorage.setItem(cartKey, JSON.stringify(cart))
  router.push('/cart')
}

onMounted(() => {
  getDealsFood()
})
</script>


<style scoped>
.deal-card {
  border: 2px dashed #d9363e;
  background: linear-gradient(to right, #fff9f9, #fff1f1);
  transition: transform 0.2s ease;
}
.deal-card:hover {
  transform: scale(1.01);
}
</style>
