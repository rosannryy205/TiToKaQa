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
      <template v-if="isPaidSuccess">
        <OrderSummary :orders="orders" :methodLabel="methodLabel" :paymentMessage="paymentMessage" />
      </template>

      <!-- Thất bại -->
      <template v-else class="container py-5">
        <div class="d-flex justify-content-center align-items-center">
          <div style="max-width: 500px; width: 100%;">
            <div class="d-flex flex-column align-items-center mb-3">
              <i class="fa-solid fa-circle-xmark" style="font-size: 5rem; color: #c92c3c;"></i>
              <p class="text-muted mt-3">{{ backendMessage || 'Thanh toán thất bại hoặc đơn bị hủy trong lúc giao dịch.'
                }}</p>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
              <router-link to="/" class="btn btn-check-out">Về trang chủ</router-link>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { Info } from "@/stores/info-order-reservation";
import { ref, onMounted, computed } from "vue";
import axios from 'axios';
import OrderSummary from "./OrderSummary.vue";

export default {
  components: { OrderSummary },
  setup() {
    const { formatNumber, formatDate } = Info.setup();
    const loading = ref(true);
    const isPaidSuccess = ref(false);
    const selectedMethod = ref('');
    const orders = ref([]);
    const orderId = ref(localStorage.getItem("order_id"));
    const backendMessage = ref("");

    const getOrder = async (orderId) => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/get-order-reservation-info', {
          params: { type: 'order_id', value: orderId }
        });
        orders.value = res.data.orders || [];
        isPaidSuccess.value = orders.value.length > 0;
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
    const verifyPayment = async () => {
      const params = new URLSearchParams(window.location.search);
      loading.value = true;

      try {
        let response;

        if (selectedMethod.value === "VNPAY") {
          response = await axios.get("http://127.0.0.1:8000/api/payments/vnpay-return", { params });
        } else if (selectedMethod.value === "MOMO") {
          response = await axios.get("http://127.0.0.1:8000/api/payments/momo-return", { params });
        } else {
          isPaidSuccess.value = true;
          paymentMessage.value = 'Thanh toán bằng tiền mặt (COD) không cần xác minh.';
          backendMessage.value = '';
          loading.value = false;
          return;
        }

        const data = response.data;

        backendMessage.value = data.Message || data.status_text || '';

        if (data.status === 'success' || data.success === true || data.success === "00") {
          isPaidSuccess.value = true;
          paymentMessage.value = 'Thanh toán thành công!';
        } else if (data.status === 'cancel') {
          isPaidSuccess.value = false;
          paymentMessage.value = 'Thanh toán đã bị hủy.';
        } else {
          isPaidSuccess.value = false;
          paymentMessage.value = 'Thanh toán thất bại. Vui lòng thử lại.';
        }

      } catch (error) {
        console.error("Lỗi xác minh thanh toán:", error);
        isPaidSuccess.value = false;
        paymentMessage.value = 'Đã xảy ra lỗi khi xác minh thanh toán.';
        backendMessage.value = error.message;
      } finally {
        loading.value = false;
      }
    };


    onMounted(async () => {
      selectedMethod.value = localStorage.getItem("payment_method") || "COD";

      await verifyPayment();

      if (isPaidSuccess.value && orderId.value) {
        await getOrder(orderId.value);
      }

      localStorage.removeItem("order_id");
      localStorage.removeItem("payment_method");
      loading.value = false;
    });


    return {
      loading,
      isPaidSuccess,
      selectedMethod,
      formatNumber,
      formatDate,
      orders,
      methodLabel,
      paymentMessage,
      backendMessage,
    };
  }
};
</script>

<style scoped>
.title {
  color: #c92c3c !important;
}
</style>
