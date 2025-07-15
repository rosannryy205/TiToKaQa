<template>
  <!-- top header-->
  <div class="header position-sticky top-0 bg-white bg-opacity-90 shadow-sm z-3">
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid px-0">
          <div class="d-flex align-items-center">
            <button class="navbar-toggler me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
              aria-controls="offcanvasMenu" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/home">
              <img src="/img/logonew.png" alt="Logo" class="logo" width="80px">
            </a>
          </div>

          <div class="d-none d-lg-flex align-items-center ms-auto ">
            <form @submit.prevent="searchProduct">
              <div class="input-wrapper position-relative me-3" ref="wrapperRef">
                <button class="icon-search-submit" type="submit">
                  <svg width="23px" height="23px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                      stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round"></path>
                  </svg>
                </button>
                <input v-model="searchQuery" type="text" class="input-search" placeholder="search..."
                  @input="handleInput" @focus="() => { handleInput(); showSuggestions = true; }"
                  @keydown.enter="searchProduct" />

                <!-- Dropdown gợi ý -->
                <ul v-if="suggestions.length && showSuggestions" class="suggestion-dropdown"
                  @scroll.passive="handleScroll">
                  <li v-for="(item, index) in suggestions" :key="index" @click="selectItem(item)">
                    <img style="width: 50px;" :src="'http://127.0.0.1:8000/storage/img/food/' + item.image"
                      :alt="item.name" class="me-2  img-search" />
                    <div class="info-search">
                      <div class="name-search">{{ item.name }}</div>
                      <div class="price-search">{{ item.price.toLocaleString() }}₫</div>
                    </div>


                  </li>

                  <li v-if="loading" class="loading"><span v-if="loading"
                      class="spinner-border spinner-border-sm me-2"></span> Đang tải thêm...</li>
                  <li v-if="!hasMore && !loading" class="no-more">Đã hết kết quả</li>
                </ul>
              </div>
            </form>

            <div class="me-2">
              <router-link to="/login" v-if="!isLoggedIn" class="text-decoration-none text-primary-black">
                <button class="icon-btn me-2">
                  <i class="bi bi-people"></i>
                </button>
              </router-link>
              <template v-else>
                <div class="d-flex align-items-center">
                  <router-link to="/account" class="text-decoration-none text-primary-red me-2">
                    <p v-if="user.username" class="mb-0 username-display">{{ user.username }}</p>
                  </router-link>
                  <button class="icon-btn" @click="handleLogout" title="Đăng xuất">
                    <i class="bi bi-box-arrow-right"></i> </button>
                </div>
              </template>
            </div>


            <div>
              <router-link to="/cart" class="icon-btn text-dark" title="Giỏ hàng">
                <i class="bi bi-cart"></i>
              </router-link>
            </div>
          </div>

        </div>
      </nav>

      <nav class="navbar navbar-expand-lg navbar-bottom d-none d-lg-block pt-0">
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav main-nav-links">
            <li class="nav-item"><router-link class="nav-link" to="/home">Trang chủ</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/food">Thực đơn</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/reservation">Đặt bàn</router-link></li>
          </ul>
        </div>
      </nav>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav offcanvas-nav-links mb-4">
          <li class="nav-item"><router-link class="nav-link" to="/home">Trang chủ</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/food">Thực đơn</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/reservation">Đặt bàn</router-link></li>
        </ul>

        <div class="mobile-actions">
          <div class="input-wrapper position-relative mb-3">
            <button class="icon-search-submit" type="button"> <svg width="23px" height="23px" viewBox="0 0 24 24"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                  stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                </path>
              </svg>
            </button>
            <input type="text" class="input-search" placeholder="search..." />
          </div>

          <div class="d-flex flex-column align-items-start">
            <router-link v-if="!isLoggedIn" to="/login" class="text-decoration-none text-primary-black">
              <button class="icon-btn text-dark mb-2">
                <i class="bi bi-people me-2"></i> Đăng nhập
              </button>
            </router-link>
            <template v-else>
              <div class="mb-2">
                <router-link to="/account/update-user" class="text-decoration-none text-primary-red me-2">
                  <p v-if="user.username" class="mb-0 username-display"><i class="bi bi-person me-2"></i>{{
                    user.username }}</p>
                </router-link>
              </div>
              <button class="icon-btn text-dark mb-2" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
              </button>
            </template>

            <!-- <router-link to="/delivery" class="icon-btn text-dark mb-2">
              <i class="bi bi-truck me-2"></i> Theo dõi đơn hàng
            </router-link> -->
            <router-link to="/cart" class="icon-btn text-dark mb-2">
              <i class="bi bi-cart me-2"></i> Giỏ hàng
            </router-link>
            <a href="tel:YOUR_PHONE_NUMBER" class="icon-btn text-dark"> <i class="bi bi-telephone me-2"></i> Liên hệ
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="searchModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content custom-modal modal-ct">
        <div class="modal-body position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
            aria-label="Close"></button>
          <div class="row">
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
            <div class="col-md-6 d-flex flex-column">
              <form @submit.prevent="addToCart" class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                  <div class="topping-container mb-3" v-if="toppingList.length">
                    <div class="mb-3" v-if="spicyLevel.length">
                      <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                      <select class="form-select" id="spicyLevel">
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                    <label class="form-label fw-bold">🧀 Chọn Topping:</label>
                    <div v-for="topping in toppingList" :key="topping.id"
                      class="d-flex justify-content-between align-items-center mb-2">
                      <label class="d-flex align-items-center">
                        <input type="checkbox" :value="topping.id" name="topping[]" class="me-2" />
                        {{ topping.name }}
                      </label>
                      <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
                    </div>
                  </div>
                  <div v-else class="mt-5">
                    <p class="text-center text-muted">Không có topping cho món này.</p>
                  </div>
                </div>
                <!---->
                <div class="mt-auto">

                  <div class="text-center mb-2">
                    <div class="qty-control px-2 py-1">
                      <button @click="decreaseQuantity" type="button" class="btn-lg"
                        style="background-color: #fff;">-</button>
                      <span>{{ quantity }}</span>
                      <button @click="increaseQuantity" type="button" class="btn-lg"
                        style="background-color: #fff;">+</button>
                    </div>

                  </div> <button class="btn btn-danger w-100 fw-bold">🛒 Thêm vào giỏ hàng</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <router-view></router-view>

</template>
<script setup>
import { useAuthStore } from '@/stores/auth';
import { useUserStore } from '@/stores/userAuth';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import * as bootstrap from 'bootstrap';
import { toast } from 'vue3-toastify';
import Swal from 'sweetalert2';

// const { formattedTime, isCounting, startCountdown } = useCountdown(60);
const auth = useAuthStore();
//Google
const loginWithGoogle = () => {
  window.location.href = 'http://localhost:8000/api/auth/google/redirect';
};

const router = useRouter();
window.bootstrap = bootstrap;
//  Đăng xuất
const handleLogout = async () => {
  const confirmResult = await Swal.fire({
    title: 'Đăng xuất khỏi hệ thống?',
    text: 'Bạn sẽ cần đăng nhập lại để tiếp tục sử dụng!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Đăng xuất',
    cancelButtonText: 'Huỷ bỏ',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    reverseButtons: true,
    focusCancel: true,
  });

  if (!confirmResult.isConfirmed) {
    return;
  }

  try {
    await axios.post('http://127.0.0.1:8000/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    localStorage.removeItem('user');
    localStorage.removeItem('token');
    localStorage.removeItem('conversation_id');
    user.value = null;
    isLoggedIn.value = false;

    await Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đăng xuất thành công',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    });

    window.location.href = '/home';
  } catch (error) {
    console.error('Lỗi đăng xuất:', error);
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Có lỗi xảy ra khi đăng xuất',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true,
    });
  }
};



const userStore = useUserStore();

const isLoggedIn = computed(() => userStore.isLoggedIn);
const user = computed(() => userStore.user);
// }const searchQuery = ref('')
const loading = ref(false)
const searchQuery = ref(''); // Từ khóa tìm kiếm
const suggestions = ref([]); // Danh sách kết quả
const offset = ref(0); // Vị trí bắt đầu
const limit = 5; // Số kết quả mỗi lần
const hasMore = ref(true); // Kiểm tra có còn dữ liệu để tải thêm không
const showSuggestions = ref(false); // Biến để điều khiển dropdown
const wrapperRef = ref(null); // Ref để gắn vào input-wrapper
const foodDetail = ref({});
const toppings = ref([]);
const spicyLevel = ref([]);
const toppingList = ref([]);
const quantity = ref(1);
const order_id = parseInt(localStorage.getItem('order_id'))


const formatNumber = (num) => new Intl.NumberFormat().format(num);
const getImageUrl = (img) => `http://127.0.0.1:8000/storage/img/food/${img}`;

// Hàm lấy dữ liệu từ API
const fetchSuggestions = async () => {
  if (loading.value || !searchQuery.value.trim() || !hasMore.value) return;

  loading.value = true;
  try {
    const res = await axios.get('http://localhost:8000/api/search', {
      params: {
        search: searchQuery.value,
        offset: offset.value,
        limit: limit,
      },
    });

    const results = res.data.results || [];
    const total = res.data.total || 0;

    console.log("Load thêm:", results.length, "offset:", offset.value, "total:", total);

    suggestions.value.push(...results);

    offset.value += results.length;
    hasMore.value = offset.value < total;
  } catch (error) {
    console.error('Lỗi khi fetch gợi ý:', error);
  } finally {
    loading.value = false;
  }
};
// Hàm debounce để tránh gọi API quá nhanh
function debounce(fn, delay = 300) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn.apply(this, args), delay);
  };
}

// Hàm xử lý khi người dùng nhập từ khóa tìm kiếm
const handleInput = debounce(() => {
  if (searchQuery.value.trim()) {
    offset.value = 0;
    suggestions.value = [];
    hasMore.value = true;
    showSuggestions.value = true;
    fetchSuggestions();
  } else {
    suggestions.value = [];
    showSuggestions.value = false;
  }

});





// Hàm xử lý cuộn để tải thêm dữ liệu
const handleScroll = (e) => {
  console.log("Đang scroll suggestion dropdown...");
  const el = e.target;
  if (
    el.scrollTop + el.clientHeight >= el.scrollHeight - 10 &&
    hasMore.value &&
    !loading.value
  ) {
    console.log("Gần cuối dropdown, tải thêm...");
    fetchSuggestions();
  }
};





const selectItem = (item) => {
  searchQuery.value = item.name;
  showSuggestions.value = false;
  suggestions.value = [];

  openModal(item);
};

const openModal = async (item) => {
  foodDetail.value = {};
  toppings.value = [];
  spicyLevel.value = [];
  toppingList.value = [];
  quantity.value = 1;

  try {
    if (item.type === 'food') {
      const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`);
      foodDetail.value = { ...res.data, type: 'Food' };

      const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`);
      toppings.value = res1.data;

      spicyLevel.value = toppings.value.filter((tp) => tp.category_id == 1);
      toppingList.value = toppings.value.filter((tp) => tp.category_id == 2);

      toppingList.value.forEach((tp) => {
        tp.price = tp.price || 0;
      });
    } else if (item.type === 'combo') {
      const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`);
      foodDetail.value = { ...res.data, type: 'Combo' };
    }

    const modalElement = document.getElementById('searchModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement); // dùng bootstrap.Modal
      modal.show();
    }
  } catch (error) {
    console.error('Lỗi khi mở modal chi tiết:', error);
  }
};


// Hàm tìm kiếm sản phẩm khi người dùng nhấn Enter hoặc submit
const searchProduct = () => {
  if (searchQuery.value.trim()) {
    showSuggestions.value = false;
    router.push({
      path: '/search', // đường dẫn của route
      query: { search: searchQuery.value }
    });
  }
};

// Hàm xử lý khi người dùng click ngoài để ẩn dropdown
const handleClickOutside = (e) => {
  if (wrapperRef.value && !wrapperRef.value.contains(e.target)) {
    showSuggestions.value = false;
  }
};

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value -= 1
  }
}

const increaseQuantity = () => {
  quantity.value += 1
}

const addToCart = () => {
  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id || 'guest'
  const cartKey = `cart_${userId}`

  const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)
  const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)
  const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không cay'

  const selectedToppingId = Array.from(
    document.querySelectorAll('input[name="topping[]"]:checked')
  ).map((el) => parseInt(el.value))

  const selectedToppings = toppingList.value
    .filter((topping) => selectedToppingId.includes(topping.id))
    .map((topping) => ({
      id: topping.id,
      name: topping.name,
      price: topping.price,
      food_toppings_id: topping.pivot?.id || null
    }))

  const cartItem = {
    id: foodDetail.value.id,
    name: foodDetail.value.name,
    image: foodDetail.value.image,
    price: foodDetail.value.price,
    spicyLevel: selectedSpicyName,
    toppings: selectedToppings,
    quantity: quantity.value,
    type: foodDetail.value.type,
  }

  let cart = JSON.parse(localStorage.getItem(cartKey)) || []

  const existingItem = cart.findIndex(
    (item) =>
      item.id === cartItem.id &&
      item.spicyLevel === cartItem.spicyLevel &&
      JSON.stringify(item.toppings.sort()) === JSON.stringify(cartItem.toppings.sort())
  )

  if (existingItem !== -1) {
    cart[existingItem].quantity += 1
  } else {
    cart.push(cartItem)
  }

  localStorage.setItem(cartKey, JSON.stringify(cart))
  alert('Đã thêm vào giỏ hàng!')
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

</script>


<style scoped>
.text-primary-red {
  color: #ca111f;
}

.hover-scale {
  transition: transform 0.2s ease;
}

.hover-scale:hover {
  transform: scale(1.1);
}

.suggestion-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  max-height: 300px;
  /* 👈 Cố định chiều cao để buộc scroll */
  max-height: 270px;
  overflow-y: auto;
  background: #fff;
  border: 1px solid #ddd;
  z-index: 999;
  list-style: none;
  margin: 0;
  padding: 5px 0;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.suggestion-dropdown li {
  display: flex;
  align-items: flex-start;
  padding: 8px 12px;
  gap: 10px;
  cursor: pointer;
}

.suggestion-dropdown li:hover {
  background-color: #f6f6f6;
}

.img-search {
  width: 50px;
  object-fit: cover;
  border-radius: 5px;
}

.info-search {
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex: 1;
}

.name-search {
  font-size: 16px;
  font-weight: 500;
  color: #333;
}

.price-search {
  font-size: 14px;
  color: red;
}

.text-primary-black {
  color: black;
}

.loading,
.no-more {
  padding: 10px;
  text-align: center;
  color: #888;
}

.loader {
  width: fit-content;
  height: fit-content;
  display: flex;
  align-items: center;
  justify-content: center;
}

.truckWrapper {
  margin-right: 16px;
  width: 38px;
  height: 22px;
  display: flex;
  flex-direction: column;
  position: relative;
  align-items: center;
  justify-content: flex-end;
  overflow-x: hidden;
}

/* Truck body */
.truckBody {
  width: 26px;
  height: fit-content;
  margin-bottom: 1px;
  animation: motion 1s linear infinite;
}

/* Suspension animation */
@keyframes motion {
  0% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(0.5px);
  }

  100% {
    transform: translateY(0px);
  }
}

/* Tires */
.truckTires {
  width: 26px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0px 2px 0px 3px;
  position: absolute;
  bottom: 0;
}

.truckTires svg {
  width: 5px;
  height: 5px;
}

/* Road */
.road {
  width: 100%;
  height: 1px;
  background-color: #282828;
  position: relative;
  bottom: 0;
  align-self: flex-end;
  border-radius: 1px;
}

.road::before {
  content: "";
  position: absolute;
  width: 4px;
  height: 100%;
  background-color: #282828;
  right: -50%;
  border-radius: 1px;
  animation: roadAnimation 1.4s linear infinite;
  border-left: 1px solid white;
}

.road::after {
  content: "";
  position: absolute;
  width: 2px;
  height: 100%;
  background-color: #282828;
  right: -65%;
  border-radius: 1px;
  animation: roadAnimation 1.4s linear infinite;
  border-left: 0.5px solid white;
}

/* Lamp post */
.lampPost {
  position: absolute;
  bottom: 0;
  right: -90%;
  height: 18px;
  animation: roadAnimation 1.4s linear infinite;
}

@keyframes roadAnimation {
  0% {
    transform: translateX(0px);
  }

  100% {
    transform: translateX(-90px);
  }
}
</style>
