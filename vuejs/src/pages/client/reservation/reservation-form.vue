<template>
  <div class="row d-flex text-center">
    <div class="title-shops1 d-sm-block fw-bold mt-4">
      <span class="fs-1">ĐẶT BÀN CÙNG CHÚNG TÔI!</span>
    </div>
  </div>

  <div class="container">
    <h6 class="fw-bold">
      Chúng tôi sẽ giữ bàn trong {{ minutes }} phút {{ seconds }} giây
    </h6>
    <!-- Bọc toàn bộ row vào form -->
    <form @submit.prevent="reservation">
      <div class="row">
        <!-- Cột trái -->
        <div class="col-lg-6">
          <div class="section-title1">Thông tin đơn hàng của bạn</div>
          <div class="border shadow-sm bg-white p-4 rounded-bottom">
            <div class="mb-3">
              <input type="text" class="form-control rounded border shadow-sm" placeholder="Tên của bạn"
                v-model="form.fullname" required/>
            </div>
            <div class="mb-3">
              <input type="email" class="form-control rounded border shadow-sm" placeholder="Email"
                v-model="form.email" required/>
            </div>
            <div class="mb-3">
              <input type="text" class="form-control rounded border shadow-sm" placeholder="Số điện thoại"
                v-model="form.phone" required/>
            </div>
            <div class="mb-3">
              <textarea class="form-control rounded border shadow-sm" rows="3" placeholder="Ghi chú"
                v-model="note"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <router-link to="/reservation" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Quay về
              </router-link>
            </div>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="col-lg-6 ffff">
          <div class="section-title1">Thanh toán</div>
          <div class="border pt-4">
            <div v-if="!cartItems.length > 0">
              <router-link :to="`/food/${orderId}`" class="bi bi-plus-circle-fill pb-2"></router-link>
              <div class="text-center fw-medium fs-6 pb-4">
                Chọn món trước khi đến nhà hàng
              </div>
            </div>

            <div class="list-product-scroll1 mb-3">
              <div v-for="(item, index) in cartItems" :key="index" class="d-flex mb-3">
                <img :src="getImageUrl(item.image)" alt="" class="me-3 rounded" width="80" height="80" />
                <div class="flex-grow-1">
                  <strong>{{ item.name }}</strong>
                  <div>Loại: {{ item.type }}</div>
                  <div>{{ item.spicyLevel }}</div>
                  <div v-if="item.toppings.length" class="text-muted small">
                    <div v-for="(topping, i) in item.toppings" :key="i">
                      {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ
                    </div>
                  </div>
                  <div v-else class="text-muted small">Không có topping</div>
                  <div>Số lượng: {{ item.quantity }}</div>
                  <div>Giá: {{ formatNumber(item.price) }} VNĐ</div>
                  <hr />
                </div>
                <div class="text-end ms-2">
                  <strong>{{ formatNumber(totalPriceItem(item)) }} VNĐ</strong>
                </div>
              </div>
            </div>
          </div>
          <div class="card-payment1 border shadow-sm bg-white p-4 rounded-bottom">
            <div class="d-flex justify-content-between mb-2">
              <span>Tạm tính</span>
              <span>{{ formatNumber(totalPrice) }} VNĐ</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
              <span>Khuyến mãi</span>
              <span class="text-success">-{{ formatNumber(discountAmount) }} VNĐ</span>
            </div>

            <div class="input-group mb-2">
              <input type="text" class="form-control" placeholder="Nhập mã giảm giá" v-model="discountInput"
                @keyup.enter="handleDiscountInput" />
              <button class="btn btn-outline-secondary" type="button" @click="handleDiscountInput">
                Áp dụng
              </button>
            </div>

            <div v-if="discounts.length" class="mb-3">
              <small class="text-muted">Chọn mã giảm giá:
                <span v-for="discount in discounts" :key="discount.id" class="badge" :class="{
                  'bg-success text-white': selectedDiscount === discount.code,
                  'bg-light text-dark': selectedDiscount !== discount.code,
                }" style="cursor: pointer; margin-right: 6px" @click="applyDiscountCode(discount.code)">
                  {{ discount.code }}
                </span>
                <span v-if="selectedDiscount" class="badge bg-danger text-white" style="cursor: pointer"
                  @click="selectedDiscount = ''">
                  Bỏ chọn
                </span>
              </small>
            </div>

            <hr />
            <div class="d-flex justify-content-between mb-3">
              <strong class="fs-5">Tổng cộng (VAT)</strong>
              <strong class="text-danger fs-5">{{ formatNumber(finalTotal) }} VNĐ</strong>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Phương thức thanh toán</label>
              <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="vnpay" value="Thanh toán VNPAY"
                v-model="paymentMethod" />
              <label class="form-check-label d-flex align-items-center" for="vnpay">
                <span class="me-2">Thanh toán qua VNPAY</span>
                <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="momo" value="Thanh toán MOMO"
                v-model="paymentMethod" />
              <label class="form-check-label d-flex align-items-center" for="momo">
                <span class="me-2">Thanh toán qua Momo</span>
                <img src="/img/momo.png" height="20" width="20" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="cod" value="Thanh toán COD"
                v-model="paymentMethod" />
              <label class="form-check-label d-flex align-items-center" for="cod">
                <span class="me-2">Thanh toán khi nhận hàng (COD)</span>
                <img src="/img/cod.png" height="30" width="30" alt="" />
              </label>
            </div>
            </div>

            <button type="submit" class="btn btn-danger1 w-100 mt-3">Thanh toán</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { User } from '@/stores/user'
import { ref, onMounted, onUnmounted } from 'vue'
import { Discounts } from '@/stores/discount'
import { Cart } from '@/stores/cart'
import numeral from 'numeral'
import { toast } from 'vue3-toastify'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import { Info } from '@/stores/info-order-reservation'
export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format("0,0");
    },
    getImageUrl(image) {
      return `/img/food/${image}`;
    },
  },
  setup() {
    const note = ref("");
    const isLoading = ref(false);
    const route = useRoute();
    const router = useRouter();
    const user1 = JSON.parse(localStorage.getItem("user")) || {};
    const userId = user1?.id || "guest";
    const orderId = route.params.orderId;
    const minutes = ref(5);
    const seconds = ref(0);
    let countdownInterval = null;
    const { form, user } = User.setup();
    const { getInfo, info } = Info.setup();
    const {
      discountAmount,
      finalTotal,
      discountInput,
      selectedDiscount,
      discounts,
      applyDiscountCode,
      handleDiscountInput,
      discountId,
    } = Discounts()

    const {
      cartKey, cartItems, totalPriceItem, loadCart, totalPrice
    } = Cart()


    const expirationTime = ref(null);
    const updateCountdown = async () => {
      try {
        expirationTime.value = new Date(info.value.expiration_time);

        const now = new Date();
        const diff = expirationTime.value - now;
        if (diff <= 0) {
          clearInterval(countdownInterval);
          minutes.value = 0;
          seconds.value = 0;

          await axios.put(
            `http://127.0.0.1:8000/api/order-history-info/cancel/${orderId}`
          );
          if (localStorage.removeItem(`cart_${userId}_reservation_${orderId}`)) {
            Swal.fire({
              icon: "error",
              text:
                "Đơn hàng của bạn đã hết thời gian giữ bàn! Vui lòng quay lại đặt đơn hàng khác",
              confirmButtonText: "Quay lại",
              confirmButtonColor: "#d32f2f",
            }).then((result) => {
              if (result.isConfirmed) {
                router.push("/reservation");
              }
            });

            return;
          }
        }

        minutes.value = Math.floor((diff / 1000 / 60) % 60);
        seconds.value = Math.floor((diff / 1000) % 60);
      } catch (error) {
        console.log(error);
      }
    };

    const paymentMethod = ref('')
    const check_payment = async () => {
      try {
        if (!paymentMethod.value) {
          alert('Vui lòng chọn phương thức thanh toán!')
          return
        }
        const orderData = {
          id: orderId,
          guest_name: form.value.fullname || form.value.username,
          guest_phone: form.value.phone,
          guest_email: form.value.email,
          note: form.value.note || "",
          deposit_amount: 100000,
          total_price: finalTotal.value,
          money_reduce: discountAmount.value,
          discount_id: discountId.value || null,
          order_detail: cartItems.value.map((item) => ({
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: item.type,
            toppings: item.toppings.map((t) => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price,
            })),
          })),
        };

        await axios.post("http://127.0.0.1:8000/api/reservation", orderData);

        if (orderData.discount_id) {
          await axios.post("http://localhost:8000/api/discounts/use", {
            discount_id: orderData.discount_id,
          });
        }
        localStorage.setItem("payment_method", paymentMethod.value);
        const cartKey = `cart_${userId}_reservation_${orderId}`;
        localStorage.removeItem(cartKey);

        if (paymentMethod.value === "Thanh toán VNPAY" || paymentMethod.value === "Thanh toán MOMO") {
          const paymentRes = await axios.post("http://127.0.0.1:8000/api/payment", {
            order_id: orderId,
            amount: finalTotal.value,
          });
          if (paymentRes.data.payment_url) {
            window.location.href = paymentRes.data.payment_url;
            return;
          } else {
            alert("Không tạo được link thanh toán.");
          }

        }
        if (paymentMethod.value === "Thanh toán COD") {
          await new Promise((resolve) => setTimeout(resolve, 300));
          await axios.post("http://127.0.0.1:8000/api/vnpay-return", {
            order_id: orderId,
            amount_paid: finalTotal.value,
            payment_method: "Thanh toán COD",
            payment_status: "Chưa thanh toán",
            payment_type: "Thanh toán toàn bộ",
          });
          alert("Đặt hàng thành công!");
        }

        router.push("/payment-result");
      } catch (error) {
        toast.error("Có lỗi xảy ra!");
        console.error("Lỗi xảy ra:", error.message);
        alert("Lỗi khi gửi đơn hàng. Vui lòng thử lại!");
      }
    };
    const reservation = async () => {
      isLoading.value = true
      try {
        console.log('✅ form gửi đi:', form.value)
        await check_payment()
        console.log('✅ check_out đã được gọi xong')
      } catch (error) {
        console.error('❌ Lỗi khi gọi check_out:', error)
      } finally {
        isLoading.value = false
      }
    }

    // const notify = async () => {
    //   const status = info.value;
    //   const now = new Date();
    //   const expirationTime = new Date(status.expiration_time);

    //   let message = '';

    //   if (status.order_status === 'Đã hủy' || status.reservation_status === 'Đã Hủy') {
    //     message = 'Đơn của bạn đã bị hủy! Vui lòng quay lại đặt đơn hàng khác.';
    //   } else if (expirationTime < now) {
    //     message = 'Đơn hàng của bạn đã hết thời hạn! Vui lòng quay lại đặt đơn hàng khác.';
    //   } else if (status.order_status === 'Giao thành công' || status.reservation_status === 'Hoàn Thành') {
    //     message = 'Đơn hàng đã được hoàn thành trước đó.';
    //   }

    //   if (message !== '') {
    //     await Swal.fire({
    //       icon: 'error',
    //       text: message,
    //       confirmButtonText: 'Quay lại',
    //       confirmButtonColor: '#d32f2f',
    //     }).then((result) => {
    //       if (result.isConfirmed) {
    //         router.push('/reservation');
    //       }
    //     });
    //     return;
    //   }

    // };

    onMounted(() => {
      getInfo("order", orderId);
      updateCountdown();
      countdownInterval = setInterval(updateCountdown, 1000);
    });

    onUnmounted(() => {
      clearInterval(countdownInterval);
    });

    return {
      orderId,
      form,
      user,
      user1,
      note,
      cartItems,
      loadCart,
      totalPriceItem,
      totalPrice,
      discountAmount,
      finalTotal,
      discountInput,
      selectedDiscount,
      discounts,
      applyDiscountCode,
      handleDiscountInput,
      discountId,
      reservation,
      updateCountdown,
      countdownInterval,
      seconds,
      minutes,
      expirationTime,
      getInfo,
      info,
      userId,
      paymentMethod,
      cartKey
    };
  },
};
</script>

<style>
.section-title1 {
  background-color: #d32f2f;
  color: white;
  padding: 8px 12px;
  font-weight: bold;
  border-radius: 5px 5px 0 0;
  font-size: 18px;
}

.bi-plus-circle-fill {
  font-size: 40px;
  color: rgb(146, 145, 145) !important;
  display: flex;
  justify-content: center;
  align-items: center;
}

.list-product-scroll1 {
  max-height: 135px;
  overflow-y: auto;
}
</style>
