<template>
  <div class="lucky-wrapper d-flex flex-column flex-md-row gap-3">
    <!-- Thể lệ -->
    <div class="gift-panel align-self-start gift-rules">
      <div class="card shadow-sm p-3">
        <h4 class="mb-3 fw-bold">🎁 Thể Lệ Vòng Quay</h4>
        <p class="mb-2 d-flex align-items-start">
          <WarningOutlined style="color: #faad14; font-size: 18px; margin-right: 8px" />
          <span>Mỗi ngày<strong> 1 lần</strong> quay.</span>
        </p>
        <p class="mb-2 d-flex align-items-start">
          <ClockCircleOutlined style="color: #1890ff; font-size: 18px; margin-right: 8px" />
          <span>Phần thưởng có hạn <strong>7 ngày</strong> từ lúc nhận.</span>
        </p>
        <p class="mb-2 d-flex align-items-start">
          <ClockCircleOutlined style="color: #1890ff; font-size: 18px; margin-right: 8px" />
          <span>Phần thưởng trùng sẽ <strong>tăng thời gian</strong> sử dụng</span>
        </p>
        <p class="mb-2 d-flex align-items-start">
          <GiftOutlined style="color: #eb2f96; font-size: 18px; margin-right: 8px" />
          <span>Chọn <strong>“Lưu về kho”</strong> để lưu về kho</span>
        </p>
        <p class="mb-2 d-flex align-items-start">
          <StarOutlined style="color: #fadb14; font-size: 18px; margin-right: 8px" />
          <span>Phần thưởng sẽ được nhận <strong>ngẫu nhiên</strong>.</span>
        </p>
        <p class="mb-2 d-flex align-items-start">
          <InfoCircleOutlined style="color: #13c2c2; font-size: 18px; margin-right: 8px" />
          <span>Vui lòng <strong>đăng nhập</strong> để quay.</span>
        </p>
        <div class="mt-3 text-center">
          <SmileOutlined style="color: #52c41a; font-size: 22px" />
          <span class="fw-bold ms-2">Chúc bạn may mắn!</span>
        </div>
      </div>
    </div>

    <!-- Vòng quay -->
    <div class="wheel-container flex-fill">
      <FortuneWheel
        ref="fortuneRef"
        :manual="true"
        :canvas="canvasOptions"
        :prizes="prizes"
        @rotateEnd="onRotateEnd"
      />
      <div class="text-center mt-3">
        <button :disabled="isLoading" @click="isAuthenticated ? onCanvasRotateStart() : redirectToLogin()">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front text">
            <template v-if="isAuthenticated">
              Quay ngay {{ spinStatus.remaining_spin }}/{{ spinStatus.max_spin }}
            </template>
            <template v-else>
              Đăng nhập để quay
            </template>
          </span>
        </button>
      </div>
    </div>

    <!-- Lịch sử -->
    <div class="gift-panel align-self-start gift-history">
      <div class="card shadow-sm p-3">
        <h4 class="mb-3 fw-bold">🎁 Lịch Sử Vòng Quay</h4>
        <div v-if="filteredRewards.length > 0">
          <div v-for="reward in filteredRewards" :key="reward.id" class="border-bottom pb-2 mb-2">
            <p class="mb-1 text-white"><strong>🎉</strong> {{ reward.prize_name }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <p class="mb-1 text-white" style="font-size: 0.85rem">
                Ngày quay {{ formatDate(reward.spun_at) }}
              </p>
              <button class="m-2" :disabled="reward.is_claimed" @click="claimReward(reward.id)">
                <span class="shadow"></span>
                <span class="edge"></span>
                <span class="front text">
                  {{ reward.is_claimed ? 'Đã lưu kho' : 'Lưu về kho' }}
                </span>
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-white">Bạn chưa có phần thưởng nào</div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import router from '@/router'

import FortuneWheel from '@/components/fortuneWheel/index.vue'
import { PrizeConfig } from '@/components/fortuneWheel/types'
import { useUserStore } from '@/stores/userAuth'
import { Rewards } from '@/stores/rewards'

import {
  WarningOutlined,
  ClockCircleOutlined,
  GiftOutlined,
  StarOutlined,
  InfoCircleOutlined,
  SmileOutlined,
} from '@ant-design/icons-vue'

// Store và state
const userStore = useUserStore()
const isAuthenticated = computed(() => !!userStore.token)
const { userRewards, getUserRewards } = Rewards()

const fortuneRef = ref()
const prizes = ref<PrizeConfig[]>([])
const isLoading = ref(true)
const spinStatus = ref({ has_spun_today: false, remaining_spin: 1, max_spin: 1 })
const latestPrize = ref<null | { name: string; expired_at: string; claimed: boolean; }>(null)

// Cấu hình vòng quay
const canvasOptions = {
  btnWidth: 120,
  borderColor: '#ff1816',
  borderWidth: 6,
  lineHeight: 30,
  fontSize: 18,
  textLength: 10,
  btnImage: '/src/assets/btn-removebg-preview.png',
}
async function fetchPrizes() {
  try {
    const res = await axios.get('http://localhost:8000/api/lucky-wheel/prizes')
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
        data: item.data || null,
      })),
    )
  } catch (error) {
    console.error('❌ Lỗi khi lấy phần thưởng:', error)
  } finally {
    isLoading.value = false
  }
}
function normalizeProbabilities(prizesRaw) {
  const total = prizesRaw.reduce((sum, prize) => sum + (prize.probability || 0), 0)
  if (total === 0) return prizesRaw
  return prizesRaw.map((prize) => ({
    ...prize,
    probability: parseFloat(((prize.probability / total) * 100).toFixed(2)),
  }))
}

function getImageByType(type: string) {
  return type === 'none' ? '/public/img/khongtrung.png' : '/public/img/gif.png'
}

async function onCanvasRotateStart() {
  try {
    const res = await axios.post(
      'http://localhost:8000/api/spin',
      {},
      { headers: { Authorization: `Bearer ${userStore.token}` } },
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
      data: prizeRaw.data ?? null,
    }

    fortuneRef.value?.startRotate(prize)
  } catch (err) {
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
    claimed: false,
  }

  const isNone = prize.type === 'none'
  const htmlContent = isNone
    ? `<img src="${getImageByType(prize.type)}" style="width:100px;height:100px;margin-bottom:10px;" />
       <div style="font-size:20px;font-weight:bold;color:#999">Chúc Bạn May Mắn Lần Sau !</div>`
    : `<img src="${getImageByType(prize.type)}" style="width:100px;height:100px;margin-bottom:10px;" />
       <div style="font-size:24px;font-weight:bold;color:#28a745">🎉 Chúc mừng bạn!</div>
       <div style="font-size:20px;margin-top:8px;">${prize.name}</div>`

  await Swal.fire({
    html: htmlContent,
    showConfirmButton: true,
    confirmButtonText: isNone ? 'Quay Lại vào Ngày Mai' : 'Lưu Ngay',
    showCancelButton: !isNone,
    cancelButtonText: 'Để sau',
    width: 350,
    timer: 4000,
    timerProgressBar: true,
  }).then(async (result) => {
    if (result.isConfirmed && !isNone && spinId.value) {
      await claimReward(spinId.value)
    }
  })

  await getUserRewards()
  await fetchSpinStatus()
}
async function claimReward(rewardId) {
  const confirm = await Swal.fire({
    title: 'Xác nhận lưu?',
    text: 'Bạn có chắc chắn muốn lưu phần thưởng này vào kho?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Lưu ngay',
    cancelButtonText: 'Hủy',
  })

  if (!confirm.isConfirmed) return

  try {
    await axios.post(
      'http://localhost:8000/api/claim-reward',
      { spin_id: rewardId },
      { headers: { Authorization: `Bearer ${userStore.token}` } },
    )
    await Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: 'Đã lưu vào kho!',
  timer: 2000,
  timerProgressBar: true,
  showConfirmButton: false,
})

    await getUserRewards()
  } catch (error) {
    const msg = error?.response?.data?.message || 'Có lỗi xảy ra khi lưu quà'
    await Swal.fire('Lỗi!', msg, 'error')
  }
}
function formatDate(dateStr: string) {
  return new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

async function fetchSpinStatus() {
  try {
    const res = await axios.get('http://localhost:8000/api/spin-status', {
      headers: { Authorization: `Bearer ${userStore.token}` },
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
    confirmButtonText: 'Đăng nhập ngay',
  }).then((result) => {
    if (result.isConfirmed) {
      router.push('/login')
    }
  })
}

// Mount
onMounted(() => {
  fetchPrizes()
  if (isAuthenticated.value) {
    getUserRewards()
    fetchSpinStatus()
  }
})

const filteredRewards = computed(() => userRewards.value.filter((r) => r.prize_type !== 'none'))
</script>


<style scoped>
.lucky-wrapper {
  background: url('@/assets/background-lcw.png') center/cover no-repeat !important;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  background: linear-gradient(to bottom, #fffdf4, #f5f5f5);
}

.wheel-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 470px;
  margin: 0 auto;
}

/* RESPONSIVE xử lý lại thứ tự khi màn hình nhỏ */
@media (max-width: 767.98px) {
  .lucky-wrapper {
    flex-direction: column !important;
  }

  .wheel-container {
    order: 1;
    width: 100%;
  }

  .gift-rules {
    order: 2;
    width: 100%;
  }

  .gift-history {
    order: 3;
    width: 100%;
  }
}
button {
  position: relative;
  border: none;
  background: transparent;
  padding: 0;
  cursor: pointer;
  outline-offset: 4px;
  transition: filter 250ms;
  user-select: none;
  touch-action: manipulation;
}

.shadow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
  background: hsl(0deg 0% 0% / 0.25);
  transform: translateY(2px);
  transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);
}

.edge {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
  background: linear-gradient(
    to left,
    hsl(340deg 100% 16%) 0%,
    hsl(340deg 100% 32%) 8%,
    hsl(340deg 100% 32%) 92%,
    hsl(340deg 100% 16%) 100%
  );
}

.front {
  display: block;
  position: relative;
  padding: 6px 16px;
  border-radius: 8px;
  font-size: 0.9rem;
  color: white;
  background: hsl(345deg 100% 47%);
  transform: translateY(-4px);
  transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);
}

button:hover {
  filter: brightness(110%);
}
button:hover .front {
  transform: translateY(-6px);
}
button:active .front {
  transform: translateY(-2px);
}
button:hover .shadow {
  transform: translateY(4px);
}
button:active .shadow {
  transform: translateY(1px);
}
button:disabled {
  cursor: not-allowed;
  filter: none !important;
  pointer-events: none;
}
button:disabled .front {
  background: #28a745 !important;
  color: #fff !important;
}
button:disabled .edge {
  background: #1e7e34 !important;
}
button:disabled .shadow {
  background: rgba(0, 0, 0, 0.2) !important;
}

.card {
  color: #f5f5f5;
  background-color: #ea322f;
  border: none;
}
.gift-history .card {
  max-height: 72.5vh;
  overflow-y: auto;
}
.gift-history .card::-webkit-scrollbar {
  width: 6px;
}

.gift-history .card::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}
span {
  color: white;
}
</style>
