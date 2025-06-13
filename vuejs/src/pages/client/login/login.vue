<template>
  <div class="form-wrapper">
    <div class="form-container text-center">
      <h2 class="mb-4">ĐĂNG NHẬP</h2>
      <form method="POST" @submit.prevent="handleLogin">
        <!-- <div v-if="loginError" class="alert alert-danger">
          {{ loginError }}
        </div> -->
        <div class="mb-3">
          <label for="email" class="form-label visually-hidden">ĐỊA CHỈ EMAIL</label>
          <input v-model="loginData.login" type="email" id="email" name="email" class="form-control"
            placeholder="Nhập email của bạn" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label visually-hidden">MẬT KHẨU</label>
          <input v-model="loginData.password" type="password" id="password" name="password" class="form-control"
            placeholder="Nhập mật khẩu" required />
        </div>

        <button type="submit" class="btn btn-black w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          ĐĂNG NHẬP
        </button>


      </form>
      <div class="mt-4">
        <div class="divider mb-3"><span class="text-muted">HOẶC</span></div>
        <button class="btn btn-outline-dark w-100 mb-2" @click="loginWithGoogle()">
          <i class="fab fa-google me-2"></i> Đăng nhập với Google
        </button>
        <button class="btn btn-outline-primary w-100">
          <i class="fab fa-facebook-f me-2"></i> Đăng nhập với Facebook
        </button>
      </div>

      <p class="mt-4 text-muted" style="font-size: 0.9rem;">
        Bằng cách tiếp tục, bạn đồng ý với chúng tôi
        <a href="#" class="text-decoration-none">Điều khoản dịch vụ của nền tảng</a> và
        <a href="#" class="text-decoration-none">Chính sách bảo mật</a>.
      </p>
      <p class="mt-4 text-muted p" style="font-size: 0.9rem;">
        <router-link to="/register" class="text-decoration-none me-3">Đăng ký</router-link>
        <router-link to="/verify" class="text-decoration-none">Quên Mật khẩu</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userAuth';
import Swal from 'sweetalert2';

export default {
  name: 'LoginForm',

  data() {
    return {
      loginData: {
        login: '',
        password: '',
      },
      loginError: '',
      loading: false,
    };
  },

  setup() {
    const router = useRouter();
    const userStore = useUserStore();  // Khởi tạo store ở setup
    return { router, userStore };
  },

  methods: {
    async loginWithGoogle() {
      window.location.href = 'http://127.0.0.1:8000/api/auth/google/redirect';
    },

async handleLogin() {
  this.loginError = '';
  this.loading = true;

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', this.loginData);

    // Lưu user và token vào store
    this.userStore.setUser(response.data.user, response.data.token);

    // Hiển thị toast đăng nhập thành công
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đăng nhập thành công!',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });

    // Reset form
    this.loginData.login = '';
    this.loginData.password = '';

    // Điều hướng
    if (response.data.user.role === 'admin') {
      this.router.push('/admin');
    } else {
      this.router.push('/home');
    }
  } catch (error) {
    console.error('Lỗi đăng nhập:', error);

        if (error.response?.status === 422) {
          const errors = error.response.data.errors;
          const firstKey = Object.keys(errors)[0];
          this.loginError = errors[firstKey][0];
        } else if (error.response?.status === 401) {
          this.loginError = 'Sai email hoặc mật khẩu!';
        } else if (error.response?.status === 403) {
          this.loginError = error.response.data.message || 'Tài khoản của bạn không được phép đăng nhập.';
        } else if (error.response?.status === 500) {
          this.loginError = error.response.data.message || 'Lỗi máy chủ. Vui lòng thử lại sau.';
        } else if (error.request) {
          this.loginError = 'Không thể kết nối đến máy chủ. Kiểm tra internet.';
        } else {
          this.loginError = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
        }
      } finally {
        this.loading = false;
      }
    },

  },
};
</script>

<style scoped>
/* Giữ nguyên style bạn đã có */
.form-container {
  max-width: 400px;
  width: 100%;
  padding: 20px;
  background: #fff;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-black {
  background-color: #d41d1d;
  border-color: #000;
  color: #fff;
}

.btn-black:hover {
  background-color: #b21212;
  color: white;
}

.form-container input,
.form-container select,
.btn {
  border-radius: 0 !important;
}

h2 {
  font-size: 1.5rem;
}

.divider {
  display: flex;
  align-items: center;
  text-align: center;
}

.divider::before,
.divider::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid #ddd;
}

.divider::before {
  margin-right: 0.5em;
}

.divider::after {
  margin-left: 0.5em;
}

.form-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 70vh;
  margin-top: 20px;
  margin-bottom: 20px;
}

.navbar-brand {
  display: flex;
  align-items: center;
}

.alert {
  margin-bottom: 1rem;
  padding: 0.75rem 1.25rem;
  border-radius: 0.25rem;
  color: #721c24;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
}
</style>
