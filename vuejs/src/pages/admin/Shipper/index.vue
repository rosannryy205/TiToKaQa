<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div v-if="isLoading" class="isLoading-overlay">
            <div class="spinner-border text-danger" role="status">
              <span class="visually-hidden">isLoading...</span>
            </div>
          </div>
          <div class="container py-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
              <h2 class="fw-bold fs-4 mb-0 text-primary d-flex align-items-center">
<<<<<<< HEAD
                <i class="bi bi-truck-front-fill me-2"></i>Đơn hàng đang giao
=======
                Đơn hàng đang giao
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
              </h2>
              <router-link to="/admin/select_order" class="ms-auto">
                <button class="btn btn-ship fw-semibold">
                  <i class="bi bi-list-check me-1"></i> Chọn đơn giao
                </button>
              </router-link>
            </div>


            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
              <div class="col" v-for="order in orders" :key="order.id">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body d-flex flex-column">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-2  w-100">
                      <h5 class="card-title mb-0">
                        <i class="bi bi-receipt-cutoff text-info me-2"></i>#Mã đơn hàng: {{ order.id }}
                      </h5>
                      <span :class="statusBadgeClass(order.order_status)">
                        <i :class="statusIcon(order.order_status)" class="me-1"></i>{{ statusLabel(order.order_status) }}
                      </span>
                    </div>

                    <!-- Customer Info -->
                    <ul class="list-unstyled small text-muted mb-3">
                      <li><i class="bi bi-person-circle me-2 text-secondary"></i><strong>Khách:</strong> {{ order.guest_name
                        }}</li>
                      <li><i class="bi bi-telephone-fill me-2 text-secondary"></i><strong>SĐT:</strong> {{ order.guest_phone
                        }}
                      </li>
                      <li class="d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill me-2 text-secondary"></i>
                        <div class="text-truncate" style="max-width: 100%" :title="order.guest_address">
                          <strong>Địa chỉ:</strong> {{ order.guest_address }}
                        </div>
                      </li>
<<<<<<< HEAD

                      <li><i class="bi bi-clock-fill me-2 text-secondary"></i><strong>Thời gian:</strong> {{ order.order_time
                        }}</li>
=======
                      <li>
                        <i class="bi bi-clock-fill me-2 text-secondary"></i><strong>Thời gian:</strong> {{ order.order_time}}
                      </li>
                      <li>
                      <i class="bi bi-chat-left-text-fill me-2"></i><strong>Ghi chú:</strong> {{ order.note || 'Không có' }}
                      </li>
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
                    </ul>

                    <!-- Order Items -->
                    <div class="mb-3 flex-grow-1 d-flex flex-column w-100">
                      <strong class="text-dark"><i class="bi bi-list-ul me-2"></i>Chi tiết đơn hàng:</strong>

                      <div class="order-items-scroll mt-2 flex-grow-1 w-100">
                        <ul class="list-group list-group-flush small w-100" v-if="order.details && order.details.length > 0">
                          <li class="list-group-item px-0" v-for="food in order.details" :key="food.id">
                            <div class="d-flex justify-content-between">
                              <div>
                                <div>🍽️ {{ food.food_name }}</div>
                                <div class="text-muted small">Số lượng: {{ food.quantity }}</div>
                              </div>
                              <span class="fw-semibold">{{ formatCurrency(food.price) }}</span>
                            </div>
                            <ul v-if="food.toppings && food.toppings.length > 0" class="ps-4 mt-1 mb-0">
                              <li v-for="(topping, tIndex) in food.toppings" :key="tIndex"
                                class="text-muted d-flex justify-content-between">
                                <span><i class="bi bi-plus-circle me-1 text-success"></i>{{ topping.topping_name }}</span>
                                <span class="text-success">{{ formatCurrency(topping.price || 0) }}</span>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>

                      <div class="text-end fw-bold mt-2 text-primary w-100">
                        Tổng: {{ formatCurrency(order.total_price) }}
                      </div>
                    </div>




                    <!-- Status Select -->
                    <div class="mb-3 w-100">
                      <label class="form-label fw-semibold">
                        <i class="bi bi-shield-check me-1 text-dark"></i>Trạng thái giao hàng:
                      </label>
                      <input type="text" class="form-control bg-light" :value="order.order_status" readonly />
                    </div>


<<<<<<< HEAD
                    <!-- Note -->
                    <p class="card-text text-secondary">
                      <i class="bi bi-chat-left-text-fill me-2"></i><strong>Ghi chú:</strong> {{ order.note || 'Không có' }}
                    </p>
=======

>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f

                    <button class="btn btn-outline-danger-custom mt-3" @click="goToTracking(order.id)">
                      Xem hành trình giao hàng
                    </button>

                  </div>
                </div>
              </div>
            </div>

            <div v-if="orders.length === 0" class="text-center text-muted mt-5">
              <i class="bi bi-emoji-frown fs-1 text-secondary"></i>
              <p class="mt-3">Hiện tại bạn chưa được giao đơn hàng nào.</p>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatDate } from '@fullcalendar/core'
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
<<<<<<< HEAD

=======
const API_URL = "http://127.0.0.1:8000/api"
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
const router = useRouter()
const goToTracking = (orderId) => {
  router.push(`/admin/delivery_map/${orderId}`)
}

const isLoading = ref(false)
const orders = ref([])

const getAllOrderByUser = async () => {
  isLoading.value = true
  try {
<<<<<<< HEAD
    const response = await axios.get('http://127.0.0.1:8000/api/shipper/orders', {
=======
    const response = await axios.get(`${API_URL}/shipper/orders`, {
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    orders.value = Array.isArray(response.data.orders) ? response.data.orders : []

    console.log('Orders:', response.data.orders)
  } catch (error) {
    isLoading.value = false
    console.log('Lỗi:', error);
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  getAllOrderByUser();
})


function formatCurrency(value) {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(value)
}

function statusBadgeClass(status) {
  switch (status) {
    case 'delivered': return 'badge bg-success rounded-pill'
    case 'failed': return 'badge bg-danger rounded-pill'
    default: return 'badge bg-warning text-dark rounded-pill'
  }
}

function statusLabel(status) {
  switch (status) {
    case 'delivered': return 'Giao thành công'
    case 'failed': return 'Giao thất bại'
    default: return 'Đang giao'
  }
}

function statusIcon(status) {
  switch (status) {
    case 'delivered': return 'bi bi-check-circle-fill'
    case 'failed': return 'bi bi-x-circle-fill'
    default: return 'bi bi-hourglass-split'
  }
}
</script>

<style scoped>
.card:hover {
  transform: scale(1.01);
  transition: transform 0.2s ease-in-out;
}

.order-items-scroll {
  max-height: 200px;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 4px;
  width: 100% !important;
  flex-grow: 1;
}

.list-group,
.list-group-item {
  width: 100%;
}

.mb-3.flex-grow-1.d-flex.flex-column {
  width: 100%;
}


.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-ship {
  background-color: #C92C3C;
  color: #fff;
}

@media (max-width: 576px) {
  .btn-ship {
    width: auto;
    padding: 8px 12px;
    font-size: 0.9rem;
  }

  .d-flex.flex-wrap.justify-content-between.align-items-center {
    flex-direction: row;
    align-items: center;
  }

  .card-body>button {
    width: 100%;
    padding: 10px;
    font-size: 0.9rem;
  }

  .card-title {
    font-size: 1rem;
  }

  .list-unstyled small,
  .card .list-group-item {
    font-size: 0.85rem;
  }

  .order-items-scroll {
    max-height: 150px;
  }

  .text-truncate[title] {
    max-width: 100% !important;
    white-space: normal;
    overflow: visible;
    text-overflow: initial;
  }
}

button.btn-outline-danger-custom {
  color: #C92C3C !important;
  border: 1px solid #C92C3C !important;
  background-color: transparent !important;
  font-weight: 600;
  border-radius: 40px;
  padding: 12px 20px;
  width: 100%;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

button.btn-outline-danger-custom:hover {
  background-color: #C92C3C !important;
  color: white !important;
  border-color: #C92C3C !important;
}
</style>
