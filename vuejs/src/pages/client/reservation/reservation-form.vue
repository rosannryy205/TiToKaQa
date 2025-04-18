<template>
  <div class="container py-4">
    <h3 class="text-center text-danger fw-bold mb-4">Thanh toán cho đơn hàng của bạn</h3>
    <div class="row">
      <!-- Thông tin đơn hàng và thực đơn -->
      <div class="col-lg-8">
        <div class="section-title">Thông tin đơn hàng của bạn</div>

        <div class="row mb-3">
          <div class="col-6"><strong>Tên người đặt bàn:</strong></div>
          <div class="col-6">{{ info.guest_name }}</div>

          <div class="col-6"><strong>Điện thoại:</strong></div>
          <div class="col-6">{{ info.guest_phone }}</div>

          <div class="col-6"><strong>Email:</strong></div>
          <div class="col-6">{{ info.guest_email }}</div>

          <div class="col-6"><strong>Thời gian dùng bữa dự kiến:</strong></div>
          <div class="col-6">{{ info.reservations_time }}</div>

          <div class="col-6"><strong>Bàn số:</strong></div>
          <div class="col-6">đang xác nhận</div>

          <div class="col-6"><strong>Khách dự kiến:</strong></div>
          <div class="col-6">{{ info.guest_count }} người</div>

          <div class="col-6"><strong>Phí giữ bàn:</strong></div>
          <div class="col-6">{{ formatNumber(info.deposit_amount) }} VND</div>
        </div>

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
                  <small class="text-muted">
                    {{ topping.topping_name }} -
                    {{ topping.price ? formatNumber(topping.price) + ' VND' : '' }}
                  </small>
                </div>
              </td>
              <td>{{ formatNumber(detail.price) }} VNĐ</td>
              <td>{{ detail.quantity }}</td>
              <td>
                {{formatNumber(
                  detail.price * detail.quantity +
                  detail.toppings.reduce((sum, topping) => sum + parseFloat(topping.price), 0)
                )}} VNĐ
              </td>
            </tr>

            <tr>
              <td colspan="3"></td>
              <td><strong>Tổng cộng</strong></td>
              <td><strong>{{formatNumber(info.details.reduce((total, detail) => total + (detail.price *
                detail.quantity), 0)) }} VNĐ</strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Thanh toán -->
      <div class="col-lg-4">
        <div class="section-title">Thanh toán</div>
        <form @submit.prevent="submitOrder">
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
                <input class="form-check-input" type="radio" name="payment" id="vnpay" value="Thanh toán VNPAY"
                  v-model="paymentMethod">
                <label class="form-check-label d-flex align-items-center" for="vnpay">
                  <span class="me-2">Thanh toán qua VNPAY</span>
                  <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="">
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment" id="momo" value="Thanh toán MOMO"
                  v-model="paymentMethod">
                <label class="form-check-label d-flex align-items-center" for="momo">
                  <span class="me-2">Thanh toán qua Momo</span>
                  <img src="/img/momo.png" height="20" width="20" alt="">
                </label>
              </div>
            </div>

            <button class="btn btn-danger">Thanh toán</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { User } from '@/stores/user'
import { onMounted } from 'vue'
import { Info } from '@/stores/info-order-reservation'
import { FoodList } from '@/stores/food'
import { Payment } from '@/stores/payment'

export default {
  setup() {
    const {
      info,
      getInfo,
      formatNumber,
      getImageUrl,
      orderId,
    } = Info.setup()

    const {
      form,
      handleSubmit
    } = User.setup()

    const {
      isLoading
    } = FoodList.setup()

    // Thanh toán
    const {
      cartItems,
      fullname,
      email,
      phone,
      address,
      note,
      paymentMethod,
      check_out,
      loadCart,
      totalPrice,
      totalPriceItem,
      totalQuantity,
      submitOrder,
    } = Payment.setup()


    onMounted(() => {
      loadCart()
      getInfo('order', orderId)
      console.log(info);
      console.log(totalPrice)
    })

    return {
      info,
      getInfo,
      formatNumber,
      getImageUrl,
      orderId,

      cartItems,
      totalPrice,
      totalPriceItem,
      totalQuantity,
      fullname,
      email,
      phone,
      address,
      note,
      check_out,
      form,
      handleSubmit,
      submitOrder,
      isLoading,
      paymentMethod,
    }
  }

}
</script>
