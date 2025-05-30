<template>
  <!-- top header-->
  <div class="header position-sticky top-0 bg-white bg-opacity-90 shadow-sm z-3">
    <div class="container">
      <div class="navbar-top">
        <nav class="navbar navbar-expand-lg navbar-bottom">
          <div class="container d-flex justify-content-between align-items-center">
            <!---->
            <div class="d-flex align-items-center">
              <button class="navbar-toggler me-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasMenu">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="logo-container ">
                <a href="/home"><img src="/img/logonew.png" alt="Logo" class="logo" width="80px"></a>

              </div>
            </div>

            <div class="d-flex align-items-center">
              <!-- Search -->
              <!-- Hiển thị kết quả tìm kiếm -->
              <form @submit.prevent="searchProduct">
                <div class="input-wrapper me-3 d-none d-lg-block position-relative " ref="wrapperRef">
                  <button class="icon" type="submit">
                    <svg width="23px" height="23px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                        stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                  </button>
                  <input v-model="searchQuery" type="text" class="input" placeholder="search..." @input="handleInput"
                    @focus="() => { handleInput(); showSuggestions = true; }" @keydown.enter="searchProduct" />

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

              <!-- Login/Logout -->
              <div class="d-none d-lg-block">
                <div class="d-flex align-items-center me-3">
                  <button v-if="!isLoggedIn" class="icon-btn me-2" data-bs-toggle="modal" @click="openLoginModal">
                    <i class="bi bi-people"></i>
                  </button>

                  <template v-else>
                    <button class="icon-btn me-2" @click="handleLogout">
                      <i class="bi bi-person-x"></i>
                    </button>
                    <router-link to="/update-user" class="text-decoration-none text-primary-red">
                      <p v-if="user.username" class="mb-0 me-2">{{ user.username }}</p>
                    </router-link>
                  </template>
                </div>
              </div>
              <div class="d-none d-lg-block">
                <router-link to="/cart" style="color: black;">
                  <button class="icon-btn"><i class="bi bi-cart"></i></button>
                </router-link>
              </div>
            </div>
          </div>
        </nav>
      </div>

      <!-- Menu bottom -->
      <nav class="navbar navbar-expand-lg navbar-bottom">
        <div class="collapse navbar-collapse text-start d-none d-lg-flex">
          <ul class="navbar-nav fs-5">
            <li class="nav-item"><a class="nav-link" href="/home">Trang chủ</a></li>
            <li class="nav-item"><a class="nav-link" href="/food">Thực đơn</a></li>
            <li class="nav-item"><a class="nav-link" href="/reservation">Đặt bàn</a></li>
          </ul>
        </div>
      </nav>
    </div>

    <!-- offcanvas menu small screen -->
    <div class="offcanvas offcanvas-start" id="offcanvasMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="/home">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link" href="/food">Thực đơn</a></li>
          <li class="nav-item"><a class="nav-link" href="/reservation">Đặt bàn</a></li>
        </ul>

        <!-- Các icon hiển thị trên mobile -->
        <div class="d-flex justify-content-around mt-4 d-lg-none">
          <div class="input-wrapper">
            <button class="icon">
              <svg width="23px" height="23px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                  stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                </path>
              </svg>
            </button>
            <input type="text" name="text" class="input" placeholder="search.." />
          </div>
          <button class="icon-btn ms-3" data-bs-toggle="modal" @click="openLoginModal">
            <i class="bi bi-people"></i>
          </button>

          <button class="icon-btn ms-3">
            <i class="bi bi-telephone"></i>
          </button>

          <router-link to="/cart" style="color: black;">
            <button class="icon-btn ms-3"><i class="bi bi-cart"></i></button>
          </router-link>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal đăng nhập -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title w-100 text-center fw-bold" id="loginModalLabel">Đăng nhập</h5>
        </div>
        <div class="modal-body px-4 py-3">
          <form @submit.prevent="handleLogin">
            <div v-if="loginError" class="text-danger small text-center">{{ loginError }}</div>
            <div></div>

            <!-- <div class="mb-3 position-relative input-group">
              <span class="input-icon">
                <i class="bi bi-person"></i>
              </span>
              <input type="text" class="form-control" v-model="loginData.login" placeholder="Tên đăng nhập hoặc email">
            </div> -->

            <div class="mb-3 position-relative">

              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" placeholder="Tên đăng nhập hoặc email"
                v-model="loginData.login">

            </div>

            <!-- <div class="mb-3 position-relative input-group">
              <span class="input-icon">
                <i class="bi bi-lock"></i>
              </span>
              <input type="password" class="form-control" v-model="loginData.password" id="password"
                placeholder="Nhập mật khẩu">
            </div> -->

            <div class="mb-3 position-relative">

              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" placeholder="Mật khẩu" v-model="loginData.password">

            </div>


            <div class="mb-3 d-flex justify-content-end gap-3 small">
              <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
                class="text-decoration-none">Quên mật khẩu</a>

              <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal"
                class="text-decoration-none">Đăng ký</a>
            </div>
            <div class="mb-3">

              <button type="submit" class="btn btn-login w-100" :disabled="loading"> <span v-if="loading"
                  class="spinner-border spinner-border-sm me-2"></span>Đăng nhập</button>
            </div>

            <div class="divider d-flex align-items-center mb-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">hoặc đăng nhập</span>
              <hr class="flex-grow-1">
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button @click="loginWithGoogle" type="button" class="btn btn-social"><i
                  class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal đăng ký  -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="registerModalLabel">Đăng ký</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="Handleregister">
            <!-- Username -->
            <div v-if="registerErrors.username" class="text-danger small text-center" style="min-height: 16px;">{{
              registerErrors.username[0] }}</div>
            <div v-else style="height:3px"></div>
            <div class="mb-3 position-relative ">

              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5 register-input" placeholder="Tên đăng nhập"
                v-model="registerData.username">

            </div>

            <!-- Email -->
            <div v-if="registerErrors.email" class="text-danger small text-center error-message">{{
              registerErrors.email[0]
            }}
            </div>
            <div v-else style="height:3px"></div>
            <div class="mb-3 position-relative">

              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5 register-input" placeholder="Email"
                v-model="registerData.email">

            </div>

            <!-- Phone  -->
            <!-- <div v-if="registerErrors.phone" class="text-danger small text-center error-message">{{
              registerErrors.phone[0]
            }}
            </div>
            <div v-else style="height:3px"></div>
            <div class="mb-3 position-relative">
              <i class="bi bi-telephone position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5 register-input" id="phone" placeholder="Số điện thoại"
                v-model="registerData.phone">
            </div> -->

            <!-- Password -->
            <div v-if="registerErrors.password" class="text-danger small text-center error-message">{{
              registerErrors.password[0] }}</div>
            <div v-else style="height:10px"></div>
            <div class="mb-3 position-relative">

              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5 register-input" placeholder="Mật khẩu"
                v-model="registerData.password">

            </div>

            <!-- Confirm Password -->
            <div style="height:3px"></div>
            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5 register-input" placeholder="Nhập lại mật khẩu"
                v-model="registerData.password_confirmation">
            </div>

            <!-- Chuyển sang đăng nhập -->
            <div class="mb-3 text-end">
              <a href="#" data-bs-toggle="modal" @click="openLoginModal">Đã có tài
                khoản</a>
            </div>

            <!-- Đăng ký -->
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Đăng ký
              </button>
            </div>



            <div class="divider d-flex align-items-center mb-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">hoặc đăng nhập</span>
              <hr class="flex-grow-1">
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button type="button" class="btn btn-social" @click="loginWithGoogle"><i
                  class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>




  <!-- Quên mật khẩu  -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="forgotPasswordModalLabel">Quên mật khẩu</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="forgotPass">

            <!-- nhập email  -->
            <div v-if="errorSendCode" class="text-danger small text-center">{{ errorSendCode }}</div>
            <div class="mb-3 position-relative">
              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="email" placeholder="Nhập Email" v-model=verify.email>
            </div>

            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Gửi
              </button>
              <!-- data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#authenticationModal" -->
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- nhập code khôi phục -->
  <div class="modal fade" id="authenticationModal" tabindex="-1" aria-labelledby="authenticationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal p-4">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title" id="authenticationModalLabel">Nhập mã xác nhận</h5>
        </div>
        <div class="modal-body pt-2">
          <form @submit.prevent="verifyResetCode">
            <div class="mb-3 d-flex justify-content-center gap-3 small">
              <p v-if="!errorVerify" class="text-success text-center m-0">
              </p>

              <p v-if="errorVerify" class="text-danger text-center m-0">
                {{ errorVerify }}
              </p>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3">
              <input v-for="(digit, index) in codeDigits" :key="index" v-model="codeDigits[index]" type="text"
                maxlength="1" class="form-control text-center border border-dark fw-bold fs-4"
                style="width: 50px; height: 50px;" @input="moveToNext(index, $event)"
                @keydown.backspace="handleBackspace($event, index)" @compositionstart="isComposing = true"
                @compositionend="isComposing = false" ref="inputs" autocomplete="one-time-code" inputmode="numeric"
                pattern="[0-9]*" />
            </div>

            <div class="d-flex justify-content-center gap-3 small mb-3">
              <p class="text-primary text-decoration-underline" style="cursor: pointer;" @click="sendCode"
                v-if="!loadingSend && wait === 0">
                Gửi lại mã
              </p>

              <p class="text-muted" v-else-if="wait > 0">
                Gửi lại mã ({{ wait }}s)
              </p>

              <p class="text-muted" v-else>
                Đang gửi...
              </p>
            </div>

            <div class="mb-3 d-flex justify-content-center align-items-center gap-2 small">
              <i class="bi bi-clock text-danger"></i>
              <p class="mb-0 fw-bold text-danger">
                {{ minutes.toString().padStart(2, '0') }}:{{ seconds.toString().padStart(2, '0') }}
              </p>
            </div>

            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold"
                :disabled="loading || codeDigits.some(d => d === '')">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Xác nhận
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>




  <!-- đặt lại mật khẩu  -->
  <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="resetModalLabel">Đặt lại mật khẩu</h5>
        </div>
        <div class="modal-body">

          <form @submit.prevent="ResetPass">
            <div v-if="errorResetPass" class="text-danger text-center small">{{ errorResetPass }}</div>
            <div class="mb-3 position-relative">
              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password" placeholder="Mật khẩu"
                v-model="verify.password">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password_confirm" placeholder="Nhập lại mật khẩu"
                v-model="verify.password_confirmation">
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Xác nhận
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- food modal -->
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
import { useCountdown } from "../stores/countDown";
import { useAuthStore } from '@/stores/auth';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { reactive, ref, onMounted, onBeforeUnmount, watch } from 'vue';
import * as bootstrap from 'bootstrap';

// const { formattedTime, isCounting, startCountdown } = useCountdown(60);
const auth = useAuthStore();
//Google
const loginWithGoogle = () => {
  window.location.href = 'http://localhost:8000/api/auth/google/redirect';
};

//search
// const searchQuery = ref('');
const router = useRouter();
// const router = useRouter();// const res = ref([]);
// const searchProduct = () => {
//   const query = searchQuery.value.trim()
//   if (!query) return

//   router.push({
//     path: '/search',
//     query: { search: query }
//   })
// }

// const handleGoogleLogin = async () => {
//   try {
//     const token = route.query.token;

//     if (token) {
//         localStorage.setItem('token', token);
//         router.push('/home'); // chuyển về trang chính
//       } else {
//         // xử lý lỗi nếu không có token
//         console.error("Token not found");
//       }
//   } catch (error) {
//     console.error("Google login error:", error);
//   }
// };


window.bootstrap = bootstrap;

// biến để hiển thị countdown
const minutes = ref(0);
const seconds = ref(0);
let countdownInterval = null;

const startCountdown = (expireTime) => {
  clearInterval(countdownInterval); // Xóa interval cũ nếu có

  const target = expireTime; // Thời gian hết hạn (5 phút từ lúc gửi request)
  const now = new Date().getTime(); // Thời gian hiện tại lúc nhận được dữ liệu
  let remainingTime = target - now; // Tính toán thời gian còn lại

  // Nếu thời gian còn lại đã hết, không cần bắt đầu countdown
  if (remainingTime <= 0) {
    minutes.value = 0;
    seconds.value = 0;
    return;
  }

  // Cập nhật thời gian ban đầu (phút và giây)
  minutes.value = Math.floor(remainingTime / 60000); // Phút
  seconds.value = Math.floor((remainingTime % 60000) / 1000); // Giây

  countdownInterval = setInterval(() => {
    remainingTime -= 1000; // Mỗi giây trôi qua, giảm đi 1 giây

    if (remainingTime <= 0) {
      clearInterval(countdownInterval); // Dừng countdown khi hết thời gian
      minutes.value = 0;
      seconds.value = 0;
      return;
    }

    // Cập nhật lại phút và giây sau mỗi giây
    minutes.value = Math.floor(remainingTime / 60000);
    seconds.value = Math.floor((remainingTime % 60000) / 1000);
  }, 1000);
};



// function stopCountdown() {
//   clearInterval(timer);
// }

// tạo thông tin register
const registerData = reactive({
  username: '',
  email: '',
  password: '',
  password_confirmation: ''
});

// báo lỗi
const registerErrors = reactive({});
const firstErrorKey = ref('');
const verifyCode = reactive({
  email: '',
  type: '' // 'register' | 'forgot'
});

const Handleregister = async () => {
  Object.keys(registerErrors).forEach(key => delete registerErrors[key]);
  loading.value = true;

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register/send-code', registerData);

    if (response.status === 200) {
      alert(response.data.message);

      // user.value = response.data.user;
      // localStorage.setItem('user', JSON.stringify(response.data.user));
      // localStorage.setItem('token', response.data.token);
      // isLoggedIn.value = true;

      // Ẩn modal đăng ký
      const modalElement = document.getElementById('registerModal');
      const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
      modalInstance.hide();

      // Hiện modal xác minh mã
      verifyCode.type = 'register';
      const codeModal = new bootstrap.Modal(document.getElementById('authenticationModal'));
      codeModal.show();

      // Lưu email để dùng khi xác minh mã
      verify.email = registerData.email;


      // Reset code input
      codeDigits.value = ['', '', '', '', '', ''];
      errorVerify.value = '';

      // Bắt đầu đếm ngược mã hết hạn
      wait.value = 60;
      const timer = setInterval(() => {
        if (wait.value > 0) wait.value--;
        else clearInterval(timer);
      }, 1000);
      const expireTime = new Date().getTime() + 5 * 60 * 1000;
      startCountdown(expireTime);
    }
  } catch (error) {
    if (error.response?.status === 422) {
      const allErrors = error.response.data.errors;
      const firstKey = Object.keys(allErrors)[0];
      Object.keys(registerErrors).forEach(k => delete registerErrors[k]);
      registerErrors[firstKey] = allErrors[firstKey];
      firstErrorKey.value = firstKey;
    } else {
      alert('Có lỗi xảy ra, vui lòng thử lại sau.');
    }
  } finally {
    loading.value = false;
  }
};


// tạo thông tin login

const loginData = reactive({
  login: '',
  password: ''
});


// tạo hiệu ứng load
const loading = ref(false);
const loadingSend = ref(false);

// kiểm tra đã đăng nhập chưa
const user = ref(JSON.parse(localStorage.getItem('user')) || null);
const isLoggedIn = ref(!!user.value);
onMounted(() => {
  const storedUser = JSON.parse(localStorage.getItem('user'));
  user.value = storedUser;
  isLoggedIn.value = !!storedUser

});


// tạo thông tin quên mật khẩu
const codeDigits = ref(['', '', '', '', '', ''])
const isComposing = ref(false);
const inputs = ref([]);

const verify = reactive({
  email: '',
  password: '',
  password_confirmation: ''
})


// báo lỗi nhập mã
const errorSendCode = ref('');
const errorVerify = ref('');
const wait = ref(0);

// tạo biến báo lỗi login
const loginError = ref('');
const loginErrors = reactive({});

// lỗi đặt lại mật khẩu
const errorResetPass = ref('');





//  Đăng ký
// const Handleregister = async () => {
//   Object.keys(registerErrors).forEach(key => delete registerErrors[key]);
//   loading.value = true;

//   try {
//     const response = await axios.post('http://127.0.0.1:8000/api/register', registerData);

//     if (response.status === 200) {
//       alert(response.data.message);

//       // Lưu thông tin người dùng và token nếu backend trả về
//       user.value = response.data.user;
//       localStorage.setItem('user', JSON.stringify(response.data.user));
//       localStorage.setItem('token', response.data.token);
//       isLoggedIn.value = true;

//       // Ẩn modal
//       const modalElement = document.getElementById('registerModal');
//       const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
//       modalInstance.hide();

//       // Xử lý backdrop thủ công nếu cần
//       document.body.classList.remove('modal-open');
//       document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

//       // Reset form
//       Object.keys(registerData).forEach(key => registerData[key] = '');
//     }
//   } catch (error) {
//     if (error.response?.status === 422) {
//       if (error.response?.status === 422) {
//         const allErrors = error.response.data.errors;
//         const firstKey = Object.keys(allErrors)[0];

//         // Xóa hết lỗi cũ
//         Object.keys(registerErrors).forEach(k => delete registerErrors[k]);

//         // Chỉ giữ lỗi đầu tiên
//         registerErrors[firstKey] = allErrors[firstKey];
//         firstErrorKey.value = firstKey;
//       }
//     } else {
//       console.error('Lỗi khi đăng ký:', error);
//       alert('Có lỗi xảy ra, vui lòng thử lại sau.');
//     }
//   } finally {
//     loading.value = false;
//   }
// };

//  Đăng nhập
const handleLogin = async () => {
  loginError.value = '';
  loading.value = true;

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', loginData);

    alert('Đăng nhập thành công!');
    user.value = response.data.user;
    localStorage.setItem('user', JSON.stringify(response.data.user));
    localStorage.setItem('token', response.data.token);
    isLoggedIn.value = true;

    // Ẩn modal
    const modalElement = document.getElementById('loginModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.hide();

    // Xử lý backdrop thủ công nếu cần
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());



    // Reset form
    loginData.login = '';
    loginData.password = '';
    if (user.value.role === 'admin') {
      router.push('/admin');
    } else {
      router.push('/home');
    }
  } catch (error) {
    console.error('Lỗi đăng nhập:', error);

    if (error.response?.status === 422) {
      // Gộp tất cả lỗi từ backend thành 1 chuỗi
      const errors = error.response.data.errors;
      const firstKey = Object.keys(errors)[0];
      loginError.value = errors[firstKey][0];
    } else if (error.response?.status === 401) {
      loginError.value = 'Sai email hoặc mật khẩu!';
    } else if (error.response?.status === 500) {
      loginError.value = 'Lỗi máy chủ. Vui lòng thử lại sau.';
    } else if (error.request) {
      loginError.value = 'Không thể kết nối đến máy chủ. Kiểm tra internet.';
    } else {
      loginError.value = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
    }
  } finally {
    loading.value = false;
  }
};




//  Đăng xuất
const handleLogout = async () => {


  const confirmLogout = confirm('Bạn chắc chắn muốn đăng xuất?');
  if (!confirmLogout) {
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

    alert('Đăng xuất thành công');
    window.location.href = '/home';
  } catch (error) {
    console.error('Lỗi đăng xuất:', error);
    alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
  }
};


// hàm mở pop up Login
const openLoginModal = () => {
  // Xóa backdrop và class nếu bị sót từ lần trước
  document.body.classList.remove('modal-open');
  document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop').forEach(el => el.remove());

  // Lấy modal element và hiển thị lại
  const modalElement = document.getElementById('loginModal');
  const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
  modalInstance.show();
};

// Di chuyển focus giữa các ô input
const moveToNext = (index, event) => {
  if (isComposing.value) return;

  const input = event.target;
  const value = input.value;

  if (value.length === 1 && index < 5) {
    const nextInput = input.nextElementSibling;
    if (nextInput) nextInput.focus();
  } else if (value === '' && index > 0) {
    const prevInput = input.previousElementSibling;
    if (prevInput) prevInput.focus();
  }
};

// Chỉ nhập số
const onlyNumber = (event) => {
  const key = event.key;
  if (!/^\d$/.test(key)) {
    event.preventDefault();
  }
};

const forgotPass = async () => {
  loading.value = true;
  errorSendCode.value = '';
  try {
    // Tính toán thời gian hết hạn từ lúc gửi request (5 phút từ thời điểm gửi)
    const expireTime = new Date().getTime() + 5 * 60 * 1000; // 5 phút (tính bằng ms)

    // Gửi request để yêu cầu mã xác nhận
    const response = await axios.post('http://127.0.0.1:8000/api/forgot', {
      email: verify.email
    });

    if (response.status === 200) {
      // alert(response.data.message);
      const modelElement = document.getElementById('forgotPasswordModal');
      const modalInstance = bootstrap.Modal.getInstance(modelElement) || new bootstrap.Modal(modelElement);
      modalInstance.hide();

      verifyCode.type = 'forgot';

      const modelCode = document.getElementById('authenticationModal');
      const modalInstanceCode = bootstrap.Modal.getInstance(modelCode) || new bootstrap.Modal(modelCode);
      modalInstanceCode.show();
    }



    // Gọi hàm startCountdown với expireTime tính từ lúc gửi request
    startCountdown(expireTime);

  } catch (error) {
    if (error.response) {
      const status = error.response.status;
      if (status === 404 || status === 410) {
        errorSendCode.value = error.response.data.errors?.email?.[0] || error.response.data.message;
      } else if (status === 422) {
        errorSendCode.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorSendCode.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorSendCode.value = 'Lỗi kết nối đến server';
    }
  } finally {
    loading.value = false;
  }
};






// Xác minh mã code
const verifyResetCode = async (Code) => {
  loading.value = true;
  errorVerify.value = '';
  const code = codeDigits.value.join('');
  try {
    let response;
    if (verifyCode.type === 'register') {
      response = await axios.post('http://127.0.0.1:8000/api/register/verify-code', {
        email: verify.email,
        code,
      });
      if (response.status === 200) {
      alert(response.data.message);

      // Ẩn modal code
      const modalCode = document.getElementById('authenticationModal');
      const modalCodeInstance = bootstrap.Modal.getInstance(modalCode) || new bootstrap.Modal(modalCode);
      modalCodeInstance.hide();

      // Lưu thông tin user và token
      user.value = response.data.user;
      localStorage.setItem('user', JSON.stringify(response.data.user));
      localStorage.setItem('token', response.data.token);

      // Cập nhật trạng thái đăng nhập (tùy theo app)
      isLoggedIn.value = true;

      // Reset form code
      codeDigits.value = ['', '', '', '', '', ''];
      errorVerify.value = '';
    }
    } else {
      response = await axios.post('http://127.0.0.1:8000/api/code', {
        email: verify.email,
        code,
      });
      if (response.status == 200) {
        alert(response.data.message);
        const modalCode = document.getElementById('authenticationModal');
        const modalCodeInstance = bootstrap.Modal.getInstance(modalCode) || new bootstrap.Modal(modalCode);
        modalCodeInstance.hide();

        const modalResetPass = document.getElementById('resetModal');
        const modalResetPassInstance = bootstrap.Modal.getInstance(modalResetPass) || new bootstrap.Modal(modalResetPass);
        modalResetPassInstance.show();
        errorVerify.value = '';
      }
    }


  } catch (error) {
    if (error.response) {
      const status = error.response.status;
      if (status === 404 || status === 410) {
        errorVerify.value = error.response.data.message;
      } else if (status === 422) {
        errorVerify.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorVerify.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorVerify.value = 'Lỗi kết nối đến máy chủ';
    }
  } finally {
    loading.value = false;
  }
};



// const sendCode = async () => {
//   if (loadingSend.value || wait.value > 0) return;
//   loadingSend.value = true;
//   try {
//     let response;

//     if (verifyCode.type === 'register') {
//       response = await axios.post('http://127.0.0.1:8000/api/register/verify-code', registerData);
//     } else {
//       response = await axios.post('http://127.0.0.1:8000/api/forgot', {
//         email: verify.email
//       });
//     }

//     if (response.status === 200) {
//       wait.value = 60;
//       const timer = setInterval(() => {
//         if (wait.value > 0) wait.value--;
//         else clearInterval(timer);
//       }, 1000);

//       const expireTime = new Date().getTime() + 5 * 60 * 1000;
//       startCountdown(expireTime);
//     }
//   } catch (error) {
//     errorVerify.value = 'Không thể gửi lại mã. Vui lòng thử lại.';
//   } finally {
//     loadingSend.value = false;
//   }
// };


const sendCode = async (Data, Code) => {
  if (loadingSend.value || wait.value > 0) return;
  loadingSend.value = true;
  errorVerify.value = '';
  try {
    let response;
    if (verifyCode.type === 'register') {
      response = await axios.post('http://127.0.0.1:8000/api/register/send-code', registerData);
    } else {
      // logic cho forgot password nếu có
      response = await axios.post('http://127.0.0.1:8000/api/forgot', { email: verify.email });
    }

    if (response.status === 200) {
      wait.value = 60;
      const timer = setInterval(() => {
        if (wait.value > 0) wait.value--;
        else clearInterval(timer);
      }, 1000);

      // Thời gian expire 5 phút
      const expireTime = new Date().getTime() + 5 * 60 * 1000;
      startCountdown(expireTime);
    }
  } catch (error) {
    errorVerify.value = 'Không thể gửi lại mã. Vui lòng thử lại.';
  } finally {
    loadingSend.value = false;
  }
};

const ResetPass = async () => {
  loading.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/reset-password', {
      "email": verify.email,
      "password": verify.password,
      "password_confirmation": verify.password_confirmation
    });

    if (response.status == 200) {
      alert(response.data.message);
      const modalResetPass = document.getElementById('resetModal');
      const modalResetPassInstance = bootstrap.Modal.getInstance(modalResetPass) || new bootstrap.Modal(modalResetPass);
      modalResetPassInstance.hide();
    }
  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 404 || status === 410) {
        errorResetPass.value = error.response.data.errors?.email?.[0] || error.response.data.message;
      } else if (status === 422) {
        errorResetPass.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorResetPass.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorResetPass.value = 'Lỗi kết nối đến server';
    }
  } finally {
    loading.value = false;
  }

  return {

  }
};

// }const searchQuery = ref('')
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

const formatNumber = (num) => new Intl.NumberFormat().format(num);
const getImageUrl = (img) => `http://127.0.0.1:8000/storage/img/food/${img}`;
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
}, 300);
// 300ms debounce

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


// Hàm xử lý khi người dùng chọn một item trong danh sách gợi ý
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

.loading,
.no-more {
  padding: 10px;
  text-align: center;
  color: #888;
}
</style>
