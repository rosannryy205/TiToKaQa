<template>
  <div class="d-flex justify-content-center bg-light py-5">
    <div class="card shadow p-4" style="width: 400px">
      <h4 class="text-center">Nhập mã xác minh</h4>
      <div class="d-flex justify-content-center gap-2 my-3">
        <input
          v-for="(d, i) in digits"
          :key="i"
          v-model="digits[i]"
          maxlength="1"
          class="form-control text-center otp-input"
          style="width: 50px"
          :ref="el => (inputs[i] = el)"
          inputmode="numeric"
          pattern="[0-9]*"
          @input="onInput(i)"
          @keydown.backspace="onBackspace(i, $event)"
        />
      </div>
      <button class="btn btn-black w-100" :disabled="loading" @click="verifyCode">
        <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
        Xác minh</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const digits = ref(['', '', '', '', '', ''])
const inputs = ref([])
const loading = ref(false)

const onInput = (index) => {
  const val = digits.value[index]
  if (val.length === 1 && index < 5) {
    inputs.value[index + 1]?.focus()
  }
}

const onBackspace = (index, event) => {
  if (!digits.value[index] && index > 0) {
    inputs.value[index - 1]?.focus()
  }
}

const onlyNumber = (event) => {
  const key = event.key;
  if (!/^\d$/.test(key)) {
    event.preventDefault();
  }
};

const verifyCode = async () => {
  const email = localStorage.getItem('verify_email')
  const code = digits.value.join('')

  if (code.length !== 6) {
    alert('Mã xác minh phải đủ 6 chữ số')
    return
  }

  try {
    const res = await axios.post('http://127.0.0.1:8000/api/register/verify-code', {
      email,
      code
    })

    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(res.data.user))

    alert('Đăng ký thành công!')
    router.push('/')
  } catch (err) {
    alert(err.response?.data?.message || 'Mã xác minh không đúng')
  } finally{
    loading.value = false;
  }
}
</script>

<style scoped>
.otp-input {
  border: 2px solid #ccc;
  transition: border-color 0.2s;
  font-size: 1.2rem;
  height: 60px;
}
.otp-input:focus {
  border-color:rgb(222, 49, 49);
  outline: none;
  box-shadow: 0 0 0 2px rgba(49, 87, 222, 0.2);
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
</style>
