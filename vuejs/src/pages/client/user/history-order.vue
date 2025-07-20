<template>
  <div class="container mt-5 fade-in">
    <div class="row g-4">
      <!-- Sidebar -->
      <div class="col-12 col-md-3">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="avatar-wrapper position-relative mx-auto mb-3">
            <template v-if="form && form.avatar">
              <img :src="form.avatar" alt="Avatar" class="rounded-circle avatar-img" />
            </template>
            <template v-else>
              <div class="avatar-placeholder d-flex justify-content-center align-items-center">
                {{ getInitial(form?.fullname) }}
              </div>
            </template>

            <!-- Hover overlay -->
            <div class="avatar-overlay d-flex justify-content-center align-items-center">
              <label class="btn btn-light btn-sm m-0 cursor-pointer">
                Đổi ảnh
                <input type="file" @change="handleImageUpload" hidden />
              </label>
            </div>
          </div>

          <h6 class="mt-2 mb-3">{{ form.fullname || 'Chưa có tên' }}</h6>

          <div class="list-group text-start">
            <router-link to="/update-user" class="text-decoration-none list-group-item list-group-item-action">
              <p class="mb-0 me-3">Thông tin tài khoản</p>
            </router-link>
            <router-link to="/history-order" class="text-decoration-none list-group-item list-group-item-action">
              <p class="mb-0 me-3">Lịch sử đơn hàng</p>
            </router-link>
            <a href="#" class="list-group-item list-group-item-action text-danger" @click="handleLogout">Đăng xuất</a>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="col-12 col-md-9">
        <div class="card shadow border-0 p-4">
          <h4 class="mb-4 text-center" style="color: #ca111f;">Lịch sử đơn hàng</h4>
          <div class="table-responsive">
            <table class="table table-hover text-center">
              <!-- Bảng cho desktop -->
              <thead v-if="isDesktop" class="table-light">
                <tr>
                  <th>Mã đơn</th>
                  <th>Ngày đặt</th>
                  <th>Tổng tiền</th>
                  <th>Trạng thái</th>
                  <th>Chi tiết</th>
                </tr>
              </thead>

              <tbody v-if="isDesktop" class="fade-in">
                <tr v-for="order in orders" :key="order.id">
                  <td>#{{ order.id }}</td>
                  <td>{{ formatDate(order.order_time || order.reservations_time) }}</td>
                  <td>{{ formatNumber(order.total_price) }}VND</td>
                  <td>
                    <span>
                      {{ order.order_status || order.order_reservation_time }}
                    </span>
                  </td>
                  <td>
                    <router-link :to="{ name: 'history-order-detail', params: { id: order.id } }"
                      class="btn btn-outline-primary btn-sm">Xem</router-link>

                  </td>
                </tr>
              </tbody>

              <!-- Card cho mobile -->
              <tbody v-else class="fade-in">
                <tr v-for="order in orders" :key="order.id">
                  <td colspan="5">
                    <div class="card mb-3 text-start shadow-sm">
                      <div class="card-body text-dark">
                        <h6 class="card-title"><strong>Mã đơn:</strong> #{{ order.id }}</h6>
                        <div class="mb-1"><strong>Ngày đặt:</strong> {{ formatDate(order.order_time ||
                          order.reservations_time) }}</div>
                        <div class="mb-1"><strong>Tổng tiền:</strong> {{ formatNumber(order.total_price) }}VND</div>
                        <div class="mb-2">
                          <strong>Trạng thái:</strong>
                          <span>
                            {{ order.order_status || order.order_reservation_time }}
                          </span>
                        </div>
                        <router-link :to="{ name: 'history-order-detail' }"
                          class="btn btn-outline-primary btn-sm">Xem</router-link>

                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { User } from '@/stores/user'
import { Info } from '@/stores/info-order-reservation';
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios';

export default {
  setup() {
    const userData = localStorage.getItem('user');
    const userId = userData ? JSON.parse(userData).id : null;
    // console.log('User ID:', userId);
    const orders = ref([])
    console.log("Thông tin đơn hàng: ".orders)
    const isDesktop = ref(window.innerWidth >= 768)

    const handleResize = () => {
      isDesktop.value = window.innerWidth >= 768
    }

    const {
      form,
      getInitial,
      handleLogout,
      handleImageUpload,
      primaryColor,
    } = User.setup()

    const {
      formatNumber,
      formatDate,

    } = Info.setup()

    const getOrderByUser = async () => {
      console.log('Đang gọi API lấy lịch sử đơn hàng...');
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/order-history-info/${userId}`)
        orders.value = res.data.orders || [];
        console.log('Đơn hàng:', orders.value);
      } catch (error) {
        console.error('Lỗi khi lấy đơn hàng:', error)
      }
    }





    onMounted(() => {
      getOrderByUser()
      window.addEventListener('resize', handleResize)
      // console.log('Lịch sử đơn hàng:', info)
    });
    onBeforeUnmount(() => {
      window.removeEventListener('resize', handleResize)
    })


    return {
      getInitial,
      handleLogout,
      handleImageUpload,
      primaryColor,
      form,
      orders,
      formatDate,
      isDesktop,
      formatNumber,
    }
  }

}

</script>
<style scoped>
.border-custom {
  border: 1px solid #ca111f;
}

.avatar-wrapper {
  width: 100%;
  max-width: 120px;
  /* nhỏ hơn 150px để vừa iPad */
  aspect-ratio: 1/1;
  position: relative;
  margin-left: auto;
  margin-right: auto;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 3px solid #ca111f;
  display: block;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
  cursor: pointer;
}

.avatar-wrapper:hover .avatar-overlay {
  opacity: 1;
}

.avatar-placeholder {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-color: #e0e0e0;
  color: #ca111f;
  font-size: 36px;
  font-weight: bold;
  border: 3px solid #ca111f;
}

.avatar-img,
.avatar-placeholder {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 3px solid #ca111f;
  display: block;
}

.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}

button:disabled {
  opacity: 0.5;
  pointer-events: none;
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
  .card {
    text-align: center;
  }

  .avatar-wrapper {
    max-width: 100px;
  }

  .avatar-placeholder {
    font-size: 28px;
  }

  .d-md-table-row-group {
    display: table-row-group !important;
  }
}
</style>
