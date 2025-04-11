<template>
  <!-- top header-->
  <div class="header">
    <div class="container">
      <div class="navbar-top">
        <nav class="navbar navbar-expand-lg navbar-bottom">
          <div class="container">
            <!-- menu small screen  -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
        </nav>
        <div class="nav-icons d-none d-lg-flex align-items-center">
          <button v-if="!isLoggedIn" class="icon-btn me-3" data-bs-toggle="modal" @click="openLoginModal">
            <i class="bi bi-people"></i>
          </button>

          <template v-else>
            <button class="icon-btn me-2" @click="handleLogout">
              <i class="bi bi-person-x"></i>
            </button>
            <router-link to="/update-user" class="text-decoration-none text-primary-red">
              <p class="mb-0 me-3">{{ user.username }}</p>
            </router-link>


          </template>

          <!--sea-->
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

        </div>

        <div class="logo-container">
          <img src="/img/logonew.png" alt="Logo" class="logo">
        </div>

        <div class="nav-icons d-none d-lg-block">
          <button class="icon-btn me-3"><i class="bi bi-telephone"></i></button>
          <router-link to="/cart" style="color: black;">
            <button class="icon-btn"><i class="bi bi-cart"></i></button>
          </router-link>

        </div>
      </div>

      <!-- menu bottom -->
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
          <li class="nav-item"><a class="nav-link" href="#">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link" href="/food">Thực đơn</a></li>
          <li class="nav-item"><a class="nav-link" href="/reservation">Đặt bàn</a></li>
          <div class="icon">
            <button class="icon-btn me-3" data-bs-toggle="modal" data-bs-target="#loginModal">
              <i class="bi bi-people"></i>
            </button>
            <button class="icon-btn"><i class="bi bi-search"></i></button>
            <button class="icon-btn me-3"><i class="bi bi-telephone"></i></button>
            <router-link to="/cart" style="color: black;">
              <button class="icon-btn"><i class="bi bi-cart"></i></button>
            </router-link>
          </div>
        </ul>
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
              <button type="button" class="btn btn-social"><i class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="registerModalLabel">Đăng ký</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="Handleregister">
            <!-- Username -->
            <div v-if="errors.username" class="text-danger small text-center">{{ errors.username[0] }}</div>
            <div class="mb-3 position-relative">

              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" placeholder="Tên đăng nhập" v-model="registerData.username">

            </div>

            <!-- Email -->
            <div v-if="errors.email" class="text-danger small text-center">{{ errors.email[0] }}</div>
            <div class="mb-3 position-relative">

              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" placeholder="Email" v-model="registerData.email">

            </div>

            <!-- Phone  -->
            <div v-if="errors.phone" class="text-danger small text-center">{{ errors.phone[0] }}</div>
            <div class="mb-3 position-relative">
              <i class="bi bi-telephone position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="phone" placeholder="Số điện thoại"
                v-model="registerData.phone">
            </div>

            <!-- Password -->
            <div v-if="errors.password" class="text-danger small text-center">{{ errors.password[0] }}</div>
            <div class="mb-3 position-relative">

              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" placeholder="Mật khẩu" v-model="registerData.password">

            </div>

            <!-- Confirm Password -->
            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" placeholder="Nhập lại mật khẩu"
                v-model="registerData.password_confirmation">
            </div>

            <!-- Đăng ký -->
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Đăng ký
              </button>
            </div>

            <!-- Chuyển sang đăng nhập -->
            <div class="mb-3 text-end">
              <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Đã có tài
                khoản</a>
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


  <!-- nhập code  -->
  <div class="modal fade" id="authenticationModal" tabindex="-1" aria-labelledby="authenticationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="authenticationModalLabel">Nhập mã xác nhận</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="verifyResetCode">
            <div class="mb-3 d-flex justify-content-center gap-3 small">
              <p v-if="!errorVerify" class="text-success text-center">
                Mã xác nhận đã được gửi về email. Vui lòng kiểm tra email.
              </p>

              <!-- Thông báo lỗi -->
              <p v-if="errorVerify" class="text-danger text-center">
                {{ errorVerify }}
              </p>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3 position-relative">
              <input v-for="(digit, index) in codeDigits" :key="index" v-model="codeDigits[index]" type="text"
                maxlength="1" class="form-control text-center border-black fw-bold fs-4"
                style="width: 50px; height: 50px;" @input="moveToNext(index, $event)"
                @keydown.backspace="handleBackspace($event, index)" @compositionstart="isComposing = true"
                @compositionend="isComposing = false" ref="inputs" />
            </div>
            <div class=" d-flex justify-content-center gap-3 small">
              <!-- <a href="#" class="text-decoration-none" > -->
              <!-- :class="{ 'disabled': isCounting }" @click="startCountdown" -->
              <p class="text-primary text-decoration-underline" style="cursor: pointer" @click="sendCode"
                v-if="!loadingSend && wait === 0">
                Gửi lại mã
              </p>

              <p class="text-muted" v-else-if="wait > 0">
                Gửi lại mã ({{ wait }}s)
              </p>

              <p class="text-muted" v-else>
                Đang gửi...
              </p>
              <!-- </a> -->
            </div>
            <div class="mb-3 d-flex justify-content-center align-items-center gap-2 small">
              <i class="bi bi-clock text-danger"></i>
              <p class="mb-0 fw-bold text-danger">
                {{ minutes.toString().padStart(2, '0') }}:{{ seconds.toString().padStart(2, '0') }}
              </p>
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Xác nhận
              </button>
              <!-- ata-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#resetModal" -->
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

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

  <router-view></router-view>

</template>

<script setup>
import { useCountdown } from "../stores/countDown";

const { formattedTime, isCounting, startCountdown } = useCountdown(60);
</script>


<script>
import axios from 'axios';
import { reactive, ref, onMounted } from 'vue';
import * as bootstrap from 'bootstrap';


window.bootstrap = bootstrap;

// biến để hiển thị countdown
const minutes = ref(0);
const seconds = ref(0);
let countdownInterval = null;

const startCountdown = (expireTime) => {
  clearInterval(countdownInterval); // clear nếu đang chạy

  const target = new Date(expireTime).getTime();
  console.log('Target timestamp:', target);
  countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const diff = Math.max(0, target - now);
    console.log('>>> Remaining:', diff);
    if (diff <= 0) {
      clearInterval(countdownInterval);
      minutes.value = 0;
      seconds.value = 0;
      return;
    }

    minutes.value = Math.floor(diff / 60000);
    seconds.value = Math.floor((diff % 60000) / 1000);
  }, 1000);
};

function stopCountdown() {
  clearInterval(timer);
}

// tạo thông tin register
const registerData = reactive({
  username: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: ''
});

// tạo thông tin login

const loginData = reactive({
  login: '',
  password: ''
});
// tạo biến báo lỗi đăng ký
const errors = reactive({});
const firstErrorKey = ref('');

// tạo hiệu ứng load
const loading = ref(false);
const loadingSend = ref(false);

// kiểm tra đã đăng nhập chưa
const user = ref(JSON.parse(localStorage.getItem('user')) || null);
const isLoggedIn = ref(!!user.value);


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

// lỗi đặt lại mật khẩu
const errorResetPass = ref('');




export default {
  setup() {
    onMounted(() => {
      isLoggedIn.value = !!user.value;
    });
  }
}
//  Đăng ký
const Handleregister = async () => {
  Object.keys(errors).forEach(key => delete errors[key]);
  loading.value = true;

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register', registerData);

    if (response.status === 200) {
      alert(response.data.message);

      // Lưu thông tin người dùng và token nếu backend trả về
      user.value = response.data.user;
      localStorage.setItem('user', JSON.stringify(response.data.user));
      localStorage.setItem('token', response.data.token);
      isLoggedIn.value = true;

      // Ẩn modal
      const modalElement = document.getElementById('registerModal');
      const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
      modalInstance.hide();

      // Xử lý backdrop thủ công nếu cần
      document.body.classList.remove('modal-open');
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

      // Reset form
      Object.keys(registerData).forEach(key => registerData[key] = '');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      if (error.response?.status === 422) {
        const allErrors = error.response.data.errors;
        const firstKey = Object.keys(allErrors)[0];

        // Xóa hết lỗi cũ
        Object.keys(errors).forEach(k => delete errors[k]);

        // Chỉ giữ lỗi đầu tiên
        errors[firstKey] = allErrors[firstKey];
        firstErrorKey.value = firstKey;
      }
    } else {
      console.error('Lỗi khi đăng ký:', error);
      alert('Có lỗi xảy ra, vui lòng thử lại sau.');
    }
  } finally {
    loading.value = false;
  }
};


//  Đăng nhập
const handleLogin = async () => {
  Object.keys(errors).forEach(key => delete errors[key]);
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
  } catch (error) {
    console.error('Lỗi đăng nhập:', error);
    loginError.value = '';

    if (error.response?.status === 422) {
      Object.assign(errors, error.response.data.errors);
      loginError.value = 'Vui lòng nhập đầy đủ thông tin hợp lệ.';
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
    // window.location.href = '/home';
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

// di chuyển ô input
const moveToNext = (index, event) => {
  const input = event.target
  const value = input.value

  if (value.length === 1 && index < 5) {
    const nextInput = input.nextElementSibling
    if (nextInput) nextInput.focus()
  } else if (value === '' && index > 0) {
    const prevInput = input.previousElementSibling
    if (prevInput) prevInput.focus()
  }
};

// chỉ nhập số
const onlyNumber = (event) => {
  const key = event.key
  if (!/^\d$/.test(key)) {
    event.preventDefault()
  }
};

const forgotPass = async () => {
  loading.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/forgot', {
      email: verify.email
    });

    if (response.status === 200) {
      alert(response.data.message);
      const modelElement = document.getElementById('forgotPasswordModal');
      const modalInstance = bootstrap.Modal.getInstance(modelElement) || new bootstrap.Modal(modelElement);
      modalInstance.hide();

      const modelCode = document.getElementById('authenticationModal');
      const modalInstanceCode = bootstrap.Modal.getInstance(modelCode) || new bootstrap.Modal(modelCode);
      modalInstanceCode.show();
    }


    console.log('>>> Expired time:', response.data.email_expired_at);
    if (response.data.email_expired_at) {
      startCountdown(response.data.email_expired_at);
    }



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



//hàm nhập code
const verifyResetCode = async () => {
  loading.value = true;
  const code = codeDigits.value.join('')
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/code', {
      'email': verify.email,
      'code': code
    })

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
    loading.value = false
  }
};


const sendCode = async () => {
  if (loadingSend.value || wait.value > 0) return;
  loadingSend.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/forgot', {
      email: verify.email
    });

    if (response.status === 200) {
      alert(response.data.message);

    }

    wait.value = 60;
    const timer = setInterval(() => {
      if (wait.value > 0) wait.value--;
      else clearInterval(timer);
    }, 1000);


    if (response.data.email_expired_at) {
      startCountdown(response.data.email_expired_at);
    }

  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 404 || status === 410) {
        errorVerify.value = error.response.data.errors?.email?.[0] || error.response.data.message;
      } else if (status === 422) {
        errorVerify.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorVerify.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorVerify.value = 'Lỗi kết nối đến server';
    }
  } finally {
    loadingSend.value = false;
  }
};

const ResetPass = async () => {
  loading.value = true
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
};

export {
  registerData,
  loginData,
  loginError,
  errors,
  loading,
  user,
  isLoggedIn,
  errorSendCode,
  errorVerify,
  firstErrorKey,
  codeDigits,
  verify,
  isComposing,
  inputs,
  errorResetPass,
  Handleregister,
  handleLogin,
  handleLogout,
  openLoginModal,
  moveToNext,
  onlyNumber,
  forgotPass,
  sendCode,
  verifyResetCode,
  ResetPass,
  startCountdown


}
</script>
<style scoped>
.text-primary-red {
  color: #ca111f;
}
</style>
