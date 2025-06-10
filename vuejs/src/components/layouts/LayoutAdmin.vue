<template>
  <div class="admin-layout d-flex">
    <TheSidebar :collapsed="collapsed" @toggleCollapse="toggleSidebar" />
    <div class="main-content flex-grow-1 d-flex flex-column">
      <TheHeader @toggleCollapse="toggleSidebar" />
      <main class="flex-grow-1 p-3">
        <router-view />
      </main>
      <TheFooter />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import TheSidebar from '../TheSidebar.vue'
import TheHeader from '../TheHeader.vue'
import TheFooter from '../TheFooter.vue'

const collapsed = ref(false)
const toggleSidebar = () => {
  collapsed.value = !collapsed.value
}
</script>

<style scoped>
.admin-layout {
  min-height: 100vh;
}
</style>
