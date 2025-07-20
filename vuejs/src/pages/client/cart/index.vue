<template>
  <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div v-else-if="cartItems.length > 0" class="container-sm fade-in">
    <span style="color: #000;"><router-link to="/home" style="text-decoration: none; color: #000; ">Trang
        chủ</router-link> / Giỏ hàng</span>
    <h3 class="mb-4">Giỏ hàng của bạn</h3>
    <div class="row">
      <!-- Danh sách sản phẩm -->
      <div class="col-12 col-lg-8 mb-4">
        <!-- Sản phẩm -->
        <div class="card mb-3" v-for="(item, index) in cartItems" :key="index">
          <div class="card-body d-flex align-items-center flex-wrap">
            <i class="bi bi-x-circle me-3 mb-2" style="cursor: pointer" @click="removeItem(index)"></i>
            <img :src="'http://127.0.0.1:8000/storage/img/food/' + item.image" class="cart-img me-3 mb-2"
              alt="Mì kim chi Nha Trang" />

            <div class="flex-grow-1 mb-2">

              <h5 class="mb-1 product-title"><strong>{{ item.name }}</strong></h5>
              <p class="text-muted mb-2">{{ item.spicyLevel }}</p>

              <p class="text-muted mb-2">
                Topping:
                <span v-if="item.toppings && item.toppings.length">
                  <ul>
                    <li v-for="(topping, index) in item.toppings" :key="index">
                      {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ
                    </li>
                  </ul>
                </span>
                <span v-else>Không có</span>
              </p>
              <p class="text-muted mb-2">Số lượng: {{ item.quantity }}</p>
              <p class="mb-0 "><strong>Giá: </strong>{{ formatNumber(item.price) }} VNĐ</p>

            </div>

            <div class="text-center me-5 mb-2">
              <div class="qty-control rounded px-2 py-1 d-inline-flex align-items-center gap-2">
                <button class="btn btn-sm px-2 py-0" @click="decreaseQuantity(index)">-</button>
                <span class="qty-box text-center">{{ item.quantity }}</span>
                <button class="btn btn-sm px-2 py-0" @click="increaseQuantity(index)">+</button>
              </div>
            </div>

            <div class="mb-2 price text-end fixed-price-width">
              <strong class="price-text">{{ formatNumber(totalPriceItem(item)) }} VNĐ</strong>
            </div>
            <!-- Nút chọn topping góc dưới bên phải -->
            <button class="btn btn-outline-primary btn-sm position-absolute m-2" style="bottom: 0; right: 0;"
              @click="openModalToEditTopping(item, index)">
              Topping
            </button>



          </div>

        </div>

      </div>

      <!-- Thông tin thanh toán -->
      <div class="col-12 col-lg-4">
        <div class="payment-box">
          <h5 class="mb-3">Thông tin thanh toán</h5>
          <div class="d-flex justify-content-between">
            <span>Giá sản phẩm</span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span>Vận chuyển</span>
            <span>Tính khi thanh toán</span>
          </div>
          <hr />
          <div class="d-flex justify-content-between">
            <span><strong>Tổng tiền thanh toán</strong></span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <button @click="goToCheckout" class="btn btn-checkout w-100 mt-4">Thanh toán ngay</button>
          <div class="mt-4 d-flex align-items-center flex-wrap">
            <i class="bi bi-telephone-fill me-2 fs-4"></i>
            <div>
              <small>Hotline hỗ trợ (8h – 22h)</small><br />
              <strong class="text-danger">09123456789</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="container-sm fade-in">
    <span style="color: #000;"><router-link to="/home" style="text-decoration: none; color: #000; ">Trang
        chủ</router-link> / Giỏ hàng</span>
    <h3 class="mb-4">Giỏ hàng của bạn</h3>
    <div class="row">
      <!-- Danh sách sản phẩm -->
      <div class="col-12 col-lg-8 mb-4">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
          <div class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
              <img src="/public/img/giohang.png" alt="Giỏ hàng rỗng" width="60px">
              <p class="mb-0">Chưa có sản phẩm trong giỏ hàng</p>
            </div>
            <router-link to="/food">
              <button class="btn btn-checkout mt-2">Tiếp tục mua sắm</button>
            </router-link>
          </div>
        </div>
      </div>


      <!-- Thông tin thanh toán -->
      <div class="col-12 col-lg-4">
        <div class="payment-box">
          <h5 class="mb-3">Thông tin thanh toán</h5>
          <div class="d-flex justify-content-between">
            <span>Giá sản phẩm</span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span>Vận chuyển</span>
            <span>Tính khi thanh toán</span>
          </div>
          <hr />
          <div class="d-flex justify-content-between">
            <span><strong>Tổng tiền thanh toán</strong></span>
            <strong>{{ formatNumber(totalPrice) }} VNĐ</strong>
          </div>
          <button @click="goToCheckout" class="btn btn-checkout w-100 mt-4" :disabled="loading">Thanh toán ngay</button>
          <div class="mt-4 d-flex align-items-center flex-wrap">
            <i class="bi bi-telephone-fill me-2 fs-4"></i>
            <div>
              <small>Hotline hỗ trợ (8h – 22h)</small><br />
              <strong class="text-danger">09123456789</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- modal chọn lại topping  -->
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
                <img :src="'http://127.0.0.1:8000/storage/img/food/' + foodDetail.image" :alt="foodDetail.name"
                  class="modal-image img-fluid" />
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router';
import numeral from 'numeral'
import { computed } from 'vue'
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

import { onBeforeRouteLeave } from 'vue-router'
import { nextTick } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'
export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },

  setup() {
    const toast = useToast();
    const cartItems = ref([])
    const router = useRouter();
    const loading = ref(true);

    const getCartKey = () => {
      const user = JSON.parse(localStorage.getItem('user'))
      const userId = user?.id || 'guest'
      return `cart_${userId}`
    }

    const loadCart = () => {
      const cartKey = getCartKey()
      const storedCart = localStorage.getItem(cartKey)
      if (storedCart) {
        cartItems.value = JSON.parse(storedCart)
      } else {
        cartItems.value = []
      }
      setTimeout(() => {
        loading.value = false
      }, 300)
    }

    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        const basePrice = Number(item.price) * item.quantity
        const toppingPrice = item.toppings.reduce((tsum, topping) => {
          return tsum + (Number(topping.price) * item.quantity)
        }, 0)
        return sum + basePrice + toppingPrice
      }, 0)
    })


    const totalPriceItem = (item) => {
      const itemPrice = Number(item.price) * item.quantity;
      const toppingPrice = item.toppings.reduce((sum, topping) => {
        return sum + (Number(topping.price) * item.quantity);
      }, 0);
      return itemPrice + toppingPrice;
    };

    const updateCartStorage = () => {
      const cartKey = getCartKey()
      localStorage.setItem(cartKey, JSON.stringify(cartItems.value))
    }

    const decreaseQuantity = (index) => {
      if (cartItems.value[index].quantity > 1) {
        cartItems.value[index].quantity--
        updateCartStorage()
      }
    }

    const increaseQuantity = (index) => {
      cartItems.value[index].quantity++
      updateCartStorage()
    }

    const removeItem = async (index) => {
      const result = await Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: 'Món này sẽ bị xóa khỏi giỏ hàng!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
      })

      if (result.isConfirmed) {
        cartItems.value.splice(index, 1)
        updateCartStorage()
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Đã xóa món khỏi giỏ hàng!',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        })
      }
    }


    const goToCheckout = () => {
      if (cartItems.value.length === 0) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'info',
          title: 'Giỏ hàng của bạn đang trống',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
        return;
      }else {
        router.push('/payment_if');
      }
    }



    // chọn lại topping

    const foodDetail = ref({})
    const toppings = ref([])
    const toppingList = ref([])
    const spicyLevel = ref([])
    const quantity = ref(1)

    const openModal = async (item) => {
      foodDetail.value = {}
      toppings.value = []
      spicyLevel.value = []
      toppingList.value = []
      quantity.value = 1
      try {
        if (item.type === 'food') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`)
          foodDetail.value = { ...res.data, type: 'Food' }
          console.log(foodeDetail.value);
          const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`)
          toppings.value = res1.data
          console.log(toppings.value);
          spicyLevel.value = toppings.value.filter((item) => item.category_id == 15)
          toppingList.value = toppings.value.filter((item) => item.category_id == 16)
          toppingList.value.forEach((item) => {
            item.price = item.price || 0
          })
        } else if (item.type === 'combo') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`)
          foodDetail.value = { ...res.data, type: 'Combo' }
        }

        const modalElement = document.getElementById('productModal')
        if (modalElement) {
          const modal = new Modal(modalElement)
          modal.show()
        }
      } catch (error) {
        console.error(error)
      }
    }

    const editCartIndex = ref(null)
    const openModalToEditTopping = async (item, index) => {
      editCartIndex.value = index;
      quantity.value = item.quantity;

      try {
        // Gọi API để lấy lại thông tin món (food hoặc combo)
        let res;
        if (item.type === 'Food') {
          res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`);
          foodDetail.value = { ...res.data, type: 'Food' };

          const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`);
          toppings.value = res1.data;

          spicyLevel.value = toppings.value.filter((i) => i.category_id == 15);
          toppingList.value = toppings.value.filter((i) => i.category_id == 16);
          toppingList.value.forEach((i) => {
            i.price = i.price || 0;
          });
        } else if (item.type === 'Combo') {
          res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`);
          foodDetail.value = { ...res.data, type: 'Combo' };
        }

        // ⚠️ Phải mở modal tại đây — đảm bảo sau khi foodDetail đã có
        const modalElement = document.getElementById('productModal');
        if (modalElement) {
          const modal = new Modal(modalElement);
          modal.show();
        }

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
      const cartKey = `cart_${userId}`;
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

      toast.success(' Đã cập nhật topping thành công!');
    };


    onMounted(() => {
      loadCart();
    });

    onBeforeRouteLeave((to, from, next) => {
      if (to.path === '/payment_if') {
        return next()
      }

      const originalLength = cartItems.value.length

      // Lọc lại: chỉ giữ item không phải deal
      const filteredCart = cartItems.value.filter(item => !item.is_deal)

      // Nếu có item deal bị xóa
      if (filteredCart.length !== originalLength) {
        if (filteredCart.length === 0) {
          localStorage.removeItem(getCartKey())
        } else {
          localStorage.setItem(getCartKey(), JSON.stringify(filteredCart))
        }

        cartItems.value = filteredCart
      }

      next()
    })


    return {
      Modal,
      cartItems,
      totalPrice,
      increaseQuantity,
      decreaseQuantity,
      removeItem,
      totalPriceItem,
      goToCheckout,
      loading,
      foodDetail,
      toppings,
      toppingList,
      spicyLevel,
      quantity,
      editCartIndex,
      openModalToEditTopping,
      updateToppingInCart

    }
  }
}
</script>
<style scoped>
.quantity-box {
  display: inline-block;
  width: 15px;
  text-align: center;
}

.btn-disabled {
  background-color: #dcdcdc;
  cursor: not-allowed;
}
</style>
