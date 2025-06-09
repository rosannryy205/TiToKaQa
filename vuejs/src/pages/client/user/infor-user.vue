<template>
  <div
    v-if="loading"
    class="d-flex justify-content-center align-items-center"
    style="min-height: 50vh"
  >
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
            <template v-if="form && form.avatar">
              <img :src="form.avatar" alt="Avatar" class="avatar-circle" />
            </template>
            <template v-else>
              <div
                class="avatar-circle border-custom d-flex justify-content-center align-items-center"
              >
                {{ getInitial(form?.fullname) || getInitial(form?.username) }}
              </div>
            </template>

            <div class="ms-md-4 mt-3 mt-md-0 text-center text-md-start">
              <h6 class="fw-bold mb-2">{{ form.fullname || form.username }}</h6>
              <a
                href="#"
                @click="handleLogout"
                class="list-group-item-action link-danger small d-flex align-items-center justify-content-center justify-content-md-start gap-1 mt-2"
              >
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
              </a>
            </div>
          </div>

          <ul class="list-group list-group-flush">
            <router-link to="/update-user" class="text-decoration-none text-dark">
              <li
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div>
                  <div class="fw-bold">Thông tin tài khoản</div>
                  <div class="small text-muted">Cập nhật thông tin</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>

            <router-link to="/infor-user" class="text-decoration-none text-dark">
              <li
                class="list-group-item d-flex justify-content-between align-items-center"
              >
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
        <!-- Tabs -->
        <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4">
          <div
            v-for="tab in tabs"
            :key="tab"
            :class="['tab-item', { active: activeTab === tab }]"
            @click="setActive(tab)"
          >
            {{ tab }}
          </div>
        </div>

        <!-- Order Table -->
        <div v-if="orders?.length > 0" class="card shadow border-0 p-4">
          <div class="table-responsive d-none d-md-block">
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
                  <td>{{ formatNumber(order.total_price) }} VND</td>
                  <td>{{ order.order_status || order.order_reservation_time }}</td>
                  <td>
                    <router-link
                      :to="{ name: 'history-order-detail', params: { id: order.id } }"
                      class="btn btn-outline-primary btn-sm"
                      >Xem</router-link
                    >
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="d-md-none">
            <div class="col-12 mb-3" v-for="order in filteredOrders" :key="order.id">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h6><strong>Mã đơn:</strong> #{{ order.id }}</h6>
                  <p>
                    <strong>Ngày đặt:</strong>
                    {{ formatDate(order.order_time || order.reservations_time) }}
                  </p>
                  <p>
                    <strong>Tổng tiền:</strong> {{ formatNumber(order.total_price) }} VND
                  </p>
                  <p>
                    <strong>Trạng thái:</strong>
                    {{ order.order_status || order.order_reservation_time }}
                  </p>
                  <router-link
                    :to="{ name: 'history-order-detail', params: { id: order.id } }"
                    class="btn btn-outline-primary btn-sm"
                    >Xem</router-link
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Orders -->
        <div v-else class="text-center mt-5">
          <div
            class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center"
            style="width: 80px; height: 80px"
          >
            <i class="bi bi-receipt fs-2 text-muted"></i>
          </div>
          <p class="text-muted mt-3">Bạn chưa có đơn hàng nào.</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { User } from "@/stores/user";
import { Info } from "@/stores/info-order-reservation";
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";

export default {
  setup() {
    const userData = localStorage.getItem("user");
    const userId = userData ? JSON.parse(userData).id : null;
    // console.log('User ID:', userId);
    const loading = ref(true);
    const orders = ref([]);
    const isDesktop = ref(window.innerWidth >= 768);

    const handleResize = () => {
      isDesktop.value = window.innerWidth >= 768;
    };

    const tabs = [
      "Tất cả đơn",
      "Chờ thanh toán",
      "Đang xử lý",
      "Đang vận chuyển",
      "Đã giao",
      "Đã hủy",
    ];

    const activeTab = ref("Tất cả đơn");

    const setActive = (tab) => {
      activeTab.value = tab;
    };

    const filteredOrders = computed(() => {
      if (activeTab.value === "Tất cả đơn") return orders.value;

      return orders.value.filter((order) => order.order_status === activeTab.value);
    });

    const getStatusBadge = (status) => {
      switch (status) {
        case "Chờ thanh toán":
          return "bg-warning text-dark";
        case "Đã giao":
          return "bg-success";
        case "Đã hủy":
          return "bg-secondary";
        default:
          return "bg-light text-dark";
      }
    };

    const {
      form,
      getInitial,
      handleLogout,
      handleImageUpload,
      primaryColor,
    } = User.setup();



    const { formatNumber, formatDate } = Info.setup();

    const getOrderByUser = async () => {
      console.log("Đang gọi API lấy lịch sử đơn hàng...");
      try {
        const res = await axios.get(
          `http://127.0.0.1:8000/api/order-history-info/${userId}`
        );
        orders.value = res.data.orders || [];
        console.log("Đơn hàng:", orders.value);
      } catch (error) {
        console.error("Lỗi khi lấy đơn hàng:", error);
      } finally {
        loading.value = false; // Ẩn spinner sau khi hoàn tất API
      }
    };

    onMounted(() => {
      getOrderByUser();
      window.addEventListener("resize", handleResize);
      handleResize();
      // console.log('Lịch sử đơn hàng:', info)
    });
    onBeforeUnmount(() => {
      window.removeEventListener("resize", handleResize);
    });

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
      getStatusBadge,
    };
  },
};
</script>
<style scoped>
.order-tabs {
   -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
  display: none;
}

.tab-item {
  flex: 0 0 auto; /* QUAN TRỌNG: không co lại */
  padding: 0.6rem 1.2rem;
  white-space: nowrap;
  border-radius: 8px;
  font-weight: 500;
  font-size: 1rem;
  letter-spacing: 0.5px;
  color: #6c757d;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: #f8f9fa;
}

.tab-item.active {
  border-bottom-color: #ca111f;
  color: #ca111f;
  background-color: #fff;
  font-weight: 600;
}

.list-group-item:hover {
  background-color: #cdcdcd;
  border-radius: 20px;
  cursor: pointer;
}

li.list-group-item {
  border: none !important;
}

.avatar-circle {
  width: clamp(80px, 25vw, 100px);
  height: clamp(80px, 25vw, 100px);
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  background-color: #f8f9fa;
}

.border-custom {
  border: 2px solid #ca111f;
}

.fade-in {
  animation: fadeIn 0.4s ease-in-out;
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
  .tab-item {
    padding: 0.75rem 1.2rem;
    font-size: 0.95rem;
    letter-spacing: 0.4px;
  }
}
</style>
