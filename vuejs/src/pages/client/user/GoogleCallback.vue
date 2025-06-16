<template>
  <div class="text-center">Đang xử lý đăng nhập Google...</div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const route = useRoute()
const router = useRouter()

onMounted(async () => {
  const code = route.query.code
  const provider = 'google'

  try {
    const response = await axios.get(`http://localhost:8000/api/auth/${provider}/callback`, {
      params: { code },
    })


    const token = response.data.token
    const user = response.data.user

    localStorage.setItem('token', token)
    localStorage.setItem('user', JSON.stringify(user))


    if (user.role === 'quanly' || user.role === 'nhanvien' || user.role === 'nhanvienkho') {
      toast.success("Đang chuyển hướng tới trang admin")
      setTimeout(() => {
        router.push('/admin')
      }, 1500)
    } else {
      toast.success("Đăng nhập thành công!")
      setTimeout(() => {
        router.push('/').then(() => window.location.reload())
      }, 1500)
    }

  } catch (error) {
    const message = error.response?.data?.message || "Đăng nhập thất bại!"
    toast.error(message)
    if (error.response?.status === 409) {
      setTimeout(() => {
        router.push('/')
      }, 1500)
    }
  }
})
</script>
