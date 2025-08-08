<template>
  <div class="container py-2">
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
          <router-link v-if="isLoggedIn" to="/account/order-management" class="btn btn-check-out">Xem chi tiết đơn hàng</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Info } from "@/stores/info-order-reservation";
import { useUserStore  } from "@/stores/userAuth";
import { defineComponent } from "vue";

export default defineComponent({
  props: ["orders", "methodLabel", "paymentMessage"],
  setup() {
    const { formatNumber, formatDate } = Info.setup();
    const auth = useUserStore ();
    return { formatNumber, formatDate,isLoggedIn: auth.isLoggedIn, };
  },
});
</script>
