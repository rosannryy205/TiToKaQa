<template>
  <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <div v-else class="container mt-5 fade-in">
    <div class="row g-4">
      <div class="col-12 col-md-4 col-lg-3 mb-4 mb-md-0">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="d-flex flex-column flex-md-row align-items-center mb-3">
            <div class="avatar-container">
              <template v-if="avatarUrl">
                <img :src="avatarUrl" alt="Avatar" class="avatar-circle" />
              </template>
              <template v-else>
                <div
                  class="avatar-circle border-custom d-flex justify-content-center align-items-center"
                >
                  {{ getInitial(form?.fullname) || getInitial(form?.username) }}
                </div>
              </template>

              <div class="avatar-overlay">
                <label for="avatar" class="btn btn-sm btn-light">
                  <i class="bi bi-pencil"></i>
                </label>
                <input type="file" id="avatar" class="d-none" @change="handleImageUpload" />
              </div>
            </div>

            <div class="ms-md-4 mt-3 mt-md-0 text-center text-md-start">
              <h6 class="fw-bold mb-2">{{ form.fullname || form.username }}</h6>

              <a
                href="#"
                @click="handleLogout"
                class="list-group-item-action link-danger small d-flex align-items-center justify-content-center justify-content-md-start gap-1 mt-2"
              >
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
              </a>

              <button
                class="rounded-pill px-2 py-1 d-flex align-items-center justify-content-center justify-content-md-start gap-1 mt-2 fw-bold border-0 bg-warning shadow-sm"
                style="font-size: 12px; line-height: 1; color: white"
              >
                <img src="/img/xubac.png" alt="coins" style="width: 15px" />
                {{ formatNumber(form.usable_points) }} TCoins
              </button>
            </div>
          </div>
          <div
            class="fw-bold text-danger mb-1 d-flex justify-content-center align-items-center gap-2"
            style="font-size: 14px"
          >
            Thành Viên TITOKAQA
          </div>
          <div class="bg-light rounded p-2 text-center mb-3 border border-light-subtle">
            <div class="mx-auto" style="max-width: 260px; font-size: 13px">
              <div class="d-flex justify-content-between align-items-center py-1">
                <span class="text-dark">Điểm của bạn: </span>
                <span class="fw-thin text-danger">{{ formatNumber(form.rank_points) }}</span>
              </div>
              <div class="text-muted small fst-italic text-start mt-1">
                * Tích lũy qua mỗi đơn hàng
              </div>

              <div class="border-top my-2"></div>

              <div class="d-flex justify-content-between align-items-center py-1">
                <span class="text-dark">Hạng của bạn: </span>
                <span class="fw-bold d-flex align-items-center gap-1" :style="{ color: rankColor }">
                  {{ formRank }}
                  <img :src="rankImage" alt="rank-icon" style="height: 16px" />
                </span>
              </div>

              <!---->
              <div class="text-muted small fst-italic text-start mt-1" v-if="nextRank">
                * Cần thêm
                <span class="text-danger fw-bold">{{ formatNumber(neededPoints) }}</span> điểm để
                lên hạng {{ nextRankName }}
              </div>
              <div class="text-muted small fst-italic text-start mt-1" v-else>
                * Bạn đã đạt hạng cao nhất 🎉
              </div>

              <!---->
              <div class="progress mt-2" style="height: 20px">
                <div
                  class="progress-bar"
                  role="progressbar"
                  :style="{ width: rankProgressPercent + '%', backgroundColor: rankColor }"
                  :aria-valuenow="rankProgressPercent"
                  aria-valuemin="0"
                  aria-valuemax="100"
                >
                  {{ rankProgressPercent }}%
                </div>
              </div>

              <!-- 🔸 Mốc điểm -->
              <div class="mt-3 text-start small text-muted">
                <div><strong>500+</strong> điểm: Hạng Bạc</div>
                <div><strong>1000+</strong> điểm: Hạng Vàng</div>
                <div><strong>3000+</strong> điểm: Kim Cương</div>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <router-link
              to="/account/coins-reward"
              class="text-decoration-none small d-inline-flex align-items-center gap-1 text-dark fw-bold border rounded px-2 py-1"
              style="font-size: 12px; border-color: #dee2e6"
            >
              Đổi TCoins
            </router-link>
          </div>

          <ul class="list-group list-group-flush">
            <router-link to="/account/update-user" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold text-danger">Thông tin tài khoản</div>
                  <div class="small text-muted">Cập nhật thông tin</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>

            <router-link to="/account/order-management" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold">Quản lý đơn hàng</div>
                  <div class="small text-muted">Đơn hàng của tôi</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>
            <router-link to="/account/discount-management" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold">Kho Mã Giảm Giá</div>
                  <div class="small text-muted">Mã giảm giá của tôi</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>
          </ul>
        </div>
      </div>
      <router-view />
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { User } from '@/stores/user'

export default {
  setup() {
    const {
      form,
      user,
      handleSubmit,
      handleImageUpload,
      handleLogout,
      getInitial,
      loading,
      avatarUrl,
    } = User.setup()

    //=========================
    // RANK CONFIG & LOGIC
    //=========================
    const ranks = [
      { name: 'Bạc', min: 500, color: '#9a9a9a', icon: '/public/img/item/rank-silver.gif' },
      { name: 'Vàng', min: 1000, color: '#f5f500', icon: '/public/img/item/rank-gold.gif' },
      { name: 'Kim cương', min: 3000, color: '#00d0f0', icon: '/public/img/item/rank-diamond.gif' },
    ]

    const currentRank = computed(() => {
      if (form.value.rank_points < 500) return null
      return [...ranks].reverse().find((rank) => form.value.rank_points >= rank.min)
    })

    const nextRank = computed(() => {
      if (!currentRank.value) return ranks[0]
      return ranks.find((rank) => rank.min > form.value.rank_points)
    })

    const rankImage = computed(() => currentRank.value?.icon || '/public/img/item/padlock.png')
    const rankColor = computed(() => currentRank.value?.color || '#6c757d')
    const formRank = computed(() => currentRank.value?.name || 'Chưa có hạng')

    form.value.rank = formRank.value

    const neededPoints = computed(() => {
      return nextRank.value ? nextRank.value.min - form.value.rank_points : 0
    })

    const nextRankName = computed(() => {
      return nextRank.value ? nextRank.value.name : ''
    })

    const rankProgressPercent = computed(() => {
      if (!nextRank.value) return 100
      const currentMin = currentRank.value?.min || 0
      const total = nextRank.value.min - currentMin
      const progress = form.value.rank_points - currentMin
      return Math.min(100, Math.round((progress / total) * 100))
    })

    //==================
    // Format point
    //==================
    const formatNumber = (value) => {
      return new Intl.NumberFormat('vi-VN').format(value)
    }

    return {
      form,
      user,
      handleSubmit,
      handleImageUpload,
      handleLogout,
      getInitial,
      loading,
      avatarUrl,
      rankColor,
      rankImage,
      formatNumber,
      formRank,
      rankProgressPercent,
      nextRank,
      neededPoints,
      nextRankName,
    }
  },
}
</script>

<style scoped>
.border-custom {
  border: 1px solid #ca111f;
}

.avatar-container {
  position: relative;
  width: clamp(80px, 25vw, 100px);
  height: clamp(80px, 25vw, 100px);
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}

.avatar-circle {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  object-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  background-color: #f8f9fa;
  transition: filter 0.3s ease;
}

.avatar-container .border-custom {
  border: 1px solid #ca111f;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: rgba(86, 86, 86, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
  cursor: pointer;
}

.avatar-container:hover .avatar-overlay {
  opacity: 1;
}

.avatar-container:hover .avatar-circle {
  filter: brightness(0.7);
}

.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}

.list-group-item:hover {
  background-color: #cdcdcd;
  border-radius: 20px;
  cursor: pointer;
}

li.list-group-item {
  border: none !important;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .avatar-container {
    max-width: 100px;
    max-height: 100px;
  }

  .avatar-circle {
    font-size: 28px;
  }
}

@media (min-width: 768px) {
  .border-md-start {
    border-top: none !important;
    border-left: 1px solid #dee2e6 !important;
  }
}

/**rank gif */
.gif-rank {
  width: 40px;
  height: 40px;
}
#app
  > div
  > div.container.mt-5.fade-in
  > div
  > div.col-12.col-md-4.col-lg-3.mb-4.mb-md-0
  > div
  > div.bg-light.rounded-3.p-3.text-center.mb-3
  > div.d-flex.justify-content-around.mt-3
  > div:nth-child(1)
  > div.fw-medium {
  padding: 9px;
}
/**coins gif */
.coins-gif {
  width: 35px;
  height: 35px;
}
.logo-member {
  width: 25px;
}
</style>
