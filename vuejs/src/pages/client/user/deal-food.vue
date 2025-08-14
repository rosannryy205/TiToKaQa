<template>
  <div class="container py-3 position-relative col-12 col-md-8 col-lg-9">
    <h4 class="fw-bold mb-3 text-danger">🎉 Deal Món Đã Nhận</h4>
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
              :src="`/public/img/food/${deal.food_snapshot?.image}`"
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

const dealsFood = ref([])
const activeTab = ref(0)
const router = useRouter()
const userStore = useUserStore()

const tabs = ref([
  { label: 'Tất cả', count: 0 },
  { label: 'Deal hết hạn', count: 0 }
])

const getDealsFood = async () => {
  const res = await axios.get('http://localhost:8000/api/deals-food', {
    headers: { Authorization: `Bearer ${userStore.token}` },
  })

  if (res.data.status) {
    dealsFood.value = res.data.data

    const now = new Date()
    const expiredCount = dealsFood.value.filter((d) => new Date(d.expired_at) < now).length
    tabs.value[0].count = dealsFood.value.length
    tabs.value[1].count = expiredCount
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
  const userId = userStore.user.id
  const cartKey = `cart_${userId}`
  const food = deal.food_snapshot
  if (!food) return alert('Thông tin món ăn không hợp lệ.')

  let cart = JSON.parse(localStorage.getItem(cartKey) || '[]')
  const existingIndex = cart.findIndex((item) => item.id === food.id && item.type === 'Food')

  if (existingIndex !== -1) {
    cart[existingIndex].quantity += 1
    cart[existingIndex].free_quantity = 1
  } else {
    cart.push({
      id: food.id,
      name: food.name,
      image: food.image,
      price: '0',
      original_price: food.price,
      toppings: [],
      quantity: 1,
      type: 'Food',
      category_id: food.category_id,
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
