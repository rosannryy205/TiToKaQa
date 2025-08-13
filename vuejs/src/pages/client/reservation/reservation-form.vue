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
                <div class="me-2">
                  <strong class="me-2">{{ formatNumber(totalPriceItem(item)) }} VNĐ</strong>
                  <span class="badge text-bg-secondary" @click="openModalToEditTopping(item, index)"
                    style="cursor: pointer;">Sửa</span>
                </div>
              </div>
            </div>
          </div>
          <div class="pt-1" v-if="cart_reservation == null">
            <button class="btn btn-outline-danger rounded-1 p-2 w-100">Đặt bàn</button>
          </div>
          <div class="card-payment1 border shadow-sm bg-white p-4 rounded-bottom" v-if="cart_reservation != null">
            <div class="d-flex justify-content-between mb-3">
              <strong class="fs-5">Tổng cộng (VAT)</strong>
              <strong class="text-danger fs-5">{{ formatNumber(totalPrice) }} VNĐ</strong>
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
                  v-model="paymentMethod" disabled />
                <label class="form-check-label d-flex align-items-center" for="momo">
                  <span class="me-2">Thanh toán qua Momo</span>
                  <img src="/img/momo.png" height="20" width="20" alt="" />
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment" id="momo" value="COD"
                  v-model="paymentMethod" />
                <label class="form-check-label d-flex align-items-center" for="momo">
                  <span class="me-2">Thanh toán qua COD</span>
                  <img src="/img/cod.png" height="20" width="20" alt="" />
                </label>
              </div>

            </div>

            <button type="submit" class="btn btn-danger1 w-100 mt-3">Thanh toán</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="modal fade" id="productModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content custom-modal modal-ct">
        <div class="modal-body position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
            aria-label="Close"></button>
          <div class="row">
            <!-- Cột hình ảnh -->
            <div class="col-md-6 border-end">
              <h5 class="fw-bold text-danger text-center mb-3">{{ foodDetail.name }}</h5>
              <div class="text-center mb-3">
                <img :src="getImageUrl(foodDetail.image)" :alt="foodDetail.name" class="modal-image img-fluid" />
              </div>
              <p class="text-danger fw-bold fs-5 text-center">
                {{ formatNumber(foodDetail.price) }} VNĐ
              </p>
              <p class="text-dark text-center text-lg fw-bold mb-3">{{ foodDetail.description }}</p>
            </div>

            <!-- Cột chọn topping -->
            <div class="col-md-6 d-flex flex-column">
              <form @submit.prevent="editCartIndex !== null ? updateToppingInCart() : addToCart()"
                class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                  <div class="topping-container mb-3" v-if="toppingList.length || spicyLevel.length">
                    <!-- Mức cay -->
                    <div class="mb-3" v-if="spicyLevel.length">
                      <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                      <select class="form-select" id="spicyLevel">
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>

                    <!-- Topping -->
                    <label v-if="toppingList.length" class="form-label fw-bold">🧀 Chọn Topping:</label>
                    <div v-for="topping in toppingList" :key="topping.id"
                      class="d-flex justify-content-between align-items-center mb-2">
                      <label class="d-flex align-items-center">
                        <input type="checkbox" :value="topping.id" name="topping[]" class="me-2" />
                        {{ topping.name }}
                      </label>
                      <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
                    </div>
                  </div>

                  <div v-else class="mt-5 none-topping">
                    <p class="text-center text-muted">Không có topping cho món này.</p>
                  </div>
                </div>

                <!-- Nút điều khiển -->
                <div class="mt-auto">
                  <div class="text-center mb-2">
                    <div class="qty-control px-2 py-1">
                      <button type="button" @click="decreaseQuantity" class="btn-lg"
                        style="background-color: #fff;">-</button>
                      <span>{{ quantity }}</span>
                      <button type="button" @click="increaseQuantity" class="btn-lg"
                        style="background-color: #fff;">+</button>
                    </div>
                  </div>

                  <!-- Nút động -->
                  <button type="submit" class="btn btn-danger w-100 fw-bold">
                    Cập nhật topping
                  </button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { User } from '@/stores/user'
import { ref, onMounted, onUnmounted } from 'vue'
import { Discounts } from '@/stores/discount'
import { Cart } from '@/stores/cart'
import numeral from 'numeral'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import { Info } from '@/stores/info-order-reservation'
import { FoodList } from '@/stores/food'
import { nextTick } from 'vue'

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
      foodDetail,
      spicyLevel,
      toppingList,
      openModal,
    } = FoodList.setup()

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
        if (!cart_reservation || cart_reservation.length === 0) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Vui lòng chọn món!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          return
        }
        if (!paymentMethod.value) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Vui lòng chọn phương thức thanh toán!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          return
        }
        const orderData = {
          id: orderId,
          guest_name: form.fullname || form.username,
          guest_phone: form.phone,
          guest_email: form.email,
          note: form.note || "",
          total_price: totalPrice.value,
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
          if (!orderId || totalPrice.value <= 0) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'info',
              title: 'Thông tin đơn hàng hoặc số tiền không hợp lệ để thanh toán VNPAY.',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
            return;
          }
          const paymentRes = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
            order_id: orderId,
            amount: totalPrice.value,
          })
          if (paymentRes.data && paymentRes.data.payment_url) {
            localStorage.setItem('order_id', orderId)
            localStorage.setItem('payment_method', paymentMethod.value)
            localStorage.removeItem(cartKey)
            window.location.href = paymentRes.data.payment_url
          } else {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'info',
              title: 'Không tạo được link thanh toán VNPAY.',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
          }
          return
        }
        if (paymentMethod.value === 'MOMO') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Chức năng thanh toán MoMo đang được phát triển!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          localStorage.setItem('order_id', orderId)
          localStorage.setItem('payment_method', paymentMethod.value);
          localStorage.removeItem(cartKey);
          // router.push('/payment-result');
          return
        }
        if (paymentMethod.value === 'COD') {
          await new Promise((resolve) => setTimeout(resolve, 300))
          await axios.post('http://127.0.0.1:8000/api/payments/cod-payment', {
            order_id: orderId,
            amount_paid: totalPrice.value,
          })
          localStorage.setItem('order_id', orderId)
          localStorage.setItem('payment_method', paymentMethod.value)
          localStorage.removeItem(cartKey)
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Đặt hàng và thanh toán bằng tiền mặt thành công!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          router.push({
            name: 'payment-result',
            query: {
              type: 'order_id',
              value: orderId
            }
          });
        }
      } catch (error) {
        console.error(error)
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
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `${validationErrors.trim()}`,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        } else {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Đặt bàn thất bại, vui lòng thử lại!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
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
    const quantity = ref(1)
    const editCartIndex = ref(null)
    const openModalToEditTopping = async (item, index) => {
      editCartIndex.value = index;
      quantity.value = item.quantity
      try {
        await openModal(item)

        await nextTick(() => {
          // Set mức cay đã chọn
          const spicy = item.toppings.find(t => t.is_spicy_level);
          if (spicy) {
            const select = document.getElementById('spicyLevel');
            if (select) {
              select.value = spicy.id;
            }
          }

          // Set topping đã chọn
          const selectedToppingIds = item.toppings
            .filter(t => !t.is_spicy_level)
            .map(t => t.id);
          const checkboxes = document.querySelectorAll('input[name="topping[]"]');
          checkboxes.forEach((checkbox) => {
            checkbox.checked = selectedToppingIds.includes(parseInt(checkbox.value));
          });
        });

      } catch (error) {
        console.error(' Lỗi khi mở modal chọn lại topping:', error);
      }
    };


    const updateToppingInCart = () => {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user?.id || 'guest';
      const cartKey = `cart_${userId}_reservation_${orderId}`;
      let cart = JSON.parse(localStorage.getItem(cartKey)) || [];

      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value);
      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId);

      let allSelectedToppings = [];

      if (selectedSpicy) {
        allSelectedToppings.push({
          id: selectedSpicy.id,
          name: selectedSpicy.name,
          price: selectedSpicy.price,
          food_toppings_id: selectedSpicy.pivot?.id || null,
          is_spicy_level: true
        });
      }

      const selectedToppingIds = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked')
      ).map((el) => parseInt(el.value));

      const normalToppings = toppingList.value
        .filter((topping) => selectedToppingIds.includes(topping.id))
        .map((topping) => ({
          id: topping.id,
          name: topping.name,
          price: topping.price,
          food_toppings_id: topping.pivot?.id || null,
          is_spicy_level: false
        }));

      allSelectedToppings = [...allSelectedToppings, ...normalToppings];

      const updatedItem = {
        ...cart[editCartIndex.value],
        toppings: allSelectedToppings,
        quantity: quantity.value
      };

      // Kiểm tra xem món mới này đã tồn tại trong giỏ chưa (trừ chính nó)
      const duplicateIndex = cart.findIndex(
        (item, i) =>
          i !== editCartIndex.value &&
          item.id === updatedItem.id &&
          JSON.stringify(item.toppings.map(t => t.id).sort()) === JSON.stringify(updatedItem.toppings.map(t => t.id).sort())
      );

      if (duplicateIndex !== -1) {
        // Nếu trùng món khác → cộng dồn số lượng, xóa item hiện tại
        cart[duplicateIndex].quantity += updatedItem.quantity;
        cart.splice(editCartIndex.value, 1);
      } else {
        // Nếu không trùng → cập nhật món hiện tại
        cart[editCartIndex.value] = updatedItem;
      }

      // Lưu lại
      localStorage.setItem(cartKey, JSON.stringify(cart));
      cartItems.value = cart;

      // Reset
      editCartIndex.value = null;
      document.querySelector('#productModal .btn-close')?.click();
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Đã cập nhật topping thành công!',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });
    };

    const increaseQuantity = () => {
      quantity.value++
    }

    const decreaseQuantity = () => {
      if (quantity.value > 1) quantity.value--
    }
    onMounted(async () => {
      try {
        await getInfo("order", orderId);

        const orderInfo = info.value;
        console.log('ss' + JSON.stringify(orderInfo.tables));

        if (!orderInfo || !orderInfo.id || orderInfo.tables.length === 0) {
          await Swal.fire({
            icon: 'error',
            text: 'Đơn hàng không tồn tại hoặc không phải đơn đặt bàn!',
            confirmButtonText: 'Quay lại',
            confirmButtonColor: '#d32f2f',
          });
          return router.push('/reservation');
        }


        const forbiddenStatuses = ['Đã xác nhận', 'Đang xử lý', 'Hoàn Thành', 'Đã Hủy', 'Giao thành công'];
        if (
          forbiddenStatuses.includes(orderInfo.order_status) ||
          forbiddenStatuses.includes(orderInfo.reservation_status)
        ) {
          await Swal.fire({
            icon: 'warning',
            text: 'Đơn hàng đã được xác nhận hoặc hoàn tất. Không thể tiếp tục!',
            confirmButtonText: 'Quay lại',
            confirmButtonColor: '#d32f2f',
          });
          return router.push('/reservation');
        }

        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
      } catch (error) {
        console.error('Lỗi khi load thông tin đơn hàng:', error);
        await Swal.fire({
          icon: 'error',
          text: 'Đã xảy ra lỗi khi kiểm tra đơn hàng!',
          confirmButtonText: 'Quay lại',
          confirmButtonColor: '#d32f2f',
        });
        router.push('/reservation');
      }
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
      cart_reservation,
      editCartIndex,
      openModalToEditTopping,
      updateToppingInCart,
      foodDetail,
      spicyLevel,
      toppingList,
      quantity,
      openModal,
      increaseQuantity,
      decreaseQuantity,

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
