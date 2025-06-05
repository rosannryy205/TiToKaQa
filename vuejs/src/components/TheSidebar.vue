<template>
  <div>
    <!-- Overlay Sidebar -->
    <div
      class="custom-sidebar"
      :class="{ open: sidebarOpen }"
    >
      <!-- Close button inside sidebar (top-right) -->
      <div
        class="close-button"
        v-if="sidebarOpen && !isMobile"
        @click="toggleSidebar"
      >
        ✖
      </div>

      <div class="logo">
        <router-link to="/admin/dashboard">
          <img :src="logoUrl" alt="Logo" v-if="sidebarOpen || isMobile" />
          <img :src="logoUrl" alt="S Logo" width="40" v-else />
        </router-link>
      </div>

      <div class="menu-items">
        <router-link
          v-for="item in menuItems"
          :key="item.key"
          :to="item.to"
          class="menu-item"
          :class="{ active: route.path === item.to }"
          @click="closeSidebarIfMobile"
        >
          <component :is="item.icon" class="menu-icon" />
          <span>{{ item.label }}</span>
        </router-link>
      </div>
    </div>

    <!-- Toggle button (hamburger ☰) -->
    <div
      class="sidebar-toggle"
      v-if="!sidebarOpen"
      @click="toggleSidebar"
    >
      ☰
    </div>

    <!-- Dark overlay when sidebar is open on mobile -->
    <div
      v-if="sidebarOpen && isMobile"
      class="overlay"
      @click="sidebarOpen = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  DashboardOutlined,
  SettingOutlined,
  UserOutlined,
  HistoryOutlined,
} from '@ant-design/icons-vue'

const logoUrl = ref('/logonew.png')
const sidebarOpen = ref(false)
const isMobile = ref(false)

const route = useRoute()

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const closeSidebarIfMobile = () => {
  if (isMobile.value) {
    sidebarOpen.value = false
  }
}

// Responsive check
const checkMobile = () => {
  isMobile.value = window.innerWidth < 992
  if (!isMobile.value) {
    sidebarOpen.value = true
  }
}
onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

watch(route, () => {
  // Highlight và auto close mobile sidebar
  closeSidebarIfMobile()
})

const menuItems = [
  { key: '/admin/dashboard', to: '/admin/dashboard', label: 'Tổng quan', icon: DashboardOutlined },
  { key: '/products', to: '/admin/products', label: 'Món ăn', icon: DashboardOutlined },
  { key: '/options', to: '/admin/options', label: 'Topping', icon: DashboardOutlined },
  { key: '/products/combo', to: '/admin/products/combo', label: 'Combo', icon: DashboardOutlined },
  { key: '/categories', to: '/admin/categories', label: 'Danh mục món ăn', icon: SettingOutlined },
  { key: '/options/category-options', to: '/admin/options/category-options', label: 'Danh mục topping', icon: SettingOutlined },
  { key: '/orders/history', to: '/admin/orders/history', label: 'Đơn hàng', icon: HistoryOutlined },
  { key: '/users/list-role', to: '/admin/users/list-role', label: 'Vai trò người dùng', icon: UserOutlined },
]
</script>

<style scoped>
.custom-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 240px;
  background-color: #fff;
  border-right: 1px solid #eee;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
  transform: translateX(-100%);
  transition: transform 0.3s ease-in-out;
  z-index: 1000;
  padding-top: 64px;
}

.custom-sidebar.open {
  transform: translateX(0);
}

.close-button {
  position: absolute;
  top: 16px;
  right: 16px;
  font-size: 18px;
  background: transparent;
  border: none;
  cursor: pointer;
  color: #D9363E;
  z-index: 1001;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 0;
}

.logo img {
  height: 100px;
  object-fit: contain;
  max-width: 100%;
}

.menu-items {
  display: flex;
  flex-direction: column;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  color: #D9363E;
  text-decoration: none;
  transition: background 0.2s;
}

.menu-item:hover {
  background-color: #feeceb;
  color: #B71C1C;
}

.menu-item.active {
  background-color: #feeceb;
  border-right: 3px solid #D9363E;
  font-weight: bold;
}

.menu-icon {
  font-size: 16px;
}

.sidebar-toggle {
  position: fixed;
  top: 16px;
  left: 16px;
  z-index: 1100;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  user-select: none;
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.25);
  z-index: 999;
}
</style>
