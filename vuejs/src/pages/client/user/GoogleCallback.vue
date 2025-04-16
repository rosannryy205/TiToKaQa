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
    const auth = useAuthStore();
    auth.setAuth(token, user);
    router.push('/');
  } else {
    console.error("Không tìm thấy token trong URL");
  }
});
  </script>
  