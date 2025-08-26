<template>
  <div class="lucky-wrapper d-flex flex-column flex-lg-row align-items-center align-items-lg-start">
    <div class="info-panel rules-panel">
      <h4 class="panel-title">🎁 Thể Lệ Vòng Quay</h4>
      <div class="panel-content">
        <p class="rule-item">
          <WarningOutlined class="rule-icon-warning" />
          <span>Mỗi ngày bạn có <strong>1 lượt</strong> quay miễn phí.</span>
        </p>
        <p class="rule-item">
          <ClockCircleOutlined class="rule-icon-info" />
          <span>Phần thưởng có hạn sử dụng <strong>7 ngày</strong> từ lúc nhận.</span>
        </p>
        <p class="rule-item">
          <ClockCircleOutlined class="rule-icon-info" />
          <span>Phần thưởng trùng sẽ được <strong>cộng dồn thời gian</strong> sử dụng.</span>
        </p>
        <p class="rule-item">
          <GiftOutlined class="rule-icon-reward" />
          <span>Chọn <strong>“Lưu về kho”</strong> để nhận và sử dụng phần thưởng.</span>
        </p>
        <p class="rule-item">
          <StarOutlined class="rule-icon-random" />
          <span>Phần thưởng bạn nhận được là <strong>hoàn toàn ngẫu nhiên</strong>.</span>
        </p>
        <p class="rule-item">
          <InfoCircleOutlined class="rule-icon-login" />
          <span>Vui lòng <strong>đăng nhập</strong> để tham gia vòng quay.</span>
        </p>
        <div class="lucky-wish">
          <SmileOutlined class="wish-icon" />
          <span>Chúc bạn may mắn!</span>
        </div>
      </div>
    </div>

    <div class="wheel-container flex-fill">
      <FortuneWheel
        ref="fortuneRef"
        :manual="true"
        :canvas="canvasOptions"
        :prizes="prizes"
        @rotateEnd="onRotateEnd"
      />
      <div class="text-center mt-4">
        <button
          class="spin-button"
          :disabled="isLoading"
          @click="isAuthenticated ? onCanvasRotateStart() : redirectToLogin()"
        >
          <template v-if="isAuthenticated">
            Quay ngay ({{ spinStatus.remaining_spin }}/{{ spinStatus.max_spin }})
          </template>
          <template v-else> Đăng nhập để quay </template>
        </button>
      </div>
    </div>

    <div class="info-panel history-panel">
      <h4 class="panel-title">📜 Lịch Sử Phần Thưởng</h4>
      <div class="panel-content history-list">
        <div v-if="filteredRewards.length > 0">
          <div v-for="reward in filteredRewards" :key="reward.id" class="history-item">
            <div class="item-info">
              <p class="item-name"><strong>🎉</strong> {{ reward.prize_name }}</p>
              <p class="item-date">Ngày quay: {{ formatDate(reward.spun_at) }}</p>
            </div>
            <button
              class="claim-button"
              :disabled="reward.is_claimed"
              @click="claimReward(reward.id)"
            >
              {{ reward.is_claimed ? 'Đã Lưu' : 'Lưu Kho' }}
            </button>
          </div>
        </div>
        <div v-else class="no-rewards">Bạn chưa có phần thưởng nào.</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
/* External libs */
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import {
  WarningOutlined,
  ClockCircleOutlined,
  GiftOutlined,
  StarOutlined,
  InfoCircleOutlined,
  SmileOutlined
} from '@ant-design/icons-vue'

import router from '@/router'
import FortuneWheel from '@/components/fortuneWheel/index.vue'
import { PrizeConfig } from '@/components/fortuneWheel/types'
import { useUserStore } from '@/stores/userAuth'
import { Rewards } from '@/stores/rewards'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
const userStore = useUserStore()
const isAuthenticated = computed(() => !!userStore.token)
const { userRewards, getUserRewards } = Rewards()

const fortuneRef = ref()
const prizes = ref<PrizeConfig[]>([])
const isLoading = ref(true)
const spinStatus = ref({ has_spun_today: false, remaining_spin: 1, max_spin: 1 })
const latestPrize = ref<null | { name: string; expired_at: string; claimed: boolean }>(null)

const canvasOptions = {
  btnWidth: 120,
  borderColor: '#ff1816',
  borderWidth: 6,
  lineHeight: 30,
  fontSize: 18,
  textLength: 10,
  btnImage: '/src/assets/btn-removebg-preview.png'
}

async function fetchPrizes() {
  try {
    const res = await axios.get(`${API_URL}/lucky-wheel/prizes`)
    const allPrizes = res.data
    prizes.value = normalizeProbabilities(
      allPrizes.map((item, index) => ({
        id: item.id,
        name: item.name,
        value: item.name,
        image: getImageByType(item.type),
        bgColor: index % 2 === 0 ? '#FF1514' : '#FFF0C4',
        color: index % 2 === 0 ? '#ffffff' : '#000000',
        probability: item.probability || 0,
        type: item.type,
        data: item.data || null
      }))
    )
  } catch (error) {
    console.error('❌ Lỗi khi lấy phần thưởng:', error)
  } finally {
    isLoading.value = false
  }
}

function normalizeProbabilities(prizesRaw: any[]) {
  const total = prizesRaw.reduce((sum, prize) => sum + (prize.probability || 0), 0)
  if (total === 0) return prizesRaw
  return prizesRaw.map((prize) => ({
    ...prize,
    probability: parseFloat(((prize.probability / total) * 100).toFixed(2))
  }))
}

function getImageByType(type: string) {
  return '/img/gif.png'
}

async function onCanvasRotateStart() {
  try {
    const res = await axios.post(
      `${API_URL}/spin`,
      {},
      { headers: { Authorization: `Bearer ${userStore.token}` } }
    )

    const prizeRaw = res.data.prize
    spinId.value = res.data.spin_id
    if (!prizeRaw || !prizeRaw.id) {
      const message = res?.data?.message || ''
      if (message.includes('hôm nay')) {
        await Swal.fire('Bạn đã quay hôm nay!', 'Thử lại vào ngày mai nhé 🎯', 'info')
        return
      }
      await Swal.fire('Lỗi!', 'Không có phần thưởng hợp lệ', 'error')
      return
    }

    const prize: PrizeConfig = {
      id: Number(prizeRaw.id),
      name: prizeRaw.name,
      value: prizeRaw.name,
      image: getImageByType(prizeRaw.type),
      bgColor: '#FF1514',
      color: '#ffffff',
      probability: prizeRaw.probability ?? 0,
      type: prizeRaw.type,
      data: prizeRaw.data ?? null
    }

    fortuneRef.value?.startRotate(prize)
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Lỗi không xác định khi quay'
    if (message.includes('hôm nay')) {
      await Swal.fire('Bạn đã quay hôm nay!', 'Thử lại vào ngày mai nhé 🎯', 'info')
    } else {
      await Swal.fire('Lỗi!', message, 'error')
    }
  }
}

const spinId = ref<number | null>(null)
async function onRotateEnd(prize: PrizeConfig) {
  const expiredAt = new Date()
  expiredAt.setDate(expiredAt.getDate() + 7)
  latestPrize.value = {
    name: prize.name,
    expired_at: expiredAt.toISOString(),
    claimed: false
  }

  const htmlContent = `
  <img src="${getImageByType(
    prize.type
  )}" style="width:100px;height:100px;margin-bottom:10px;" />
  <div style="font-size:24px;font-weight:bold;color:#28a745">🎉 Chúc mừng bạn!</div>
  <div style="font-size:20px;margin-top:8px;">${prize.name}</div>`

  await Swal.fire({
    html: htmlContent,
    showConfirmButton: true,
    confirmButtonText: 'Lưu Ngay',
    showCancelButton: true,
    cancelButtonText: 'Để sau',
    width: 350,
    timer: 4000,
    timerProgressBar: true
  }).then(async (result) => {
    if (result.isConfirmed && spinId.value) {
      await claimReward(spinId.value)
      spinId.value = null
    }
  })

  await getUserRewards()
  await fetchSpinStatus()
}

async function claimReward(rewardId: number) {
  const confirm = await Swal.fire({
    title: 'Xác nhận lưu?',
    text: 'Bạn có chắc chắn muốn lưu phần thưởng này vào kho?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Lưu ngay',
    cancelButtonText: 'Hủy'
  })
  if (!confirm.isConfirmed) return

  try {
    await axios.post(
      `${API_URL}/claim-reward`,
      { spin_id: rewardId },
      { headers: { Authorization: `Bearer ${userStore.token}` } }
    )
    await Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã lưu vào kho!',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false
    })
    await getUserRewards()
  } catch (error: any) {
    const msg = error?.response?.data?.message || 'Có lỗi xảy ra khi lưu quà'
    await Swal.fire('Lỗi!', msg, 'error')
  }
}

function formatDate(dateStr: string) {
  return new Date(dateStr).toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

async function fetchSpinStatus() {
  try {
    const res = await axios.get(`${API_URL}/spin-status`, {
      headers: { Authorization: `Bearer ${userStore.token}` }
    })
    spinStatus.value = res.data
  } catch (e) {
    console.error('Lỗi lấy trạng thái lượt quay', e)
  }
}

function redirectToLogin() {
  Swal.fire({
    icon: 'info',
    title: 'Vui lòng đăng nhập',
    text: 'Bạn cần đăng nhập để quay vòng may mắn 🎯',
    confirmButtonText: 'Đăng nhập ngay'
  }).then((result) => {
    if (result.isConfirmed) {
      router.push('/login')
    }
  })
}

/* Mount */
onMounted(() => {
  fetchPrizes()
  if (isAuthenticated.value) {
    getUserRewards()
    fetchSpinStatus()
  }
})

const filteredRewards = computed(() => userRewards.value)
</script>


<style scoped>
@keyframes gradient-animation {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
.lucky-wrapper {
  width: 100%;
  min-height: 80vh;
  padding: 2rem;

  background: url('@/assets/bg.png') no-repeat center center;
  background-size: cover;

  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: flex-start;
  gap: 2rem;
}

/* ---- INFO PANELS (RULES & HISTORY) ---- */
.info-panel {
  width: 100%;
  max-width: 380px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 1.5rem;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
  color: white;
}

.panel-title {
  font-weight: bold;
  margin-bottom: 1.5rem;
  text-align: center;
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 1);
}

.panel-content {
  font-size: 0.95rem;
}
/* ---- RULES PANEL SPECIFIC STYLES ---- */
.rule-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.9);
}

.rule-item .ant-icon {
  font-size: 20px;
  flex-shrink: 0;
}

.rule-icon-warning { color: #faad14; }
.rule-icon-info { color: #1890ff; }
.rule-icon-reward { color: #eb2f96; }
.rule-icon-random { color: #fadb14; }
.rule-icon-login { color: #13c2c2; }

.lucky-wish {
  margin-top: 1.5rem;
  text-align: center;
  font-weight: bold;
  font-size: 1.1rem;
  color: #fff;
}
.wish-icon {
  color: #52c41a;
  font-size: 22px;
  margin-right: 8px;
}

/* ---- WHEEL CONTAINER & SPIN BUTTON ---- */
.wheel-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 470px;
}

.spin-button {
  padding: 12px 32px;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: bold;
  color: white;
  background: linear-gradient(45deg, #ff416c, #ff4b2b);
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  user-select: none;
}

.spin-button:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.spin-button:active:not(:disabled) {
  transform: translateY(-1px);
}

.spin-button:disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.7;
}

/* ---- HISTORY PANEL SPECIFIC STYLES ---- */
.history-list {
  max-height: 450px;
  overflow-y: auto;
  padding-right: 10px;
  margin-right: -10px;
}

/* Custom scrollbar */
.history-list::-webkit-scrollbar {
  width: 6px;
}
.history-list::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}
.history-list::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}
.history-list::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

.history-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.8rem;
  margin-bottom: 0.8rem;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 10px;
  transition: background 0.3s ease;
}

.history-item:hover {
  background: rgba(0, 0, 0, 0.3);
}

.item-info .item-name {
  font-weight: bold;
  margin: 0;
  color: #fff;
}
.item-info .item-date {
  font-size: 0.8rem;
  margin: 0;
  color: rgba(255, 255, 255, 0.7);
}

.claim-button {
  padding: 6px 12px;
  border: 1px solid white;
  border-radius: 6px;
  background: transparent;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.85rem;
  flex-shrink: 0; /* Prevents button from shrinking */
}

.claim-button:hover:not(:disabled) {
  background: white;
  color: #e73c7e;
}

.claim-button:disabled {
  background: transparent;
  border-color: rgba(255, 255, 255, 0.4);
  color: rgba(255, 255, 255, 0.4);
  cursor: not-allowed;
}

.no-rewards {
  text-align: center;
  padding: 2rem 0;
  color: rgba(255, 255, 255, 0.8);
}

/* ---- RESPONSIVE ORDERING ---- */
@media (max-width: 1200px) {
  .wheel-container {
    order: 1;
    margin-bottom: 2rem;
  }
  .rules-panel {
    order: 2;
    margin-bottom: 2rem;
  }
  .history-panel {
    order: 3;
  }
}
</style>
