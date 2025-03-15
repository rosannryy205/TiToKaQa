<template>
  <div>
    <Header v-if="
      route.path !== '/admin'
      && route.path !== '/admin-product'
      && route.path !== '/admin-cate'
      && route.path !== '/admin-users'
      && route.path !== '/addCate'
      && route.path !== '/addPro'
      && !route.path.includes('/editCate/')
      && !route.path.includes('/editPro/')
    " />

    <!-- Router View -->
    <RouterView />

    <!-- Footer (ẩn khi ở trang admin hoặc admin-product) -->
    <Footer v-if="
      route.path !== '/admin'
      && route.path !== '/admin-product'
      && route.path !== '/admin-cate'
      && route.path !== '/admin-users'
      && route.path !== '/addCate'
      && route.path !== '/addPro'
      && !route.path.includes('/editCate/')
      && !route.path.includes('/editPro/')
    " />
  </div>
</template>

<script setup>
// Import các component Header và Footer
import { useRoute, RouterView } from 'vue-router';
const route = useRoute();
import Footer from './components/layouts/Footer.vue';
import Header from './components/layouts/Header.vue';
</script>