<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h3 class="fw-bold mb-0 text-primary">
                <i class="bi bi-truck me-2"></i>Chọn đơn hàng
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


            <div class="order-list-scroll" v-else>
              <div class="row g-4">
                <div class="col-md-4" v-for="order in orders" :key="order.id">
                  <div class="card order-card h-100 shadow-sm" :class="{ selected: selectedIds.includes(order.id) }">
                    <div class="card-body d-flex flex-column">
                      <div class="d-flex justify-content-between align-items-start mb-2 w-100">
                        <h5 class="fw-bold text-dark mb-0">#{{ order.id }}</h5>
                        <i class="bi"
                          :class="selectedIds.includes(order.id) ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'"
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
                      <strong class="text-dark"><i class="bi bi-list-ul me-1"></i>Chi tiết món:</strong>

                      <!-- Chi tiết đơn hàng -->
                      <div class="order-detail-box mt-2 mb-2">
                        <ul class="list-group list-group-flush small mt-1">
                          <li class="list-group-item px-0 py-1" v-for="food in order.details" :key="food.id">
                            <div class="d-flex justify-content-between">
                              <span>🍽️ {{ food.food_name }} (x{{ food.quantity }})</span>
                              <span class="text-primary fw-semibold">{{ formatCurrency(food.price) }}</span>
                            </div>
                            <ul v-if="food.toppings && food.toppings.length" class="ps-3 mt-1 mb-0 text-muted small">
                              <li v-for="(topping, index) in food.toppings" :key="index">
                                <i class="bi bi-plus-circle me-1 text-success"></i>{{ topping.topping_name }} -
                                <span class="text-success">{{ formatCurrency(topping.price || 0) }}</span>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>

                      <!-- Tổng tiền & nút -->
                      <div class="mt-auto total-action-box">
                        <div class="total-label text-primary fw-bold mb-2">
                          Tổng: {{ formatCurrency(order.total_price) }}
                        </div>
                        <div class="total-button text-end">
                          <button class="btn btn-sm px-3 py-1 rounded-pill"
                            :class="selectedIds.includes(order.id) ? 'btn-outline-danger' : 'btn-outline-primary'"
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
            <div class="d-flex justify-content-between align-items-center mt-4">
              <button class="btn btn-outline-secondary" @click="goBack">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
              </button>

              <button class="btn btn-success px-4 py-2 fw-semibold" :disabled="selectedIds.length === 0"
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
import { toast } from 'vue3-toastify'
import { useRouter } from 'vue-router'

const router = useRouter()

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
    const res = await axios.get('http://127.0.0.1:8000/api/get_all_orders')
    orders.value = res.data.orders.filter(order => order.order_status === 'Bắt đầu giao' && order.shipper_id === null)
  } catch (e) {
    toast.error('Không thể tải đơn hàng')
  } finally {
    isLoading.value = false
  }
}

const toggleSelect = (id) => {
  if (selectedIds.value.includes(id)) {
    selectedIds.value = selectedIds.value.filter(item => item !== id)
  } else {
    if (selectedIds.value.length >= 3) {
      toast.warning('Chỉ chọn tối đa 3 đơn')
      return
    }
    selectedIds.value.push(id)
  }
}

const assignOrders = async () => {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/selected_orders', {
      order_ids: selectedIds.value,
      shipper_id: shipperId
    })

    if (response.data.success) {
      toast.success('Giao đơn thành công!')
      selectedIds.value = []
      await getOrders()
      router.back()
    } else {
      toast.error('Không thể cập nhật đơn hàng')
    }
  } catch (err) {
    toast.error('Lỗi hệ thống')
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
  border-color: #0d6efd;
  background-color: #e7f1ff;
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

.order-list-scroll {
  max-height: 70vh;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 8px;
}

.order-list-scroll::-webkit-scrollbar {
  width: 6px;
}

.order-list-scroll::-webkit-scrollbar-thumb {
  background-color: #ccc;
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
