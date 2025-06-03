<template>
  <div class="form-wrapper">
    <div class="form-container text-center">
      <h2 class="mb-4">ĐẶT LẠI MẬT KHẨU</h2>

      <!-- BƯỚC 1: GỬI MÃ XÁC NHẬN -->
      <form v-if="step === 1" @submit.prevent="handleSendCode">
        <div class="text-danger mb-2" v-if="errorSendCode">{{ errorSendCode }}</div>
        <div class="mb-3">
          <input
            type="email"
            name="email"
            class="form-control"
            id="email"
            placeholder="Nhập địa chỉ email của bạn"
            v-model="email"
            required
          />
        </div>

        <button type="submit" class="btn btn-black w-100" :disabled="isLoading">
          <span v-if="isLoading">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Đang gửi...
          </span>
          <span v-else>Gửi mã xác nhận</span>
        </button>
      </form>

      <!-- BƯỚC 2: NHẬP MÃ XÁC NHẬN -->
      <form v-if="step === 2" @submit.prevent="handleVerifyCode">
        <div class="text-danger mb-2" v-if="errorVerifyCode">{{ errorVerifyCode }}</div>


        <div class="mb-3 d-flex justify-content-between code-inputs">
          <input
            v-for="(digit, index) in codeDigits"
            :key="index"
            type="text"
            maxlength="1"
            class="form-control text-center mx-1"
            v-model="codeDigits[index]"
            @input="focusNext(index)"
            @keydown.backspace="focusPrev($event, index)"
            ref="codeInputs"
            style="width: 40px;"
            required
          />
        </div>
        <div class="mb-2 text-muted">Thời gian còn lại: {{ countdownText }}</div>
        <button type="submit" class="btn btn-black w-100" :disabled="isLoading || code.length < 6">
          <span v-if="isLoading">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Đang xác minh...
          </span>
          <span v-else>Xác minh mã</span>
        </button>
      </form>

      <!-- BƯỚC 3: ĐẶT LẠI MẬT KHẨU -->
      <form v-if="step === 3" @submit.prevent="handleResetPassword">
        <div class="text-danger mb-2" v-if="errorReset">{{ errorReset }}</div>
        <div class="mb-3">
          <input
            type="password"
            class="form-control"
            placeholder="Mật khẩu mới"
            v-model="newPassword"
            required
          />
        </div>
        <div class="mb-3">
          <input
            type="password"
            class="form-control"
            placeholder="Nhập lại mật khẩu mới"
            v-model="confirmPassword"
            required
          />
        </div>

        <button type="submit" class="btn btn-black w-100" :disabled="isLoading">
          <span v-if="isLoading">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Đang đặt lại...
          </span>
          <span v-else>Đặt lại mật khẩu</span>
        </button>
      </form>

      <!-- BƯỚC 4: HOÀN TẤT -->
      <div v-if="step === 4">
        <p class="text-success">Mật khẩu đã được đặt lại thành công!</p>
        <a href="/login" class="btn btn-black w-100 mt-3">Quay lại đăng nhập</a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ForgotPasswordForm',
  data() {
    return {
      step: 1,
      email: '',
      codeDigits: ['', '', '', '', '', ''],
      newPassword: '',
      confirmPassword: '',
      errorSendCode: '',
      errorVerifyCode: '',
      errorReset: '',
      countdown: 0,
      isLoading: false,
      interval: null
    }
  },
  computed: {
    countdownText() {
      const min = Math.floor(this.countdown / 60)
      const sec = this.countdown % 60
      return `${min}:${sec < 10 ? '0' + sec : sec}`
    },
    code() {
      return this.codeDigits.join('')
    }
  },
  methods: {
    startCountdown() {
      this.countdown = 300
      this.interval = setInterval(() => {
        if (this.countdown > 0) {
          this.countdown--
        } else {
          clearInterval(this.interval)
          this.step = 1
          this.errorVerifyCode = 'Mã xác nhận đã hết hạn. Vui lòng gửi lại mã mới.'
        }
      }, 1000)
    },

    async handleSendCode() {
      this.errorSendCode = ''
      if (!this.email) {
        this.errorSendCode = 'Vui lòng nhập địa chỉ email.'
        return
      }

      this.isLoading = true
      try {
        await axios.post('http://127.0.0.1:8000/api/forgot', { email: this.email })
        this.step = 2
        this.codeDigits = ['', '', '', '', '', '']
        this.startCountdown()

        this.$nextTick(() => {
          this.$refs.codeInputs[0]?.focus()
        })
      } catch (err) {
        if (err.response?.data?.message) {
          this.errorSendCode = err.response.data.message
        } else if (err.response?.status === 404) {
          this.errorSendCode = 'Email không tồn tại trong hệ thống.'
        } else {
          this.errorSendCode = 'Đã xảy ra lỗi. Vui lòng thử lại.'
        }
      } finally {
        this.isLoading = false
      }
    },

    async handleVerifyCode() {
      this.errorVerifyCode = ''
      if (this.code.length < 6) {
        this.errorVerifyCode = 'Vui lòng nhập đầy đủ 6 chữ số.'
        return
      }

      this.isLoading = true
      try {
        await axios.post('http://127.0.0.1:8000/api/verify-code', {
          email: this.email,
          code: this.code
        })
        this.step = 3
        clearInterval(this.interval)
      } catch (err) {
        this.errorVerifyCode = err.response?.data?.message || 'Mã xác nhận không đúng hoặc đã hết hạn.'
      } finally {
        this.isLoading = false
      }
    },

    async handleResetPassword() {
      this.errorReset = ''
      this.isLoading = true
      try {
        await axios.post('http://127.0.0.1:8000/api/reset-password', {
          email: this.email,
          password: this.newPassword,
          password_confirmation: this.confirmPassword
        })
        this.step = 4
      } catch (err) {
        if (err.response && err.response.status === 422) {
          const errors = err.response.data.errors
          if (errors) {
            this.errorReset = Object.values(errors).flat().join(' ')
          } else {
            this.errorReset = err.response.data.message || 'Lỗi xác thực dữ liệu.'
          }
        } else {
          this.errorReset = err.response?.data?.message || 'Không thể đặt lại mật khẩu. Vui lòng thử lại.'
        }
      } finally {
        this.isLoading = false
      }
    },

    focusNext(index) {
      if (this.codeDigits[index].length === 1 && index < 5) {
        this.$refs.codeInputs[index + 1]?.focus()
      }
    },

    focusPrev(event, index) {
      if (event.key === 'Backspace' && !this.codeDigits[index] && index > 0) {
        this.$refs.codeInputs[index - 1]?.focus()
      }
    }
  },

  unmounted() {
    clearInterval(this.interval)
  }
}
</script>

<style scoped>
.form-container {
  max-width: 400px;
  width: 100%;
  padding: 20px;
  background: #fff;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-black {
  background-color: #000;
  border-color: #000;
  color: #fff;
}

.btn-black:hover {
  background-color: #d41d1d;
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

.form-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 70vh;
  margin-top: 20px;
  margin-bottom: 20px;
}

.code-inputs input {
  font-size: 1.5rem;
  padding: 0.5rem;
}
</style>
