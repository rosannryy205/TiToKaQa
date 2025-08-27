<template>
  <!-- Main Content -->
  <div v-if="loading" class="isLoading-overlay d-flex justify-content-center align-items-center">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Đang xử lý...</span>
    </div>
  </div>
  <div class="col-12 col-md-8 col-lg-9">
    <h4 class="fw-bold mb-4">Đơn hàng đã mua</h4>
    <!-- Tabs -->
    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4">
      <div v-for="tab in tabs" :key="tab" :class="['tab-item', { active: activeTab === tab }]" @click="setActive(tab)">
        {{ tab }}
      </div>
    </div>

    <!-- Order Table -->
    <div v-if="orders?.length > 0" class="card shadow border-0 p-0">
      <!-- Desktop Table -->
      <div class="d-none d-md-block">
        <table class="table table-hover w-100 custom-borderless-table">
          <thead class="table-secondary">
            <tr>
              <th>Mã đơn</th>
              <th>Ngày đặt</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in paginatedOrders" :key="order.id">
              <td>#{{ order.id }}</td>
              <td>{{ formatDate(order.order_time || order.reservations_time) }}</td>
              <td>{{ formatNumber(order.total_price) }} VND</td>
              <td class="text-primary">{{ order.order_status || order.order_reservation_time }}</td>
              <td>
                <router-link :to="{ name: 'history-order-detail', params: { id: order.id } }"
                  class="btn btn-outline-primary btn-sm">Xem</router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>


      <!-- Mobile Cards -->
      <div class="d-md-none">
        <div class="col-12 mb-3" v-for="order in paginatedOrders" :key="order.id">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="mb-2"><strong>Mã đơn:</strong> #{{ order.id }}</h6>
              <div class="mb-1 small text-muted">
                <strong>Ngày đặt:</strong> {{ formatDate(order.order_time || order.reservations_time) }}
              </div>
              <div class="mb-1 small text-muted">
                <strong>Tổng tiền:</strong> {{ formatNumber(order.total_price) }} VND
              </div>
              <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
                <strong>Trạng thái:</strong>
                <span>
                  {{ order.order_status || order.order_reservation_time }}
                </span>
              </div>
              <router-link :to="{ name: 'history-order-detail', params: { id: order.id } }"
                class="btn btn-outline-primary btn-sm w-100">
                <i class="bi bi-eye"></i> Xem chi tiết
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center mt-3 w-100" v-if="totalPages > 1">
        <nav>
          <ul class="pagination">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button type="button" class="page-link" @click="goToPage(currentPage - 1)">«</button>
            </li>

            <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: currentPage === page }">
              <button type="button" class="page-link" @click="goToPage(page)">
                {{ page }}
              </button>
            </li>

            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <button type="button" class="page-link" @click="goToPage(currentPage + 1)">»</button>
            </li>
          </ul>
        </nav>
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
import { Info } from "@/stores/info-order-reservation";
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
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
      "Chờ xác nhận",
      "Đã xác nhận",
      "Đang xử lý",
      "Bắt đầu giao",
      "Giao thành công",
      "Đã hủy",
    ];

    const activeTab = ref("Chờ xác nhận");

    const setActive = (tab) => {
      activeTab.value = tab;
    };

    const filteredOrders = computed(() => {
      return orders.value.filter((order) => order.order_status === activeTab.value);
    });


    const { formatNumber, formatDate } = Info.setup();

    const getOrderByUser = async () => {
      console.log("Đang gọi API lấy lịch sử đơn hàng...");
      try {
        const res = await axios.get(
          `${API_URL}/order-history-info`, {
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
    const itemsPerPage = 6;
    const currentPage = ref(1);

    const paginatedOrders = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage;
      return filteredOrders.value.slice(start, start + itemsPerPage);
    });

    const totalPages = computed(() => {
      return Math.ceil(filteredOrders.value.length / itemsPerPage);
    });

    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
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
        const res = await axios.post(`${API_URL}/reorder/${orderId}`, {}, {
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
      orders,
      formatDate,
      isDesktop,
      formatNumber,
      reOrder,
      tabs,
      activeTab,
      setActive,
      filteredOrders,

      paginatedOrders,
      currentPage,
      totalPages,
      goToPage,
      loading,
    };
  },
};
</script>
<style scoped>
.order-tabs {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  white-space: nowrap;
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
  border-bottom-color: #c92c3c;
  color: #c92c3c;
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

.btn-outline-primary {
  color: #c92c3c;
  border-color: #c92c3c;
}

.btn-outline-primary:hover {
  background-color: #c92c3c;
  color: #fff;
  /* border: none; */
}

.table-secondary th {
  font-weight: bold;
  font-size: 15px;
}

.table-hover tbody tr:hover {
  background-color: #f0f0f0;
  /* hoặc màu xám nhẹ tùy chọn */
}

.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}

.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.7);
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
