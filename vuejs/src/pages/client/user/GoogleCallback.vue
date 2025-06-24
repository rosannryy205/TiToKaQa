<template>
  <div class="loader-wrapper">
    <div class="loader"></div>
  </div>
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
<style scoped>
.loader-wrapper {
  height: 50vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* loader */
.loader {
  width: 50px;
  --b: 8px;
  aspect-ratio: 1;
  border-radius: 50%;
  padding: 1px;
  background: conic-gradient(#0000 10%, #f03355) content-box;
  -webkit-mask:
    repeating-conic-gradient(#0000 0deg, #000 1deg 20deg, #0000 21deg 36deg),
    radial-gradient(farthest-side, #0000 calc(100% - var(--b) - 1px), #000 calc(100% - var(--b)));
  -webkit-mask-composite: destination-in;
  mask-composite: intersect;
  animation: l4 1s infinite steps(10);
}

@keyframes l4 {
  to {
    transform: rotate(1turn);
  }
}
</style>
