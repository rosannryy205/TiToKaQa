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
          <button v-if="!isLoggedIn" class="icon-btn me-3" data-bs-toggle="modal"  @click="openLoginModal"
          >
            <i class="bi bi-people"></i>
          </button>

          <template v-else>
            <button class="icon-btn me-2" @click="handleLogout">
              <i class="bi bi-person-x"></i>
            </button>
            <p class="mb-0 me-3">{{ user.username }}</p>
          </template>

          <button class="icon-btn">
            <i class="bi bi-search"></i>
          </button>
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

            <div class="mb-3 position-relative input-group">
              <span class="input-icon">
                <i class="bi bi-person"></i>
              </span>
              <input type="text" class="form-control" v-model="loginData.login" placeholder="Tên đăng nhập hoặc email">
            </div>

            <div class="mb-3 position-relative input-group">
              <span class="input-icon">
                <i class="bi bi-lock"></i>
              </span>
              <input type="password" class="form-control" v-model="loginData.password" id="password"
                placeholder="Nhập mật khẩu">
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
            <div v-if="errors.username" class="text-danger small">{{ errors.username[0] }}</div>
            <div class="mb-3 position-relative">

              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" placeholder="Tên đăng nhập" v-model="registerData.username">

            </div>

            <!-- Email -->
            <div v-if="errors.email" class="text-danger small">{{ errors.email[0] }}</div>
            <div class="mb-3 position-relative">

              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" placeholder="Email" v-model="registerData.email">

            </div>

            <!-- Password -->
            <div v-if="errors.password" class="text-danger small">{{ errors.password[0] }}</div>
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

  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="forgotPasswordModalLabel">Quên mật khẩu</h5>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 position-relative">
              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="email" placeholder="Nhập Email">
            </div>

            <div class="mb-3">
              <button type="button" class="btn btn-login form-control fw-semibold" data-bs-dismiss="modal"
                data-bs-toggle="modal" data-bs-target="#authenticationModal">
                Gửi
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="authenticationModal" tabindex="-1" aria-labelledby="authenticationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="authenticationModalLabel">Nhập mã xác nhận</h5>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 d-flex justify-content-center gap-3 small">
              <p class="text-black">Mã xác nhận đã được gửi về email. Vui lòng kiểm tra email.</p>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3 position-relative">
              <input type="text" class="form-control text-center border-black fw-bold fs-4" maxlength="1"
                style="width: 50px; height: 50px;">
              <input type="text" class="form-control text-center border-black fw-bold fs-4" maxlength="1"
                style="width: 50px; height: 50px;">
              <input type="text" class="form-control text-center border-black fw-bold fs-4" maxlength="1"
                style="width: 50px; height: 50px;">
              <input type="text" class="form-control text-center border-black fw-bold fs-4" maxlength="1"
                style="width: 50px; height: 50px;">
              <input type="text" class="form-control text-center border-black fw-bold fs-4" maxlength="1"
                style="width: 50px; height: 50px;">
              <input type="text" class="form-control text-center border-black fw-bold fs-4" maxlength="1"
                style="width: 50px; height: 50px;">
            </div>
            <div class="mb-3 d-flex justify-content-center gap-3 small">
              <a href="#" class="text-decoration-none" :class="{ 'disabled': isCounting }" @click="startCountdown">
                Gửi lại
              </a>
            </div>
            <div class="mb-3 d-flex justify-content-center gap-3 small">
              <p class="text-black">{{ formattedTime }}</p>
            </div>
            <div class="mb-3">
              <button type="button" class="btn btn-login form-control fw-semibold" data-bs-dismiss="modal"
                data-bs-toggle="modal" data-bs-target="#resetModal">
                Xác nhận
              </button>
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
          <form>
            <div class="mb-3 position-relative">
              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password" placeholder="Mật khẩu">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password_confirm" placeholder="Nhập lại mật khẩu">
            </div>
            <div class="mb-3">
              <button type="button" class="btn btn-login form-control fw-semibold">Xác nhận</button>
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


const registerData = reactive({
  username: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const loginData = reactive({
  login: '',
  password: ''
});

const errors = reactive({});
const firstErrorKey = ref('');

const loading = ref(false);

const user = ref(JSON.parse(localStorage.getItem('user')) || null);
const isLoggedIn = ref(!!user.value);

onMounted(() => {
  isLoggedIn.value = !!user.value;
});

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


const loginError = ref('');


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

    alert('Đăng xuất thành công!');
  } catch (error) {
    console.error('Lỗi đăng xuất:', error);
    alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
  }
};

const openLoginModal = () => {
  // Xóa backdrop và class nếu bị sót từ lần trước
  document.body.classList.remove('modal-open');
  document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop').forEach(el => el.remove());

  // Lấy modal element và hiển thị lại
  const modalElement = document.getElementById('loginModal');
  const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
  modalInstance.show();
};




export {
  registerData,
  loginData,
  errors,
  loading,
  user,
  isLoggedIn,
  Handleregister,
  handleLogin,
  handleLogout,
  openLoginModal
};

</script>
