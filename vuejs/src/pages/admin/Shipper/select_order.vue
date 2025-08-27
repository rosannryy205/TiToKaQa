<template v-if="hasPermission('edit_shipper')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h3 class="fw-bold mb-0 text-primary">
                Chọn đơn hàng
              </h3>
              <span class="badge bg-secondary">
                Đã chọn: {{ selectedIds.length }}/3
              </span>
            </div>

            <div v-if="isLoading" class="text-center my-5">
              <div class="spinner-border text-primary" role="status"></div>
            </div>

            <div v-else-if="orders.length === 0" class="d-flex justify-content-center align-items-center"
              style="height: 400px;">
              <p class="text-muted">Không có đơn hàng nào để giao</p>
            </div>


            <div v-else>
              <div class="row g-4">
                <div class="col-md-4" v-for="order in orders" :key="order.id">
                  <div class="card order-card h-100 shadow-sm" :class="{ selected: selectedIds.includes(order.id) }">
                    <div class="card-body d-flex flex-column">
                      <div class="d-flex justify-content-between align-items-start mb-2 w-100">
                        <h5 class="fw-bold text-dark mb-0">#{{ order.id }}</h5>
                        <i class="bi"
                          :class="selectedIds.includes(order.id) ? 'bi-check-circle-fill text-danger' : 'bi-circle text-muted'"
                          style="font-size: 1.5rem"></i>
                      </div>

                      <!-- Thông tin khách -->
                      <p class="mb-1 text-dark"><i class="bi bi-person me-1 text-secondary"></i> {{ order.guest_name }}
                      </p>
                      <p class="mb-1 text-dark"><i class="bi bi-telephone me-1 text-secondary"></i> {{ order.guest_phone
                        }}
                      </p>
                      <p class="mb-2 small text-muted">
                        <i class="bi bi-geo-alt me-1"></i> {{ order.guest_address }}
                      </p>
                      <p class="card-text text-secondary">
                        <i class="bi bi-chat-left-text-fill me-2"></i>
                        <strong>Ghi chú:</strong> {{ order.note }}
                      </p>
                      <!-- Order Items -->
                      <div class="mb-3 flex-grow-1 d-flex flex-column w-100">
                        <strong class="text-dark"><i class="bi bi-list-ul me-2"></i>Chi tiết đơn hàng:</strong>

                        <div class="order-items-scroll mt-2 flex-grow-1 w-100">
                          <ul class="list-group list-group-flush small w-100"
                            v-if="order.details && order.details.length > 0">
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
                                  <span><i class="bi bi-plus-circle me-1 text-success"></i>{{ topping.topping_name
                                    }}</span>
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

                      <!-- Tổng tiền & nút -->
                      <div class="mt-auto total-action-box">
                        <div class="total-label text-primary fw-bold mb-2">
                          Tổng: {{ formatCurrency(order.total_price) }}
                        </div>
                        <div class="total-button text-end">
                          <button class="btn btn-sm px-3 py-1 "
                            :class="selectedIds.includes(order.id) ? 'btn-outline-danger' : 'btn-outline-dark'"
                            @click="toggleSelect(order.id)">
                            <i :class="selectedIds.includes(order.id) ? 'bi bi-x-lg' : 'bi bi-plus-lg'"></i>
                            {{ selectedIds.includes(order.id) ? 'Bỏ' : 'Chọn' }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Nút quay lại + xác nhận -->
            <div
              class="d-flex flex-wrap justify-content-between justify-content-md-between align-items-center mt-4 gap-2">
              <button class="btn btn-outline-secondary px-4 py-2 fw-medium" @click="goBack">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
              </button>

              <button v-if="hasPermission('edit_shipper')" class="btn btn-success px-4 py-2 fw-semibold" :disabled="selectedIds.length === 0"
                @click="assignOrders">
                <i class="bi bi-check-circle me-1"></i> Xác nhận {{ selectedIds.length }} đơn
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'
import { Permission } from '@/stores/permission'
import { API_URL } from '@/config'
const router = useRouter()
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) userId.value = user.id
}
const { hasPermission } = Permission(userId)
const goBack = () => {
  router.back()
}

const orders = ref([])
const selectedIds = ref([])
const isLoading = ref(false)
const shipperId = JSON.parse(localStorage.getItem('user'))?.id

const getOrders = async () => {
  isLoading.value = true
  try {
    const res = await axios.get(`${API_URL}/get_all_orders`)
    orders.value = res.data.orders.filter(order => order.order_status === 'Bắt đầu giao' && order.shipper_id === null)
  } catch (e) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Không thể tải đơn hàng',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    })
  } finally {
    isLoading.value = false
  }
}

const toggleSelect = (id) => {
  if (selectedIds.value.includes(id)) {
    selectedIds.value = selectedIds.value.filter(item => item !== id)
  } else {
    if (selectedIds.value.length >= 3) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title: 'Chỉ chọn tối đa 3 đơn',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      })
      return
    }
    selectedIds.value.push(id)
  }
}

const assignOrders = async () => {
  try {
    const response = await axios.post(`${API_URL}/selected_orders`, {
      order_ids: selectedIds.value,
      shipper_id: shipperId
    })

    if (response.data.success) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Giao đơn thành công!',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      })
      selectedIds.value = []
      await getOrders()
      router.back()
    } else {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Không thể cập nhật đơn hàng',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      })
    }
  } catch (err) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Lỗi hệ thống',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    })
  }
}

const formatCurrency = (val) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)

onMounted(() => {
  getOrders()
})
</script>

<style scoped>
.order-card {
  border-radius: 16px;
  transition: all 0.2s ease-in-out;
  border: 2px solid transparent;
}

.order-card:hover {
  transform: scale(1.02);
}

.order-card.selected {
  border-color: #c92c3c;
}

.order-detail-box {
  max-height: 160px;
  overflow-y: auto;
  background-color: #f9f9f9;
  border-radius: 8px;
  padding: 8px 12px;
}

.order-detail-box::-webkit-scrollbar {
  height: 4px;
  width: 4px;
}

.order-detail-box::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}

.total-action-box {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px dashed #ccc;
}

.total-label {
  font-size: 16px;
  margin-bottom: 0;
}

.total-button {
  text-align: right;
}

.btn-outline-dark:focus,
.btn-outline-dark:active,
.btn-outline-dark.active,
.btn-outline-dark:focus-visible {
  color: #212529 !important;
  /* giữ màu chữ đen */
  background-color: transparent !important;
  /* không đổi nền */
  box-shadow: none !important;
  /* bỏ viền xanh */
  outline: none !important;
  /* bỏ outline */
}

.btn-outline-danger:focus,
.btn-outline-danger:active,
.btn-outline-danger.active,
.btn-outline-danger:focus-visible {
  color: #c92c3c !important;
  /* giữ màu chữ đen */
  background-color: transparent !important;
  /* không đổi nền */
  box-shadow: none !important;
  /* bỏ viền xanh */
  outline: none !important;
  /* bỏ outline */
}

/* Responsive cho mobile */
@media (max-width: 576px) {
  .total-action-box {
    gap: 8px;
    flex-wrap: wrap;
  }

  .total-label {
    font-size: 15px;
  }

  .total-button .btn {
    font-size: 14px;
    padding: 6px 12px;
  }
}
</style>
