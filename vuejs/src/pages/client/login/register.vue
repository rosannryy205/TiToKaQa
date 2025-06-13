<template>
  <div class="form-wrapper">
    <div class="form-container text-center">
      <h2 class="mb-4" v-if="step === 1">ĐĂNG KÝ</h2>
      <h2 class="mb-4" v-else>NHẬP MÃ XÁC MINH</h2>

      <!-- Bước 1: Form đăng ký -->
      <form v-if="step === 1" @submit.prevent="handleSendCode">
        <div class="mb-3">
          <small class="text-danger" :style="{ visibility: errors.email ? 'visible' : 'hidden' }">{{ errors.email || ' '
            }}</small>
          <small class="text-danger" :style="{ visibility: errors.username ? 'visible' : 'hidden' }">{{ errors.username
            || ' ' }}</small>
          <small class="text-danger" :style="{ visibility: errors.password ? 'visible' : 'hidden' }">{{ errors.password
            || ' ' }}</small>
          <small class="text-danger" :style="{ visibility: errors.password_confirmation ? 'visible' : 'hidden' }">{{
            errors.password_confirmation || ' ' }}</small>
          <input type="email" v-model="form.email" class="form-control" placeholder="Nhập địa chỉ email" required />
        </div>

        <div class="mb-3">

          <input type="text" v-model="form.username" class="form-control" placeholder="Nhập tên đăng nhập" required />
        </div>

        <div class="mb-3">

          <input type="password" v-model="form.password" class="form-control" placeholder="Nhập mật khẩu" required />
        </div>

        <div class="mb-3">

          <input type="password" v-model="form.password_confirmation" class="form-control"
            placeholder="Xác nhận mật khẩu" required />
        </div>

        <button type="submit" class="btn btn-black w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          Đăng ký
        </button>


      </form>
      <!-- Tách phần đăng nhập mạng xã hội ra khỏi form -->
      <div v-if="step === 1" class="mt-4">
        <div class="divider mb-3"><span class="text-muted">HOẶC</span></div>
        <button type="button" class="btn btn-outline-dark w-100 mb-2" @click="loginWithGoogle">
          <i class="fab fa-google me-2"></i> Đăng nhập với Google
        </button>
        <button type="button" class="btn btn-outline-primary w-100" >
          <i class="fab fa-facebook-f me-2"></i> Đăng nhập với Facebook
        </button>
        <p class="mt-4 text-muted" style="font-size: 0.9rem;">
          Bằng cách tiếp tục, bạn đồng ý với chúng tôi
          <a href="#" class="text-decoration-none">Điều khoản dịch vụ của nền tảng</a> và
          <a href="#" class="text-decoration-none">Chính sách bảo mật</a>.
        </p>
        <p class="mt-4 text-muted p" style="font-size: 0.9rem;"></p>
      </div>


      <!-- Bước 2: Nhập mã xác minh 6 ô input -->
      <form v-else @submit.prevent="handleVerifyCode">
        <div class="d-flex justify-content-center gap-2 my-3 code-inputs">
          <input v-for="(digit, index) in digits" :key="index" v-model="digits[index]" maxlength="1"
            class="form-control text-center " style="width: 50px" inputmode="numeric" pattern="[0-9]*"
            @input="onInput(index)" @keydown.backspace="onBackspace(index, $event)" ref="otpInputs" />
        </div>

        <div class="mb-2 text-muted">Thời gian còn lại: {{ countdownText }}</div>
        <div class="text-danger mb-2" v-if="errorVerifyCode">{{ errorVerifyCode }}</div>

        <button type="submit" class="btn btn-black w-100" :disabled="loadingVerify">
          <span v-if="loadingVerify" class="spinner-border spinner-border-sm me-2"></span>
          Xác minh mã
        </button>
      </form>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userAuth';

const router = useRouter()

// đăng ký bằng google
const loginWithGoogle = () => {
  window.location.href = 'http://127.0.0.1:8000/api/auth/google/redirect';
};

// Bước form
const step = ref(1)

// Form đăng ký
const form = reactive({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = reactive({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const loadingVerify = ref(false)

// Mã xác minh dạng mảng 6 chữ số
const digits = reactive(['', '', '', '', '', ''])
const errorVerifyCode = ref('')

// Countdown timer
const countdown = ref(300) // 5 phút
const countdownText = ref('05:00')
let countdownInterval = null

// Các refs input mã xác minh
const otpInputs = ref([])

const clearErrors = () => {
  for (const key in errors) errors[key] = ''
}

// Hàm gửi code xác minh
const handleSendCode = async () => {
  clearErrors()
  loading.value = true
  try {
    const res = await axios.post('http://127.0.0.1:8000/api/register/send-code', form)

    if (res.status === 200 && res.data?.message?.includes('Mã xác minh')) {
      alert(res.data.message || 'Mã xác minh đã được gửi')
      localStorage.setItem('verify_email', form.email)
      step.value = 2

      // Countdown và focus...
      countdown.value = 300
      updateCountdownText()
      if (countdownInterval) clearInterval(countdownInterval)
      countdownInterval = setInterval(() => {
        if (countdown.value > 0) {
          countdown.value--
          updateCountdownText()
        } else {
          clearInterval(countdownInterval)
        }
      }, 1000)

      await nextTick()
      otpInputs.value[0]?.focus()
    } else {
      throw new Error('Không thể gửi mã xác minh.')
    }
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const errorObj = err.response.data.errors
      for (const key in errorObj) {
        errors[key] = errorObj[key][0]
      }
    } else if (err.response?.data?.message) {
      alert('Lỗi: ' + err.response.data.message)
    } else {
      alert('Có lỗi xảy ra. Vui lòng thử lại.')
    }
    console.error('Chi tiết lỗi:', err.response || err)
  }
  finally {
    loading.value = false
  }

}

const updateCountdownText = () => {
  const m = Math.floor(countdown.value / 60).toString().padStart(2, '0')
  const s = (countdown.value % 60).toString().padStart(2, '0')
  countdownText.value = `${m}:${s}`
}

// Xử lý nhập từng ô mã xác minh
const onInput = (index) => {
  const val = digits[index]
  if (val.length > 1) {
    digits[index] = val.slice(0, 1)
  }
  if (val.length === 1 && index < 5) {
    otpInputs.value[index + 1]?.focus()
  }
}

// Xử lý backspace để quay về ô trước
const onBackspace = (index, event) => {
  if (!digits[index] && index > 0) {
    otpInputs.value[index - 1]?.focus()
  }
}


const userStore = useUserStore();
// Gửi xác minh mã
const handleVerifyCode = async () => {
  errorVerifyCode.value = ''
  const code = digits.join('')
  if (code.length !== 6) {
    errorVerifyCode.value = 'Mã xác minh phải đủ 6 chữ số'
    return
  }
  loadingVerify.value = true
  try {
    const email = localStorage.getItem('verify_email')
    const res = await axios.post('http://127.0.0.1:8000/api/register/verify-code', { email, code })

    userStore.setUser(res.data.user, res.data.token);


    alert('Đăng ký thành công!')
    router.push('/')
  } catch (err) {
    errorVerifyCode.value = err.response?.data?.message || 'Mã xác minh không đúng'
  } finally {
    loadingVerify.value = false;
    localStorage.removeItem('verify_email');
  }
}
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

.code-inputs input {
  font-size: 1.5rem;
  padding: 0.5rem;
}
</style>
