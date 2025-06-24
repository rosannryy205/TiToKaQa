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

            <!-- <div class="d-none d-lg-block">
              <router-link :to="{ name: 'delivery', params: { id: order_id   } }" style="color: black;">
                <div class="loader">
                  <div class="truckWrapper">
                    <div class="truckBody">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 198 93" class="trucksvg">
                        <path stroke-width="3" stroke="#282828" fill="#F83D3D"
                          d="M135 22.5H177.264C178.295 22.5 179.22 23.133 179.594 24.0939L192.33 56.8443C192.442 57.1332 192.5 57.4404 192.5 57.7504V89C192.5 90.3807 191.381 91.5 190 91.5H135C133.619 91.5 132.5 90.3807 132.5 89V25C132.5 23.6193 133.619 22.5 135 22.5Z">
                        </path>
                        <path stroke-width="3" stroke="#282828" fill="#7D7C7C"
                          d="M146 33.5H181.741C182.779 33.5 183.709 34.1415 184.078 35.112L190.538 52.112C191.16 53.748 189.951 55.5 188.201 55.5H146C144.619 55.5 143.5 54.3807 143.5 53V36C143.5 34.6193 144.619 33.5 146 33.5Z">
                        </path>
                        <path stroke-width="2" stroke="#282828" fill="#282828"
                          d="M150 65C150 65.39 149.763 65.8656 149.127 66.2893C148.499 66.7083 147.573 67 146.5 67C145.427 67 144.501 66.7083 143.873 66.2893C143.237 65.8656 143 65.39 143 65C143 64.61 143.237 64.1344 143.873 63.7107C144.501 63.2917 145.427 63 146.5 63C147.573 63 148.499 63.2917 149.127 63.7107C149.763 64.1344 150 64.61 150 65Z">
                        </path>
                        <rect stroke-width="2" stroke="#282828" fill="#FFFCAB" rx="1" height="7" width="5" y="63"
                          x="187">
                        </rect>
                        <rect stroke-width="2" stroke="#282828" fill="#282828" rx="1" height="11" width="4" y="81"
                          x="193"></rect>
                        <rect stroke-width="3" stroke="#282828" fill="#DFDFDF" rx="2.5" height="90" width="121" y="1.5"
                          x="6.5">
                        </rect>
                        <rect stroke-width="2" stroke="#282828" fill="#DFDFDF" rx="2" height="4" width="6" y="84" x="1">
                        </rect>
                      </svg>
                    </div>
                    <div class="truckTires">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                        <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15"></circle>
                        <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                      </svg>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                        <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15"></circle>
                        <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                      </svg>
                    </div>
                    <div class="road"></div>

                    <svg xml:space="preserve" viewBox="0 0 453.459 453.459" xmlns:xlink="http://www.w3.org/1999/xlink"
                      xmlns="http://www.w3.org/2000/svg" id="Capa_1" version="1.1" fill="#000000" class="lampPost">
                      <path d="M252.882,0c-37.781,0-68.686,29.953-70.245,67.358h-6.917v8.954c-26.109,2.163-45.463,10.011-45.463,19.366h9.993
  c-1.65,5.146-2.507,10.54-2.507,16.017c0,28.956,23.558,52.514,52.514,52.514c28.956,0,52.514-23.558,52.514-52.514
  c0-5.478-0.856-10.872-2.506-16.017h9.992c0-9.354-19.352-17.204-45.463-19.366v-8.954h-6.149C200.189,38.779,223.924,16,252.882,16
  c29.952,0,54.32,24.368,54.32,54.32c0,28.774-11.078,37.009-25.105,47.437c-17.444,12.968-37.216,27.667-37.216,78.884v113.914
  h-0.797c-5.068,0-9.174,4.108-9.174,9.177c0,2.844,1.293,5.383,3.321,7.066c-3.432,27.933-26.851,95.744-8.226,115.459v11.202h45.75
  v-11.202c18.625-19.715-4.794-87.527-8.227-115.459c2.029-1.683,3.322-4.223,3.322-7.066c0-5.068-4.107-9.177-9.176-9.177h-0.795
  V196.641c0-43.174,14.942-54.283,30.762-66.043c14.793-10.997,31.559-23.461,31.559-60.277C323.202,31.545,291.656,0,252.882,0z
  M232.77,111.694c0,23.442-19.071,42.514-42.514,42.514c-23.442,0-42.514-19.072-42.514-42.514c0-5.531,1.078-10.957,3.141-16.017
  h78.747C231.693,100.736,232.77,106.162,232.77,111.694z"></path>
                    </svg>
                  </div>
                </div>
              </router-link>
            </div> -->

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
            <button v-if="!isLoggedIn" class="icon-btn text-dark mb-2" data-bs-toggle="modal" @click="openLoginModal">
              <i class="bi bi-people me-2"></i> Đăng nhập
            </button>
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
