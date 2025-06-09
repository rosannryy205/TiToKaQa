<template>
  <div>
    <div
      class="custom-sidebar"
      :class="{ open: sidebarOpen }"
    >
    <div class="logo-admin">
      <img src="/img/logonew.png" alt="">
    </div>
    <div class="close-button text-dark fw-bold" v-if="sidebarOpen" @click="toggleSidebar"> x
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

    <div
      class="sidebar-toggle"
      v-if="!sidebarOpen && showToggleIcon"
      @click="toggleSidebar"
    >
      ☰
    </div>
    <div
      v-if="sidebarOpen && isMobile"
      class="overlay"
      @click="toggleSidebar"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  DashboardOutlined,
  AppstoreOutlined,
  ShoppingOutlined,
  ClusterOutlined,
  OrderedListOutlined,
  TagsOutlined,
  HistoryOutlined,
  TeamOutlined,
  TableOutlined,
} from '@ant-design/icons-vue'

const logoUrl = ref('/logonew.png')
const sidebarOpen = ref(false)
const showToggleIcon = ref(true)
const isMobile = ref(false)

const route = useRoute()

const toggleSidebar = () => {
  if (sidebarOpen.value) {
    sidebarOpen.value = false
    showToggleIcon.value = false
    setTimeout(() => {
      showToggleIcon.value = true
    }, 300) // Delay khớp với transition CSS
  } else {
    sidebarOpen.value = true
    showToggleIcon.value = false
  }
}

const closeSidebarIfMobile = () => {
  if (isMobile.value) {
    sidebarOpen.value = false
    showToggleIcon.value = false
    setTimeout(() => {
      showToggleIcon.value = true
    }, 300)
  }
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 992
  showToggleIcon.value = true
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

watch(route, () => {
  closeSidebarIfMobile()
})

const menuItems = [
  {
    key: '/admin/dashboard',
    to: '/admin/dashboard',
    label: 'Tổng quan',
    icon: DashboardOutlined,
  },
  {
    key: '/products',
    to: '/admin/products',
    label: 'Món ăn',
    icon: ShoppingOutlined, 
  },
  {
    key: '/options',
    to: '/admin/options',
    label: 'Topping',
    icon: TagsOutlined,
  },
  {
    key: '/products/combo',
    to: '/admin/products/combo',
    label: 'Combo',
    icon: AppstoreOutlined, 
  },
  {
    key: '/categories',
    to: '/admin/categories',
    label: 'Danh mục món ăn',
    icon: ClusterOutlined, 
  },
  {
    key: '/options/category-options',
    to: '/admin/options/category-options',
    label: 'Danh mục topping',
    icon: ClusterOutlined,
  },
  {
    key: '/orders/history',
    to: '/admin/orders/history',
    label: 'Đơn hàng',
    icon: HistoryOutlined,
  },
  {
    key: '/users/list-role',
    to: '/admin/users/list-role',
    label: 'Vai trò người dùng',
    icon: TeamOutlined,
  },
  {
    key: '/users/tables',
    to: '/admin/tables/list',
    label: 'Danh sách bàn',
    icon: TableOutlined,
  },
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
}

.custom-sidebar.open {
  transform: translateX(0);
}
.logo-admin{
  width: 70px;
}
.logo-admin img{
  margin: 20px 20px 10px 20px;
max-width: 100%;
}

.close-button {
  position: absolute;
  top: 5px;
  right: 16px;
  font-size: 20px;
  border: none;
  cursor: pointer;
  z-index: 1001;
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
  /* z-index: 1100; */
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  user-select: none;
  transition: opacity 0.2s;
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
