<template>
  <div class="container py-5">
    <h2 class="mb-4">Kết quả thanh toán</h2>
    <div v-if="loading">Đang xác minh kết quả thanh toán...</div>
    <div v-else>
      <div v-if="success">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
          <div class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
              <img src="/public/img/meovui.png" alt="Giỏ hàng rỗng" width="300px">
              <p class="alert alert-success mb-0" v-if="selectedMethod === 'Thanh toán COD'">
                Đặt hàng thành công! Chờ thanh toán.</p>
              <p class="alert alert-success mb-0" v-else>
                Thanh toán thành công! Cảm ơn bạn đã đặt hàng.</p>
            </div>
            <router-link to="/order-management" class="btn btn-check-out mt-2">Theo dõi đơn hàng</router-link>
          </div>
        </div>
      </div>
      <div v-else>
        <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
          <div class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
              <img src="/public/img/meobuon.png" alt="Giỏ hàng rỗng" width="300px">
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
import { useRoute } from 'vue-router'
import axios from 'axios'

export default {
  setup() {
    const route = useRoute()
    const loading = ref(true)
    const success = ref(false)
    const selectedMethod = ref('')

    onMounted(() => {
      const params = new URLSearchParams(window.location.search)
      const order_id = localStorage.getItem('order_id')

      if (!order_id) {
        console.error('❌ Không tìm thấy order_id trong localStorage!')
        loading.value = false
        success.value = false
        return
      }
      selectedMethod.value = localStorage.getItem('payment_method') || 'Thanh toán COD'
      success.value = selectedMethod.value === 'Thanh toán COD'
        || params.get('vnp_ResponseCode') === '00' // VNPay
        || params.get('resultCode') === '0' // Momo

      loading.value = false
    })

    return {
      route,
      loading,
      success,
      selectedMethod
    }
  }
}

</script>
