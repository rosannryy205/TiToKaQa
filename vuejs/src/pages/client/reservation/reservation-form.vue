<template>
  <div class="container py-4">
    <h3 class="text-center text-danger fw-bold mb-4">Thanh toán cho đơn hàng của bạn</h3>

    <div class="row">
      <!-- Thông tin đơn hàng và thực đơn -->
      <div class="col-lg-8">
        <!-- Thông tin đặt bàn -->
        <div class="section-title">Thông tin đơn hàng của bạn</div>
        <div class="row mb-3">
          <div class="col-6"><strong>Tên người đặt bàn:</strong></div>
          <div class="col-6">{{ info.guest_name || user.name }}</div>

          <div class="col-6"><strong>Điện thoại:</strong></div>
          <div class="col-6">{{ info.guest_phone || user.phone}}</div>

          <div class="col-6"><strong>Email:</strong></div>
          <div class="col-6">{{ info.guest_email || user.email }}</div>

          <div class="col-6"><strong>Thời gian dùng bữa dự kiến:</strong></div>
          <div class="col-6">{{ info.reservations_time }}</div>

          <div class="col-6"><strong>Bàn số:</strong></div>
          <div class="col-6">đang xác nhận</div>

          <div class="col-6"><strong>Khách dự kiến:</strong></div>
          <div class="col-6">{{ info.guest_count }} người</div>

          <div class="col-6"><strong>Phí giữ bàn:</strong></div>
          <div class="col-6"> {{ formatNumber(info.deposit_amount) }}VND</div>
        </div>

        <!-- Thực đơn đặt hàng -->
        <div class="section-title">Thực đơn đặt hàng</div>
        <table class="table table-bordered bg-white">
          <thead class="table-light">
            <tr>
              <th>STT</th>
              <th>Món ăn</th>
              <th>Giá bán</th>
              <th>Số lượng</th>
              <th>Thành tiền</th>
            </tr>
          </thead>
          <tbody v-if="info.details && info.details.length">
            <tr v-for="(detail, index) in info.details" :key="index">
              <td>{{ ++index }}</td>
              <td>
                <img :src="getImageUrl(detail.image)" style="width: 50px; height: auto; margin-right: 10px;">
                {{ detail.food_name }} <br>
                <div class="text-start" v-for="(topping, index) in detail.toppings" :key="index">
                  <small class="text-muted" v-if="topping.price !== null">
                    {{ topping.topping_name + ' - ' + formatNumber(topping.price) + ' VND' }}
                  </small>
                  <small class="text-muted" v-else>
                    {{ topping.topping_name }}
                  </small>
                </div>

              </td>
              <td>{{ formatNumber(detail.price) }} VNĐ</td>
              <td>{{ detail.quantity }}</td>
              <td>{{ formatNumber(detail.price * detail.quantity) }} VNĐ</td>
            </tr>
            <tr>
              <td colspan="3"></td>
              <td><strong>Tổng cộng</strong></td>
              <td><strong>{{ formatNumber(info.total_price) }} VNĐ</strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Thanh toán -->
      <div class="col-lg-4">
        <div class="section-title">Thanh toán</div>
        <div class="card-payment">
          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính</span>
            <span>{{ formatNumber(info.total_price) }} VNĐ</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Khuyến mãi</span>
            <span>-</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-3">
            <strong>Tổng cộng (VAT)</strong>
            <strong class="text-danger">{{ formatNumber(info.total_price) }} VNĐ</strong>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Phương thức thanh toán</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="pay1" checked>
              <label class="form-check-label" for="pay1">Thanh toán tại nhà hàng</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="pay2">
              <label class="form-check-label" for="pay2">Thanh toán bằng VNPAY</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="pay3">
              <label class="form-check-label" for="pay3">Thanh toán bằng MoMo</label>
            </div>
          </div>

          <button class="btn btn-danger">Thanh toán</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { onMounted } from 'vue'
import { Info } from '@/stores/info-order-reservation'

export default {
  setup() {


  const {
    info,
    user,
    getInfo,
    formatNumber,
    getImageUrl,
    orderId
  } = Info.setup()

  onMounted(() => {
    getInfo('order', orderId)
    console.log(info);

  })

  return {
    info,
    user,
    getInfo,
    formatNumber,
    getImageUrl,
    orderId
  }
}

}
</script>
