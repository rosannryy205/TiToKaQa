<template>
  <!-- Main Content -->
  <!-- <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div> -->
  <div class="col-12 col-md-8 col-lg-9">
    <h4 class="fw-bold mb-4">Đơn hàng của tôi</h4>
    <!-- Tabs -->
    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4">
      <div v-for="tab in tabs" :key="tab" :class="['tab-item', { active: activeTab === tab }]" @click="setActive(tab)">
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
                <router-link :to="{ name: 'history-order-detail', params: { id: order.id } }"
                  class="btn btn-outline-primary btn-sm">Xem</router-link>
                <button v-if="order.order_status ==='Hoàn thành'" class="btn btn-outline-primary btn-sm ms-1" @click="reOrder(order.id)">
                  Đặt lại
                </button>
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
              <router-link :to="{ name: 'history-order-detail', params: { id: order.id } }"
                class="btn btn-outline-primary btn-sm">Xem</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- No Orders -->
    <div v-else class="text-center mt-5">
      <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center"
        style="width: 80px; height: 80px">
        <i class="bi bi-receipt fs-2 text-muted"></i>
      </div>
      <p class="text-muted mt-3">Bạn chưa có đơn hàng nào.</p>
    </div>
  </div>
</template>
<script>
import { User } from "@/stores/user";
import { Info } from "@/stores/info-order-reservation";
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router';

export default {
  setup() {
    const userData = localStorage.getItem("user");
    const userId = userData ? JSON.parse(userData).id : null;
    // console.log('User ID:', userId);
    const loading = ref(true);
    const isLoading = ref(false);
    const orders = ref([]);
    const isDesktop = ref(window.innerWidth >= 768);

    const handleResize = () => {
      isDesktop.value = window.innerWidth >= 768;
    };

    const tabs = [
      "Tất cả đơn",
      "Chờ xác nhận",
      "Đang xử lý",
      "Đang giao hàng",
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
        case "Chờ xác nhận":
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
      avatarUrl,
    } = User.setup();



    const { formatNumber, formatDate } = Info.setup();

    const getOrderByUser = async () => {
      console.log("Đang gọi API lấy lịch sử đơn hàng...");
      try {
        const res = await axios.get(
          `http://127.0.0.1:8000/api/order-history-info`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        }
        );
        orders.value = res.data.orders || [];
        console.log("Đơn hàng:", orders.value);
      } catch (error) {
        console.error("Lỗi khi lấy đơn hàng:", error);
      } finally {
        loading.value = false; // Ẩn spinner sau khi hoàn tất API
      }
    };


    const reOrder = async (orderId) => {
      const result = await Swal.fire({
        title: 'Xác nhận đặt lại?',
        text: 'Bạn có chắc muốn đặt lại đơn hàng này?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ca111f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đặt lại',
        cancelButtonText: 'Hủy',
      });

      if (!result.isConfirmed) return;

      isLoading.value = true;

      try {
        const res = await axios.post(`http://127.0.0.1:8000/api/reorder/${orderId}`, {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          }
        });

        console.log(" RES:", res);
        console.log(" DATA:", res.data);

        if (res.data.status === true) {
          Swal.fire({
            toast: true,
            position: 'top-end', // Góc trên bên phải
            icon: 'success',
            title: 'Đặt lại đơn hàng thành công!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
          });

          await getOrderByUser();
          return;
        }

        // Trường hợp response không lỗi HTTP nhưng không đúng định dạng mong đợi
        let errorMsg = res.data.message || 'Không thể đặt lại đơn hàng.';
        if (res.data.error) errorMsg += `\n\n${res.data.error}`;
        if (res.data.errors) {
          const errorList = Object.values(res.data.errors).flat().join('\n');
          errorMsg += `\n\n${errorList}`;
        }
        Swal.fire('Lỗi', errorMsg, 'error');

      } catch (err) {
        console.log(" Axios bị lỗi:");
        console.log(err); // log toàn bộ object lỗi
        let errorMsg = err.response?.data?.message || 'Lỗi không xác định từ máy chủ.';
        const errorList = err.response?.data?.errors
          ? Object.values(err.response.data.errors).flat().join('\n')
          : err.response?.data?.error || '';
        if (errorList) {
          errorMsg += `\n\n${errorList}`;
        }
        Swal.fire('Lỗi', errorMsg, 'error');
      }
      finally {
        isLoading.value = false;
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
      getOrderByUser,
      handleLogout,
      handleImageUpload,
      primaryColor,
      form,
      orders,
      formatDate,
      isDesktop,
      formatNumber,
      reOrder,
      tabs,
      activeTab,
      setActive,
      filteredOrders,
      loading,
      getStatusBadge,
      avatarUrl,
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
  flex: 0 0 auto;
  /* QUAN TRỌNG: không co lại */
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
