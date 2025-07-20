<template>
  <div class="container py-5">
    <h2 class="mb-4">Kết quả thanh toán</h2>

    <!-- Trạng thái loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary mb-3" role="status"></div>
      <p>Đang xác minh kết quả thanh toán...</p>
    </div>

    <!-- Thành công -->
    <div v-else>
      <div v-if="success" class="container py-2">
        <div class="text-center p-2 rounded mb-5">
          <div class="row text-center mb-2" v-for="order in orders" :key="order.id">
            <div class="col-md-3 col-6">
              <div class="text-uppercase text-muted title">Mã đơn hàng:</div>
              <div class="fw-semibold">#{{ order.id }}</div>
            </div>
            <div class="col-md-3 col-6">
              <div class="text-uppercase text-muted title">Ngày:</div>
              <div class="fw-semibold">{{ formatDate(order.order_time || order.reservations_time) }}</div>
            </div>
            <div class="col-md-3 col-6">
              <div class="text-uppercase text-muted title">Tổng cộng:</div>
              <div class="fw-semibold">{{ formatNumber(order.total_price) }} VND</div>
            </div>
            <div class="col-md-3 col-6">
              <div class="text-uppercase text-muted title">Phương thức thanh toán:</div>
              <div class="fw-semibold">{{ methodLabel }}</div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center align-items-center">
          <div style="max-width: 600px; width: 100%;">
            <div class="d-flex flex-column align-items-center mb-3">
              <i class="fa-solid fa-circle-check mb-2" style="font-size: 5rem; color: #03cc00;"></i>
              <p class="fw-bold text-uppercase fs-5 mb-0">{{ paymentMessage }}</p>
              <p class="text-muted mt-2">Chúng tôi đã nhận được đơn hàng và sẽ sớm liên hệ với bạn.</p>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
              <router-link to="/" class="btn btn-check-out">Về trang chủ</router-link>
              <router-link to="/account/order-management" class="btn btn-check-out">Xem chi tiết đơn hàng</router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Thất bại -->
      <div v-else class="container py-5">
        <div class="d-flex justify-content-center align-items-center">
          <div style="max-width: 500px; width: 100%;">
            <div class="d-flex flex-column align-items-center mb-3">
              <i class="fa-solid fa-circle-xmark" style="font-size: 5rem; color: #c92c3c;"></i>
              <p class="text-muted mt-2">Thanh toán thất bại hoặc đơn bị hủy trong lúc giao dịch.</p>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
              <router-link to="/" class="btn btn-check-out">Về trang chủ</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Info } from "@/stores/info-order-reservation";
import { ref, onMounted, computed } from "vue";
import axios from 'axios';

export default {
  setup() {
    const { formatNumber, formatDate } = Info.setup();
    const loading = ref(true);
    const success = ref(false);
    const selectedMethod = ref('');
    const orders = ref([]);

    const getOrder = async (orderId) => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/get-order-reservation-info', {
          params: { type: 'order_id', value: orderId }
        });
        orders.value = res.data.orders || [];
        success.value = orders.value.length > 0;
        console.log("Đơn hàng:", orders.value);
      } catch (err) {
        console.error("Lỗi khi lấy đơn hàng:", err);
      }
    };

    const paymentMessage = computed(() => {
      switch (selectedMethod.value) {
        case 'VNPAY':
        case 'MOMO':
          return 'Thanh toán thành công! Cảm ơn bạn đã đặt hàng.';
        case 'COD':
        default:
          return 'Đặt hàng thành công! Cảm ơn bạn đã đặt hàng.';
      }
    });

    const methodLabel = computed(() => {
      switch (selectedMethod.value) {
        case 'VNPAY': return 'VNPAY';
        case 'MOMO': return 'MoMo';
        default: return 'Thanh toán khi nhận hàng';
      }
    });

    onMounted(async () => {
      selectedMethod.value = localStorage.getItem('payment_method') || 'COD';
      const orderId = localStorage.getItem('order_id');
      loading.value = true;

      try {
        if (selectedMethod.value === 'VNPAY') {
          const res = await axios.get('http://127.0.0.1:8000/api/payments/vnpay-return', {
            params: new URLSearchParams(window.location.search)
          });
          success.value = res.data.success === true;
          console.log('🔁 VNPAY RESPONSE:', res.data);
        } else if (selectedMethod.value === 'MOMO') {
          const res = await axios.get('http://127.0.0.1:8000/api/payments/momo-return', {
            params: new URLSearchParams(window.location.search)
          });
          success.value = res.data.success === true;
        } else {
          success.value = true;
        }

        if (success.value && orderId) {
          await getOrder(orderId);
        }

        localStorage.removeItem('payment_method');
        localStorage.removeItem('order_id');
      } catch (err) {
        console.error('Lỗi xác minh thanh toán:', err);
        success.value = false;
      } finally {
        loading.value = false;
      }
    });

    return {
      loading,
      success,
      selectedMethod,
      formatNumber,
      formatDate,
      orders,
      methodLabel,
      paymentMessage
    };
  }
};
</script>

<style scoped>
.title {
  color: #c92c3c !important;
}
</style>
