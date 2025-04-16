<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Đơn hiện tại</h2>
      <button class="btn btn-primary">+ Tạo đơn mới</button>
    </div>

    <div class="row mt-3 g-3">
      <!-- Đơn hàng -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-4" v-for="order in orderOfTable" :key="order.order_id"
        v-show="order.reservation_status == 'Khách Đã Đến'">
        <div class="order-card">
          <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-secondary">#{{ order.order_id }}</span>
            <p>👥 {{ order.guest_count }} người</p>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">🕒 {{ timers[order.order_id] || '00:00:00' }}</p>
            <p class="fw-bold fs-4">{{ order.table_numbers.join(', ') }}</p>
            <p class="fw-bold text-danger">{{ formatNumber(order.total_price) }}VND</p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dropdown">
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                ...
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item1" href="#">Chọn món</a></li>
                <li><a class="dropdown-item1" href="#">Chi tiết</a></li>
              </ul>
            </div>
            <button class="btn btn-primary btn-sm">Thanh toán</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { Info } from '@/stores/info-order-reservation'

export default {
  setup() {
    const { formatNumber } = Info.setup()

    const timers = ref({})
    const startTimes = ref({})
    const updateTimers = () => {
      const now = new Date()
      for (const id in startTimes.value) {
        const start = startTimes.value[id]
        const diff = Math.floor((now - start) / 1000)
        const hours = String(Math.floor(diff / 3600)).padStart(2, '0')
        const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0')
        const seconds = String(diff % 60).padStart(2, '0')
        timers.value[id] = `${hours}:${minutes}:${seconds}`
      }
    }

    const orderOfTable = ref([])
    const getOrderOfTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/order-tables')
        orderOfTable.value = res.data.data
        res.data.data.forEach(order => {
          if (order.reservation_status === 'Khách Đã Đến') {
            startTimes.value[order.order_id] = new Date(order.check_in_time)
          }
        })

        updateTimers()
      } catch (error) {
        console.log(error)
      }
    }


    onMounted(() => {
      getOrderOfTable()
      setInterval(updateTimers, 1000)

    })

    return {
      orderOfTable,
      formatNumber,
      timers
    }
  },
}
</script>

<style scoped>
.order-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 15px;
  transition: transform 0.2s ease-in-out;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.order-card:hover {
  transform: scale(1.02);
}

.text-danger {
  color: rgb(207, 63, 63) !important;
}

/* Giới hạn dropdown */
.dropdown-menu {
  min-width: 150px;
}

.dropdown-item1 {
  text-align: left;
  color: #333;
  padding: 6px 10px;
  font-weight: normal;
  text-decoration: none;
  border: none;
  font-size: 16px;
  display: block;
  background-color: transparent;
  transition:
    background-color 0.3s,
    color 0.3s;
  cursor: pointer;
}

.dropdown-item1:hover {
  background-color: #f0f0f0;
  color: #800020;
}
</style>
