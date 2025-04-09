<template>
  <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div v-else-if="user" class="container mt-5 fade-in">
    <div class="row g-4">
      <!-- Sidebar -->
      <div class="col-12 col-md-3">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="avatar-wrapper mx-auto mb-3 d-flex justify-content-center align-items-center">

            <template v-if="form && form.avatar">
              <img :src="form.avatar" alt="Avatar" class="rounded-circle avatar-img" />
            </template>
            <template v-else>
              <div class="avatar-placeholder d-flex justify-content-center align-items-center">
                {{ getInitial(form?.fullname) }}
              </div>
            </template>

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
            <a href="#" class="list-group-item list-group-item-action" @click="handleLogout">
              <span class="text-danger">Đăng xuất</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="col-12 col-md-9">
        <div class="card shadow border-0 p-4">
          <h4 class="mb-4 text-center" :style="{ color: primaryColor }">Lịch sử đơn hàng</h4>
          <div class="table-responsive">
            <table class="table table-hover text-center">
              <thead v-if="isDesktop" class="table-light">
                <tr>
                  <th>Mã đơn</th>
                  <th>Ngày đặt</th>
                  <th>Tổng tiền</th>
                  <th>Trạng thái</th>
                  <th>Chi tiết</th>
                </tr>
              </thead>
              <!-- Table: Desktop và tablet -->
              <tbody v-if="isDesktop" class="fade-in">
                <template v-if="orders.length > 0">
                  <tr v-for="order in orders" :key="order.id">
                    <td>#{{ order.id }}</td>
                    <td>{{ formatDate(order.created_at) }}</td>
                    <td>{{ formatCurrency(order.total) }}</td>
                    <td>
                      <span :class="getStatusClass(order.status)">
                        {{ getStatusText(order.status) }}
                      </span>
                    </td>
                    <td>
                      <button class="me-2 btn btn-outline-secondary btn-sm" @click="openOrderModal(order)">
                        Xem
                      </button>
                      <button class="me-2 btn btn-outline-danger btn-sm" :disabled="isCompleted(order.status)"
                        @click="openCancelModal(order)">
                        Hủy đơn
                      </button>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td colspan="5" class="text-muted text-center py-4">
                    Bạn chưa có đơn hàng nào.
                  </td>
                </tr>
              </tbody>




              <!-- Card: Mobile -->
              <tbody v-if="!isDesktop" class="fade-in">
                <template v-if="orders.length > 0">
                  <tr v-for="order in orders" :key="order.id">
                    <td colspan="5">
                      <div class="card mb-3 text-start shadow-sm">
                        <div class="card-body text-dark">
                          <h6 class="card-title"><strong>Mã đơn:</strong> #{{ order.id }}</h6>
                          <div class="mb-1"><strong>Ngày đặt:</strong> {{ formatDate(order.created_at) }}</div>
                          <div class="mb-1"><strong>Tổng tiền:</strong> {{ formatCurrency(order.total) }}</div>
                          <div class="mb-2">
                            <strong>Trạng thái:</strong>
                            <span :class="getStatusClass(order.status)">
                              {{ getStatusText(order.status) }}
                            </span>
                          </div>
                          <button class="btn btn-outline-primary btn-sm" @click="openOrderModal(order)">
                            Xem
                          </button>
                          <button class="me-2 btn btn-outline-danger btn-sm" :disabled="isCompleted(order.status)"
                            @click="openCancelModal(order)">
                            Hủy đơn
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td colspan="5" class="text-muted text-center py-4">
                    Bạn chưa có đơn hàng nào.
                  </td>
                </tr>
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Nếu chưa đăng nhập -->
  <div v-else class="container d-flex align-items-center justify-content-center" style="min-height: 50vh;">
    <h5 class="mb-3">Vui lòng đăng nhập để xem thông tin tài khoản.</h5>
  </div>


  <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold text-center w-100" id="orderDetailModalLabel">
            Chi tiết đơn hàng #{{ selectedOrder?.id }}
          </h5>
        </div>
        <div class="modal-body px-4 py-3" v-if="selectedOrder">
          <div class="mb-1"><strong>Ngày đặt:</strong> {{ formatDate(selectedOrder.created_at) }}</div>
          <div class="mb-1"><strong>Tổng tiền:</strong> {{ formatCurrency(selectedOrder.total) }}</div>
          <div class="mb-2"><strong>Trạng thái:</strong>
            <span :class="getStatusClass(selectedOrder.status)">
              {{ getStatusText(selectedOrder.status) }}
            </span>
          </div>
          <div class="mb-3">
            <strong>Sản phẩm:</strong>
            <div v-if="selectedOrder.order_items && selectedOrder.order_items.length > 0">
              <div v-for="item in selectedOrder.order_items" :key="item.id"
                class="d-flex align-items-center mb-2 border-bottom pb-2">
                <img :src="item.image" alt="ảnh sản phẩm" class="me-2"
                  style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                <div>
                  <div><strong>{{ item.name }}</strong></div>
                  <div>SL: {{ item.quantity }} × {{ formatCurrency(item.price) }}</div>
                  <div class="text-muted small">Thành tiền: {{ formatCurrency(item.price * item.quantity) }}</div>
                </div>
              </div>
            </div>
            <div v-else class="text-muted">Không có sản phẩm nào.</div>
          </div>

        </div>
      </div>
    </div>
  </div>


</template>

<script>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'


export default {
  setup() {
    const successMessage = ref('')
    const primaryColor = '#ca111f';
    const user = ref(null)
    const form = ref({
      fullname: '',
      email: '',
      phone: '',
      address: '',
      avatar: ''
    })
    const user1 = JSON.parse(localStorage.getItem('user')) || null
    const token = localStorage.getItem('token')

    const personally = async (userId) => {
      try {
        const res = await axios.get(`http://localhost:8000/api/user/${userId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          }
        })
        user.value = res.data
        form.value = {
          fullname: res.data.fullname,
          email: res.data.email,
          phone: res.data.phone || '',
          address: res.data.address || '',
          avatar: res.data.avatar || ''
        }
      } catch (error) {
        console.error('Không lấy được thông tin người dùng', error)
      }
    }

    const orders = ref([]) // ✅ Đảm bảo luôn là mảng
    const fetchOrders = async () => {
      try {
        const res = await axios.get(`http://localhost:8000/api/orders`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        if (Array.isArray(res.data) && res.data.length > 0) {
          orders.value = res.data
        } else {
          // fallback nếu API trả về mảng rỗng
          orders.value = [
            {
              id: 101,
              created_at: '2025-04-01',
              total: 350000,
              status: 'pending',
              note: "Giao trước 18h",
              payment_method: "COD",
              order_items: [
                {
                  id: 1,
                  name: "Áo sơ mi trắng",
                  price: 150000,
                  quantity: 2,
                  image: "https://via.placeholder.com/60"
                },
                {
                  id: 2,
                  name: "Quần jean",
                  price: 50000,
                  quantity: 1,
                  image: "https://via.placeholder.com/60"
                }
              ]
            },
            {
              id: 102,
              created_at: '2025-04-02',
              total: 520000,
              status: 'completed',
              note: "Giao tận nơi",
              payment_method: "Chuyển khoản",
              order_items: []
            }
          ]

        }
      } catch (error) {
        console.error("Không lấy được đơn hàng:", error)
        // fallback nếu lỗi kết nối hoặc server
        // Này là dữ liệu mẫu
        orders.value = [
          {
            id: 101,
            created_at: '2025-04-01',
            total: 350000,
            status: 'pending',
            note: "Đặt bàn lúc 16:00 giờ",
            payment_method: "COD",
            order_items: [
              {
                id: 1,
                name: "Mì cay",
                price: 150000,
                quantity: 2,
                image: "https://via.placeholder.com/60"
              },
              {
                id: 2,
                name: "Mì hảo hảo",
                price: 50000,
                quantity: 1,
                image: "https://via.placeholder.com/60"
              }
            ]
          },
          {
            id: 102,
            created_at: '2025-04-02',
            total: 520000,
            status: 'completed',
            note: "Giao tận nơi",
            payment_method: "Chuyển khoản",
            order_items: [
              {
                id: 1,
                name: "Mì hảo hảo",
                price: 50000,
                quantity: 1,
                image: "https://via.placeholder.com/60"
              }
            ]
          }
        ]

      }
    }

    const isCompleted = (status) => {
      return ['completed', 'cancelled'].includes(status);
    }
    const getStatusText = (status) => {
      switch (status) {
        case 'processing': return 'Đang xử lý';
        case 'shipped': return 'Đang giao';
        case 'completed': return 'Hoàn tất';
        case 'cancelled': return 'Đã hủy';
        default: return 'Không rõ';
      }
    }

    const getStatusClass = (status) => {
      switch (status) {
        case 'processing': return 'text-warning';
        case 'shipped': return 'text-primary';
        case 'completed': return 'text-success';
        case 'cancelled': return 'text-danger';
        default: return '';
      }
    }
    const formatDate = (dateStr) => {
      const date = new Date(dateStr)
      return date.toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }

    const formatCurrency = (amount) => {
      return amount.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND'
      })
    }


    const isLoggedIn = ref(false);

    //  Đăng xuất
    const handleLogout = async () => {
      try {
        await axios.post('http://127.0.0.1:8000/api/logout', null, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });


        localStorage.removeItem('user');
        localStorage.removeItem('token');
        user1.value = null;
        isLoggedIn.value = false;

        alert('Đăng xuất thành công!');
        window.location.href = '/';
      } catch (error) {
        console.error('Lỗi đăng xuất:', error);
        alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
      }
    };

    const getInitial = (fullname) => {
      if (!fullname) return '?'
      return fullname.trim().charAt(0).toUpperCase()
    }
    const loading = ref(true);
    const isDesktop = ref(window.innerWidth >= 768)

    const handleResize = () => {
      isDesktop.value = window.innerWidth >= 768
    }

    const selectedOrder = ref(null);

    const openOrderModal = (order) => {
      selectedOrder.value = order;
      const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
      modal.show();
    };
    const openCancelOrder = (order) => {
      // Mở modal xác nhận hủy, hoặc sau này gọi API
      console.log("Hủy đơn:", order)
    }


    onMounted(() => {
      if (user1 && user1.id && token) {
        personally(user1.id)
          .then(() => {
            isLoggedIn.value = !!user.value;
            fetchOrders();
          })
          .catch(() => {
            isLoggedIn.value = false;
          })
          .finally(() => {
            setTimeout(() => {
              loading.value = false;
            }, 400);
          });
        window.addEventListener('resize', handleResize)
      } else {
        console.warn('Không tìm thấy user/token trong localStorage');
        isLoggedIn.value = false;
        loading.value = false;
      }
    });
    onBeforeUnmount(() => {
      window.removeEventListener('resize', handleResize)
    })

    return {
      form,
      user,
      successMessage,
      handleLogout,
      isLoggedIn,
      getInitial,
      loading,
      primaryColor,
      orders,
      getStatusText,
      getStatusClass,
      formatDate,
      formatCurrency,
      isDesktop,
      selectedOrder,
      openOrderModal,
      openCancelOrder,
      isCompleted
    }

  },
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
