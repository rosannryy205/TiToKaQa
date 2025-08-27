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

          <div class="collapse navbar-collapse">
            <ul class="navbar-nav main-nav-links">
              <li class="nav-item"><router-link class="nav-link" to="/home">Trang chủ</router-link></li>
              <li class="nav-item"><router-link class="nav-link" to="/food">Thực đơn</router-link></li>
              <li class="nav-item"><router-link class="nav-link" to="/reservation">Đặt bàn</router-link></li>
              <li class="nav-item"><router-link class="nav-link" to="/luckywheel">Vòng quay may mắn</router-link></li>
              <li class="nav-item"><router-link class="nav-link" to="/posts">Bài viết</router-link></li>
            </ul>
          </div>
          <div class="me-2">
            <button class="button" @click.prevent="toggleBookingForm" :class="{ 'active': isBookingFormVisible }">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none"
                class="svg-icon">
                <g stroke-width="2" stroke-linecap="round" stroke="#fff">
                  <rect y="5" x="4" width="16" rx="2" height="16"></rect>
                  <path d="m8 3v4"></path>
                  <path d="m16 3v4"></path>
                  <path d="m4 11h16"></path>
                </g>
              </svg>
              <span class="lable">Đặt bàn ngay !</span>
            </button>
          </div>
          <div class="d-none d-lg-flex align-items-center ms-auto ">
            <form @submit.prevent="searchProduct">
              <div class="input-wrapper position-relative me-3" ref="wrapperRef">
                <button class="icon-search-submit" type="submit">
                  <svg width="22px" height="22px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <img style="width: 50px;" :src="getImageUrl(item.image)" :alt="item.name"
                      class="me-2  img-search" />
                    <div class="info-search">
                      <div class="name-search">{{ item.name }}</div>
                      <div class="price-search">{{ formatNumber(item.price) }}</div>
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


    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav offcanvas-nav-links mb-4">
          <li class="nav-item"><router-link class="nav-link" to="/home" @click.prevent="closeMenu()">Trang chủ</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/food" @click.prevent="closeMenu()">Thực đơn</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/reservation" @click.prevent="closeMenu()">Đặt bàn</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/luckywheel" @click.prevent="closeMenu()">Vòng quay may mắn</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/posts" @click.prevent="closeMenu()">Bài viết</router-link></li>
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
            <router-link v-if="!isLoggedIn" to="/login" @click.prevent="closeMenu()" class="text-decoration-none text-primary-black">
              <button class="icon-btn text-dark mb-2">
                <i class="bi bi-people me-2"></i> Đăng nhập
              </button>
            </router-link>
            <template v-else>
              <div class="mb-2">
                <router-link to="/account/update-user" @click.prevent="closeMenu()" class="text-decoration-none text-primary-red me-2">
                  <p v-if="user.username" class="mb-0 username-display"><i class="bi bi-person me-2"></i>{{
                    user.username }}</p>
                </router-link>
              </div>
              <button class="icon-btn text-dark mb-2" @click="handleLogout" @click.prevent="closeMenu()">
                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
              </button>
            </template>

            <!-- <router-link to="/delivery" class="icon-btn text-dark mb-2">
              <i class="bi bi-truck me-2"></i> Theo dõi đơn hàng
            </router-link> -->
            <router-link to="/cart" @click.prevent="closeMenu()" class="icon-btn text-dark mb-2">
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content custom-modal modal-ct">
        <div class="modal-body position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
            aria-label="Close"></button>
          <div class="row">
            <div class="col-md-6 border-end">
              <h5 class="fw-bold text-danger text-center mb-3">{{ foodDetail.name }}</h5>
              <h5 v-if="false">{{ foodDetail.category_id }}</h5>
              <div class="text-center mb-3">
                <img :src="getImageUrl(foodDetail.image)" class="modal-image img-fluid" />
              </div>
              <p class="text-danger fw-bold fs-5 text-center">
                {{ formatNumber(foodDetail.price) }} VNĐ
              </p>
              <p class="text-dark text-center text-lg fw-bold mb-3">{{ foodDetail.description }}</p>
            </div>
            <div class="col-md-6 d-flex flex-column">
              <form @submit.prevent="addToCart" class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                  <div class="topping-container mb-3" v-if="toppingList.length
                    || spicyLevel.length">
                    <div class="mb-3" v-if="spicyLevel.length">
                      <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                      <select class="form-select" id="spicyLevel">
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
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

                <!---->
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
                  <button class="btn btn-danger w-100 fw-bold">🛒 Thêm vào giỏ hàng</button>
                </div>
              </form>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- reservation -->
  <div class="container">
    <transition name="slide-fade">
      <div v-if="isBookingFormVisible" ref="bookingForm" class="booking-form-container position-relative mb-2">
        <button @click="closeForm" class="btn-close position-absolute" style="top: 5px; right: 5px;"
          aria-label="Close"></button>

        <form class="row g-3 align-items-end mt-3" @submit.prevent="makeReservationQuickly">
          <div class="col-12 col-md-4 col-lg">
            <label for="bookingGuests" class="form-label">Số khách</label>
            <input type="number" class="form-control" id="bookingGuests" min="1" value="1" v-model="guest_count">
          </div>
          <div class="col-12 col-md-4 col-lg">
            <label for="bookingDate" class="form-label">Ngày đặt</label>
            <input type="date" class="form-control" v-model="date" :min="today" />
          </div>
          <div class="col-12 col-md-4 col-lg">
            <!-- <label for="bookingTime" class="form-label">Giờ đặt</label>
            <input type="time" class="form-control" id="bookingTime"> -->
            <select class="form-control" v-model="time">
              <option value="">Chọn giờ</option>
              <option v-for="time in filteredTimeOptions" :key="time" :value="time">
                {{ time }}
              </option>
            </select>
          </div>
          <div class="col-12 col-lg-auto">
            <button class="button" type="submit">
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
              <span class="lable">Đặt ngay !</span>
            </button>
          </div>
        </form>
      </div>
    </transition>
  </div>
  <router-view></router-view>

</template>
<script setup>
import { Offcanvas } from 'bootstrap'
import { useAuthStore } from '@/stores/auth';
import { useUserStore } from '@/stores/userAuth';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { ref, onMounted, onBeforeUnmount, computed, watch, nextTick } from 'vue';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';
import { API_URL } from '@/config'
import { CSRF_URL } from '@/config'
import { STORAGE_URL } from '@/config'
// const { formattedTime, isCounting, startCountdown } = useCountdown(60);
const auth = useAuthStore();
//Google
const loginWithGoogle = () => {
  window.location.href = `${API_URL}/auth/google/redirect`;
  
};

const router = useRouter();
// window.bootstrap = bootstrap;
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
    await axios.get(`${CSRF_URL}/sanctum/csrf-cookie`, { withCredentials: true })
    await axios.post(`${API_URL}/logout`, {}, {
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
const getImageUrl = (img) => `${STORAGE_URL}/img/food/${img}`;

// Hàm lấy dữ liệu từ API
const fetchSuggestions = async () => {
  if (loading.value || !searchQuery.value.trim() || !hasMore.value) return;

  loading.value = true;
  try {
    const res = await axios.get(`${API_URL}/search`, {
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
  foodDetail.value = {}
  toppings.value = []
  spicyLevel.value = []
  toppingList.value = []
  quantity.value = 1
  try {
    if (item.type === 'food') {
      const res = await axios.get(`${API_URL}/home/food/${item.id}`)
      foodDetail.value = { ...res.data, type: 'Food' }

      const res1 = await axios.get(`${API_URL}/home/topping/${item.id}`)
      toppings.value = res1.data

      spicyLevel.value = toppings.value.filter((item) => item.category_id == 15)
      toppingList.value = toppings.value.filter((item) => item.category_id == 16)
      toppingList.value.forEach((item) => {
        item.price = item.price || 0
      })
    } else if (item.type === 'combo') {
      const res = await axios.get(`${API_URL}/home/combo/${item.id}`)
      foodDetail.value = { ...res.data, type: 'Combo' }
    }

    const modalElement = document.getElementById('searchModal')
    if (modalElement) {
      const modal = new Modal(modalElement)
      modal.show()
    }
  } catch (error) {
    console.error(error)
  }
}


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
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Đã thêm vào giỏ hàng!',
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true
  });
}
/**form datban */
const route = useRoute()
const bookingForm = ref(null)
const isBookingFormVisible = ref(false)

const closeForm = () => {
  isBookingFormVisible.value = false
}

const toggleBookingForm = () => {
  isBookingFormVisible.value = !isBookingFormVisible.value
}
watch(isBookingFormVisible, (visible) => {
  // Chỉ thực hiện hành động khi form được MỞ RA
  if (visible) {
    // Chỉ cuộn nếu người dùng KHÔNG ở đầu trang
    if (window.scrollY > 0) {
      // Sử dụng window.scrollTo để cuộn lên đầu trang một cách mượt mà
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      })
    }
    // nextTick vẫn hữu ích để đảm bảo form có trong DOM trước khi có thể tương tác
    nextTick(() => {
      // Bạn có thể thực hiện các hành động khác ở đây sau khi form đã render
      // Ví dụ: bookingForm.value.querySelector('input').focus()
    })
  }
})
watch(route, () => {
  isBookingFormVisible.value = false
})


const today = new Date().toISOString().split('T')[0]
const date = ref()
const timeOptions = ref([])
const time = ref('')
const guest_count = ref(1)
const isLoading = ref(false)
const filteredTimeOptions = computed(() => {
  if (!date.value) {
    return timeOptions.value
  }

  const selectedDate = new Date(date.value)
  const now = new Date()

  if (selectedDate.toDateString() === now.toDateString()) {
    return timeOptions.value.filter((timeStr) => {
      const [hours, minutes] = timeStr.split(':').map(Number)
      const timeDate = new Date(selectedDate)
      timeDate.setHours(hours, minutes, 0)

      return timeDate > now
    })
  }

  return timeOptions.value
})

const formatDateTime = (dateStr) => {
  if (!dateStr) return '';

  const d = new Date(dateStr);

  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0'); // tháng bắt đầu từ 0
  const day = String(d.getDate()).padStart(2, '0');
  const hours = String(d.getHours()).padStart(2, '0'); // 24h format
  const minutes = String(d.getMinutes()).padStart(2, '0');

  return `${year}-${month}-${day} ${hours}:${minutes}`;
};

const makeReservationQuickly = async () => {
  if (!date.value || !time.value || !guest_count.value) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'info',
      title: 'Vui lòng điền đầy đủ thông tin để tìm bàn!',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });
    return
  }
  const selectedDateTime = new Date(`${date.value}T${time.value}:00`)
  try {
    isLoading.value = true

    const reservedFrom = selectedDateTime
    const reservedTo = new Date(reservedFrom.getTime() + 2 * 60 * 60 * 1000)

    const reserved_from = formatDateTime(reservedFrom)
    const reserved_to = formatDateTime(reservedTo)

    const res = await axios.post(`${API_URL}/make-reservation-quickly`, {
      reserved_from,
      reserved_to,
      number_of_guests: guest_count.value,
    })

    isLoading.value = false
    toggleBookingForm()
    router.push({
      name: 'reservation-form',
      params: { orderId: res.data.orderId },
    })
  } catch (error) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Lỗi khi lấy danh sách bàn có thể đặt',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });
    console.error('Lỗi:', error)
  } finally {
    isLoading.value = false
  }
}

/**end form datban */
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  for (let hour = 8; hour <= 21; hour++) {
    let hourStr = hour < 10 ? '0' + hour : '' + hour
    timeOptions.value.push(hourStr + ':00')
    if (hour !== 20) {
      timeOptions.value.push(hourStr + ':30')
    }
  }
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
onMounted(() => {
  const el = document.getElementById('offcanvasMenu')
  if (el) offcanvasInst = Offcanvas.getOrCreateInstance(el)
})

let offcanvasInst = null

function closeMenu() {
  if (offcanvasInst) offcanvasInst.hide()
  document.body.classList.remove('modal-open')
  document.body.style.overflow = ''
  document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove())
}
</script>

<style scoped>
.booking-form-container {
  background-color: #ffffff;
  border-radius: 0 0 .5rem .5rem;
  border-top: none;
}

.booking-form-container .form-label {
  font-size: 0.85rem;
  margin-bottom: 0.25rem;
  color: #495057;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-15px);
  opacity: 0;
}

.button {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 7px;
  height: 38px;
  width: 187px;
  border: none;
  background: #FF342B;
  border-radius: 20px;
  cursor: pointer;
  gap: 3px;
}

@media (max-width: 991.98px) {
  .button {
    height: 30px;
    width: 180px;
  }

  #app>div.client>div.header.position-sticky.top-0.bg-white.bg-opacity-90.shadow-sm.z-3>div.container>nav>div>div.me-2>button>svg {
    width: 21px;
    height: 21px;
  }
  #app > div.client > div.header.position-sticky.top-0.bg-white.bg-opacity-90.shadow-sm.z-3 > div.container > nav > div > div.me-2 > button > span{
    font-size: 15px;
  }
  #app > div.client > div.header.position-sticky.top-0.bg-white.bg-opacity-90.shadow-sm.z-3 > div.container > nav > div > div.me-2 > button{
    gap:5px;
  }
}
.lable {
  line-height: 22px;
  font-size: 17px;
  color: #fff;
  font-family: sans-serif;
  letter-spacing: 1px;
}

.button:hover {
  background: #e52e26;
}

.button:hover .svg-icon {
  animation: slope 1s linear infinite;
}

@keyframes slope {
  0% {}

  50% {
    transform: rotate(10deg);
  }

  100% {}
}
</style>
