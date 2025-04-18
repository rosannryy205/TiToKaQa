<template>
    <div>Đang xử lý đăng nhập Google...</div>
  </template>

  <script setup>
  import { onMounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'

  const route = useRoute()
  const router = useRouter()

  onMounted(() => {
    const token = route.query.token
    const user = route.query.user ? JSON.parse(route.query.user) : null

    if (token && user) {
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))

      router.push('/').then(() => {
        window.location.reload()
      })
    } else {
      console.error("Không tìm thấy token hoặc user trong URL")
    }
  })
  </script>
