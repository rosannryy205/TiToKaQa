<template>
  <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div v-else class="container mt-5 fade-in">
    <div class="row g-4">
      <!-- Sidebar -->
      <div class="col-12 col-md-4 col-lg-3 mb-4 mb-md-0" style="max-height: 300px;">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="d-flex  align-items-center mb-3 mx-3">
            <template v-if="form && form.avatar">
              <img :src="form.avatar" alt="Avatar"
                class="rounded-circle avatar-img shadow p-3 mb-5 bg-body-tertiary rounded" />
            </template>
            <template v-else>
              <div class="avatar-placeholder d-flex justify-content-center align-items-center">
                {{ getInitial(form?.fullname) || getInitial(form?.username) }}
              </div>
            </template>

            <div class="ms-4 text-center text-md-start">
              <h6 class="mt-2 mb-3 fw-bold">{{ form.fullname || form.username }}</h6>
              <a href="#" @click="handleLogout"
                class="list-group-item-action link-danger small d-flex align-items-center gap-1 mt-2">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
              </a>
            </div>

          </div>


          <!-- <div class="bg-light rounded-3 p-3 text-center mb-3">
            <div class="fw-bold">POP MART MEMBER</div>
            <div class="d-flex justify-content-around mt-2">
              <div>
                <div class="fw-bold fs-5">50</div>
                <div class="text-muted small">Điểm</div>
              </div>
              <div>
                <div class="fw-bold fs-5">0</div>
                <div class="text-muted small">Coupons</div>
              </div>
            </div>
            <button class="btn btn-outline-dark btn-sm mt-3 rounded-pill px-4">Rewards</button>
          </div> -->

          <ul class="list-group list-group-flush">
            <router-link to="/update-user" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold">Thông tin tài khoản</div>
                  <div class="small text-muted">Cập nhật thông tin</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>

            <router-link to="/infor-user" class="text-decoration-none text-dark">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold text-danger">Quản lý đơn hàng</div>
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
        <h4 class="fw-bold mb-4">Đơn hàng của tôi</h4>
        <!-- Có đơn hàng -->
        <div v-if="orders?.length > 0" class="card shadow border-0 p-4">
          <!-- Tabs -->
          <div class="order-tabs d-flex overflow-auto gap-3 mb-4">
            <div v-for="tab in tabs" :key="tab" :class="['tab-item', { active: activeTab === tab }]"
              @click="setActive(tab)">
              {{ tab }}
            </div>
          </div>




          <div class="table-responsive">
            <!--Desktop -->
            <div v-if="isDesktop">
              <table class="table table-hover text-center w-100">
                <thead class="table-light">
                  <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="order in filteredOrders" :key="order.id">
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
              </table>
            </div>
            <!--Mobile -->
            <div v-else class="fade-in">
              <div class="col-12 mb-3" v-for="order in orders" :key="order.id">
                <div class="card text-start shadow-sm">
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
                    <router-link :to="{ name: 'history-order-detail', params: { id: order.id } }"
                      class="btn btn-outline-primary btn-sm">Xem</router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Không có đơn hàng -->
        <div v-else>
          <div class="text-center mt-5">
            <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center"
              style="width: 80px; height: 80px;">
              <i class="bi bi-receipt fs-2 text-muted"></i>
            </div>
            <p class="text-muted mt-3">Bạn chưa có đơn hàng nào.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>
<script>
import { User } from '@/stores/user'
import { Info } from '@/stores/info-order-reservation';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import axios from 'axios';

export default {
  setup() {
    const userData = localStorage.getItem('user');
    const userId = userData ? JSON.parse(userData).id : null;
    // console.log('User ID:', userId);
    const loading = ref(true);
    const orders = ref([])
    const isDesktop = ref(window.innerWidth >= 768)

    const handleResize = () => {
      isDesktop.value = window.innerWidth >= 768
    }

    const tabs = [
      'Tất cả đơn',
      'Chờ thanh toán',
      'Đang xử lý',
      'Đang vận chuyển',
      'Đã giao',
      'Đã hủy'
    ];

    const activeTab = ref('Tất cả đơn');

    const setActive = (tab) => {
      activeTab.value = tab;
    };

    const filteredOrders = computed(() => {
      if (activeTab.value === 'Tất cả đơn') return orders.value;

      return orders.value.filter(order => order.order_status === activeTab.value);
    });

    const getStatusBadge = (status) => {
      switch (status) {
        case 'Chờ thanh toán': return 'bg-warning text-dark'
        case 'Đã giao': return 'bg-success'
        case 'Đã hủy': return 'bg-secondary'
        default: return 'bg-light text-dark'
      }
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
      formatDate
    } = Info.setup()


    const getOrderByUser = async () => {
      console.log('Đang gọi API lấy lịch sử đơn hàng...');
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/order-history-info/${userId}`)
        orders.value = res.data.orders || [];
        console.log('Đơn hàng:', orders.value);
      } catch (error) {
        console.error('Lỗi khi lấy đơn hàng:', error)
      } finally {
        loading.value = false; // Ẩn spinner sau khi hoàn tất API
      }
    }




    onMounted(() => {
      getOrderByUser()
      window.addEventListener('resize', handleResize)
      handleResize()
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

      tabs,
      activeTab,
      setActive,
      filteredOrders,
      loading,
      getStatusBadge
    }
  }

}

</script>
<style scoped>
.order-tabs {
  display: flex;
  justify-content: center; /* căn giữa các tab */
  align-items: center;
  gap: 1rem; /* khoảng cách giữa các tab */
  flex-wrap: wrap; /* nếu thiếu chỗ thì tự xuống dòng */
  margin-bottom: 1.5rem;
}


.tab-item {
  color: #6c757d;
  font-weight: bold;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: color 0.3s, border-bottom-color 0.3s;
  white-space: nowrap;
  border-radius: 4px;
}

.tab-item.active {
  border-bottom-color: #ca111f;
  color: #ca111f;
}

.tab-item.active {
  border-bottom-color: #ca111f;
  color: #ca111f;
  font-weight: bold;
}



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
  width: clamp(80px, 25vw, 150px);
  height: clamp(80px, 25vw, 150px);
  border-radius: 50%;
  background-color: #e0e0e0;
  color: #ca111f;
  font-size: 36px;
  font-weight: bold;
  border: 1px solid #ca111f;
}

.avatar-img,
.avatar-placeholder {
  width: clamp(70px, 20vw, 100px);
  height: clamp(70px, 20vw, 100px);
  object-fit: cover;
  border-radius: 50%;
  /* border: 1px solid #ca111f; */
  display: block;
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
  .avatar-wrapper {
    max-width: 100px;
  }

  .avatar-placeholder {
    font-size: 28px;
  }
}
</style>
