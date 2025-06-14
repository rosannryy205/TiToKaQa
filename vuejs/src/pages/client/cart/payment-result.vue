<template>
  <div class="container py-5">
    <h2 class="mb-4">Kết quả thanh toán</h2>

    <!-- Trạng thái loading -->
    <div v-if="loading">
      Đang xác minh kết quả thanh toán...
    </div>

    <!-- Kết quả thanh toán -->
    <div v-else>
      <!-- Thành công -->
      <div v-if="success">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
          <div class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
              <img src="/public/img/meovui.png" alt="Thanh toán thành công" width="300px" />
              <p class="alert alert-success mb-0">
                {{ selectedMethod === 'COD' ? 'Đặt hàng thành công! Chờ thanh toán.' : 'Thanh toán thành công! Cảm ơn bạn đã đặt hàng.' }}
              </p>
            </div>
            <router-link to="/order-management" class="btn btn-check-out mt-2">Theo dõi đơn hàng</router-link>
          </div>
        </div>
      </div>

      <!-- Thất bại -->
      <div v-else>
        <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
          <div class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
              <img src="/public/img/meobuon.png" alt="Thanh toán thất bại" width="300px" />
              <p class="alert alert-danger mb-0">Thanh toán thất bại hoặc bị hủy.</p>
            </div>
            <router-link to="/" class="btn btn-check-out mt-2">Quay về trang chủ</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const loading = ref(true)
    const success = ref(false)
    const selectedMethod = ref('')

    onMounted(async () => {
      selectedMethod.value = localStorage.getItem('payment_method') || 'COD'

      if (selectedMethod.value !== 'COD') {
        try {
          const res = await axios.get('http://127.0.0.1:8000/api/payments/vnpay-return', {
            params: new URLSearchParams(window.location.search)
          })


          success.value = res.data.success == true
          console.log(res.data)
        } catch (error) {
          console.error('❌ Xác minh từ backend thất bại:', error)
          success.value = false
        }
      } else {
        success.value = true
      }

      loading.value = false
    })


    return {
      loading,
      success,
      selectedMethod
    }
  }
}

</script>
