<template>
  <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div v-else class="container mt-5 fade-in">
    <div class="row g-4">
      <!-- Sidebar -->
      <div class="col-12 col-md-4 col-lg-3 mb-4 mb-md-0">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="d-flex flex-column flex-md-row align-items-center mb-3">
            <div class="avatar-container">
              <template v-if="avatarUrl">
                <img :src="avatarUrl" alt="Avatar" class="avatar-circle" />
              </template>
              <template v-else>
                <div class="avatar-circle border-custom d-flex justify-content-center align-items-center">
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

              <a href="#" @click="handleLogout"
                class="list-group-item-action link-danger small d-flex align-items-center justify-content-center justify-content-md-start gap-1 mt-2">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
              </a>

              <button
                class="rounded-pill px-2 py-1 d-flex align-items-center justify-content-center justify-content-md-start gap-1 mt-2 fw-bold border-0 bg-warning"
                style="font-size: 12px; line-height: 1; color: white">
                <img src="/img/xubac.png" alt="coins" style="width: 15px" />
                {{ formatNumber(form.usable_points) }} TGold
              </button>
            </div>
          </div>
          <div class="fw-bold text-danger mb-1 d-flex justify-content-center align-items-center gap-2"
            style="font-size: 14px;">
            Thành Viên TITOKAQA
          </div>
          <div class="bg-light rounded p-2 text-center mb-3 border border-light-subtle">


            <div class="mx-auto" style="max-width: 260px; font-size: 13px;">
              <!-- Điểm -->
              <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted">Điểm</span>
                <span class="fw-thin">{{ form.rank_points }}+</span>
              </div>

              <!-- Hạng -->
              <div class="d-flex justify-content-between align-items-center py-1">
                <span class="text-muted">Hạng</span>
                <span class="fw-bold d-flex align-items-center gap-1" :style="{ color: rankColor }">
                  {{ form.rank }}
                  <img :src="rankImage" alt="rank-icon" style="height: 16px;" />
                </span>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <router-link to="/coins-reward"
              class="text-decoration-none small d-inline-flex align-items-center gap-1 text-dark fw-bold border rounded px-2 py-1"
              style="font-size: 12px; border-color: #dee2e6;">
              Đổi TGold
            </router-link>
          </div>



          <ul class="list-group list-group-flush">
            <router-link to="/update-user" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold text-danger">Thông tin tài khoản</div>
                  <div class="small text-muted">Cập nhật thông tin</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>

            <router-link to="/order-management" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold">Quản lý đơn hàng</div>
                  <div class="small text-muted">Đơn hàng của tôi</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>
          </ul>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-12 col-md-8 col-lg-9">
        <h4 class="fw-bold mb-4">Quản lý tài khoản</h4>

        <div class="card shadow-lg p-4 mx-auto">
          <div class="row">
            <!-- Cột trái -->
            <div class="col-md-7 mb-5 mb-md-0">
              <form @submit.prevent="handleSubmit">
                <div class="mb-3">
                  <label class="form-label">Tên người dùng</label>
                  <input type="text" v-model="form.fullname" class="form-control form-control-lg rounded"
                    placeholder="Nhập nickname của bạn" id="fullname" />
                </div>

                <div class="mb-3">
                  <label for="phone" class="form-label">Số điện thoại</label>
                  <div class="input-group">
                    <span class="input-group-text">+84</span>
                    <input type="text" v-model="form.phone" class="form-control form-control-lg rounded" id="phone"
                      placeholder="Nhập số điện thoại của bạn" />
                  </div>
                </div>

                <div class="mb-3">
                  <label for="address" class="form-label">Địa chỉ</label>
                  <input type="text" v-model="form.address" class="form-control form-control-lg rounded" id="address"
                    placeholder="Nhập địa chỉ của bạn" />
                </div>
                <div class="text-center">
                  <button type="submit" style="background-color: #ca111f" class="btn text-white w-100">
                    Lưu tài khoản
                  </button>
                </div>
              </form>
            </div>

            <!-- Cột phải -->
            <div class="col-md-5 ps-md-4 pt-4 pt-md-0 border-top border-md-0 border-md-start">
              <ul class="p-0 m-0 list-unstyled">
                <li class="p-3 border rounded d-flex justify-content-between align-items-center mb-3">
                  <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-envelope"></i>
                    <div>
                      <div class="fw-bold">Địa chỉ email</div>
                      <div class="small text-muted">Thay đổi địa chỉ email</div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger w-100" style="max-width: 100px">
                    <strong>Cập nhật</strong>
                  </button>
                </li>
                <li class="p-3 border rounded d-flex justify-content-between align-items-center mb-3">
                  <div class="d-flex align-items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-lock" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4M4.5 7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7zM8 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3" />
                    </svg>
                    <div>
                      <div class="fw-bold">Đổi mật khẩu</div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger w-100" style="max-width: 100px">
                    <strong>Cập nhật</strong>
                  </button>
                </li>
                <li class="p-3 border rounded d-flex justify-content-between align-items-center mb-3">
                  <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-trash"></i>
                    <div>
                      <div class="fw-bold">Xóa tài khoản</div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger w-100" style="max-width: 100px">
                    <strong>Xóa</strong>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'
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
    // RANK AND PONTS
    //=========================
    const rankImage = computed(() => {
      if (form.value.rank_points >= 3000) {
        return '/public/img/item/rank-diamond.gif'
      } else if (form.value.rank_points >= 1000) {
        return '/public/img/item/rank-gold.gif'
      } else {
        return '/public/img/item/rank-silver.gif'
      }
    })
    const rankColor = computed(() => {
      if (form.value.rank_points >= 3000) {
        return '#00d0f0' // diamond
      } else if (form.value.rank_points >= 1000) {
        return '#f5f500' // gold
      } else {
        return '#9a9a9a' // silver
      }
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
      //rank
      rankColor,
      rankImage,
      formatNumber,
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

#app>div>div.container.mt-5.fade-in>div>div.col-12.col-md-4.col-lg-3.mb-4.mb-md-0>div>div.bg-light.rounded-3.p-3.text-center.mb-3>div.d-flex.justify-content-around.mt-3>div:nth-child(1)>div.fw-medium {
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
