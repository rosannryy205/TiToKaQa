<template>
    <div>Đang xử lý đăng nhập Google...</div>
  </template>

  <script setup>
  import { useRoute, useRouter } from 'vue-router';
  import { onMounted } from 'vue';
  import { useAuthStore } from '@/stores/auth';

  const route = useRoute();
  const router = useRouter();

  onMounted(() => {
  const token = route.query.token;
  const user = JSON.parse(route.query.user);
  if (token && user) {
    // const auth = useAuthStore();
    // auth.setAuth(token, user);
    localStorage.setItem('user', JSON.stringify(route.query.token));
    localStorage.setItem('token', route.query.user);
    router.push('/');
  } else {
    console.error("Không tìm thấy token trong URL");
  }
});
</script>

<!--
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
// const auth = useAuthStore(); xoa
<p class="mb-0 me-2">{{ user.username }}</p> -->
