<template>
  <div class="row d-flex text-center">
    <div class="title-shops1 d-sm-block fw-bold mt-4">
      <span class="fs-1">ĐẶT BÀN CÙNG CHÚNG TÔI!</span>
    </div>
  </div>
  <div v-if="isLoading" class="loader-wrapper">
    <div class="loader"></div>
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
              <label for="" class="form-label">Tên của bạn <b class="text-danger">*</b></label>
              <input type="text" class="form-control rounded border shadow-sm" placeholder="Tên của bạn"
                v-model="form.fullname" required />
            </div>
            <div class="mb-3">
              <label for="" class="form-label">Email</label>
              <input type="email" class="form-control rounded border shadow-sm" placeholder="Email" v-model="form.email"
                required />
            </div>
            <div class="mb-3">
              <label for="" class="form-label">Số điện thoại <b class="text-danger">*</b></label>
              <input type="text" class="form-control rounded border shadow-sm" placeholder="Số điện thoại"
                v-model="form.phone" required />
            </div>
            <div class="mb-3">
              <label for="" class="form-label">Ghi chú</label>
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
          <div class="section-title1 d-flex justify-between">
            <div>Thanh Toán</div>
            <div v-if="cart_reservation != null"><router-link :to="`/food/${orderId}`" class="fs-6 text-white pb-2">Thêm
                món</router-link></div>
          </div>
          <div class="border pt-4">
            <div v-if="!cartItems.length > 0">
              <router-link :to="`/food/${orderId}`" class="bi bi-plus-circle-fill pb-2"></router-link>
              <div class="text-center fw-medium fs-6 pb-4">
                Chọn món trước khi đến nhà hàng
              </div>
            </div>
            <div class="list-product-scroll1 mb-3" v-if="cart_reservation != null">
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
          <div class="card-payment1 border shadow-sm bg-white p-4 rounded-bottom" v-if="cart_reservation != null">

            <div class="d-flex justify-content-between mb-3">
              <strong class="fs-5">Tổng cộng (VAT)</strong>
              <strong class="text-danger fs-5">{{ formatNumber(finalTotal) }} VNĐ</strong>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Phương thức thanh toán</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment" id="vnpay" value="VNPAY"
                  v-model="paymentMethod" />
                <label class="form-check-label d-flex align-items-center" for="vnpay">
                  <span class="me-2">Thanh toán qua VNPAY</span>
                  <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="" />
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment" id="momo" value="MOMO"
                  v-model="paymentMethod" />
                <label class="form-check-label d-flex align-items-center" for="momo">
                  <span class="me-2">Thanh toán qua Momo</span>
                  <img src="/img/momo.png" height="20" width="20" alt="" />
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
    const cart_reservation = JSON.parse(localStorage.getItem(`cart_${userId}_reservation_${orderId}`)) || null;
    const minutes = ref(5);
    const seconds = ref(0);
    let countdownInterval = null;
    const { form, user } = User.setup();
    const { getInfo, info } = Info.setup();
    const {
      discounts,
      discountInput,
      selectedDiscount,
      discountId,
      applyDiscountCode,
      handleDiscountInput,
      finalTotal,
      discountFoodAmount,
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
          localStorage.removeItem(`cart_${userId}_reservation_${orderId}`)
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

        }

        minutes.value = Math.floor((diff / 1000 / 60) % 60);
        seconds.value = Math.floor((diff / 1000) % 60);
      } catch (error) {
        console.log(error);
      }
    };

    const paymentMethod = ref('')
    const check_payment = async (orderId) => {
      isLoading.value = true

      try {
        if (!paymentMethod.value) {
          toast.error('Vui lòng chọn phương thức thanh toán!')
          return
        }
        const orderData = {
          id: orderId,
          guest_name: form.value.fullname || form.value.username,
          guest_phone: form.value.phone,
          guest_email: form.value.email,
          note: form.value.note || "",
          total_price: finalTotal.value,
          money_reduce: discountFoodAmount.value,
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
            order_id: orderId,
          });
        }
        localStorage.setItem("payment_method", paymentMethod.value);
        const cartKey = `cart_${userId}_reservation_${orderId}`;
        localStorage.removeItem(cartKey);

        if (paymentMethod.value === 'VNPAY') {
          if (!orderId || finalTotal.value <= 0) {
            toast.error('Thông tin đơn hàng hoặc số tiền không hợp lệ để thanh toán VNPAY.');
            return;
          }
          const paymentRes = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
            order_id: orderId,
            amount: finalTotal.value,
          })
          if (paymentRes.data && paymentRes.data.payment_url) {
            localStorage.setItem('payment_method', paymentMethod.value)
            localStorage.removeItem(cartKey)
            window.location.href = paymentRes.data.payment_url
          } else {
            toast.error('Không tạo được link thanh toán VNPAY.')
          }
          return
        }
        if (paymentMethod.value === 'MOMO') {
          toast.info('Chức năng thanh toán MoMo đang được phát triển!');
          localStorage.setItem('payment_method', paymentMethod.value);
          localStorage.removeItem(cartKey);
          // router.push('/payment-result');
          return
        }
        if (paymentMethod.value === 'COD') {
          await new Promise((resolve) => setTimeout(resolve, 300))
          await axios.post('http://127.0.0.1:8000/api/payments/cod-payment', {
            order_id: orderId,
            amount_paid: finalTotal.value + 100000,
          })
          localStorage.setItem('payment_method', paymentMethod.value)
          localStorage.removeItem(cartKey)
          toast.success('Đặt hàng và thanh toán bằng tiền mặt thành công!')
          router.push('/payment-result');
        }
      } catch (error) {
        if (error.response && error.response.status === 422 && error.response.data.errors) {
          let validationErrors = ''
          for (const field in error.response.data.errors) {
            const fieldErrors = error.response.data.errors[field];
            if (Array.isArray(fieldErrors)) {
              validationErrors += fieldErrors.join(' ') + ' ';
            } else if (typeof fieldErrors === 'string') {
              validationErrors += fieldErrors + ' ';
            } else {
              validationErrors += 'Lỗi không xác định. ';
            }
          }

          toast.error(`${validationErrors.trim()}`)
        } else {
          toast.error('Đặt bàn thất bại, vui lòng thử lại!')
        }

      } finally {
        isLoading.value = false
      }
    };
    const reservation = async () => {
      try {
        console.log('✅ form gửi đi:', form.value)
        await check_payment(orderId)

        console.log('✅ check_out đã được gọi xong')
      } catch (error) {
        console.error('❌ Lỗi khi gọi check_out:', error)
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
      discountFoodAmount,
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
      cartKey,
      isLoading,
      cart_reservation
    };
  },
};
</script>

<style scoped>
.section-title1 {
  background-color: #d32f2f;
  color: white;
  padding: 8px 12px;
  font-weight: bold;
  border-radius: 5px 5px 0 0;
  font-size: 18px;
  display: flex;
  justify-content: space-between;
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

.loader-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* loader */
.loader {
  width: 50px;
  --b: 8px;
  aspect-ratio: 1;
  border-radius: 50%;
  padding: 1px;
  background: conic-gradient(#0000 10%, #f03355) content-box;
  -webkit-mask:
    repeating-conic-gradient(#0000 0deg, #000 1deg 20deg, #0000 21deg 36deg),
    radial-gradient(farthest-side, #0000 calc(100% - var(--b) - 1px), #000 calc(100% - var(--b)));
  -webkit-mask-composite: destination-in;
  mask-composite: intersect;
  animation: l4 1s infinite steps(10);
}

@keyframes l4 {
  to {
    transform: rotate(1turn);
  }
}
</style>
