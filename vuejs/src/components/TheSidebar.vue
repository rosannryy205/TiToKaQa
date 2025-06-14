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
        <template v-for="item in menuItems" :key="item.key">
          <router-link
            v-if="!item.children"
            :to="item.to"
            class="menu-item"
            :class="{ active: route.path === item.to }"
            @click="closeSidebarIfMobile"
          >
            <component :is="item.icon" class="menu-icon" />
            <span>{{ item.label }}</span>
          </router-link>

          <div v-else class="menu-item-with-submenu" :class="{ 'active-parent': isParentActive(item.children) }">
            <div class="menu-item parent-item" @click="toggleSubMenu(item.key)">
              <component :is="item.icon" class="menu-icon" />
              <span>{{ item.label }}</span>
              <span class="submenu-arrow" :class="{ 'rotated': activeSubMenu === item.key }"><i class="bi bi-caret-right"></i></span>
            </div>
            <div v-if="activeSubMenu === item.key" class="submenu-items">
              <router-link
                v-for="subItem in item.children"
                :key="subItem.key"
                :to="subItem.to"
                class="submenu-item"
                :class="{ active: route.path === subItem.to }"
                @click="closeSidebarIfMobile"
              >
                <component :is="subItem.icon" class="submenu-icon" v-if="subItem.icon" />
                <span>{{ subItem.label }}</span>
              </router-link>
            </div>
          </div>
        </template>
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
  DownOutlined, // Thêm icon cho mũi tên xổ xuống
  UpOutlined, // Thêm icon cho mũi tên xổ lên
} from '@ant-design/icons-vue'

const logoUrl = ref('/logonew.png')
const sidebarOpen = ref(false)
const showToggleIcon = ref(true)
const isMobile = ref(false)
const activeSubMenu = ref(null) // State để quản lý sub-menu đang mở

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

// Function để mở/đóng sub-menu
const toggleSubMenu = (key) => {
  activeSubMenu.value = activeSubMenu.value === key ? null : key
}

// Function kiểm tra xem có sub-menu nào đang active không để làm nổi bật mục cha
const isParentActive = (children) => {
  return children.some(child => route.path === child.to);
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  // Mở sub-menu nếu đường dẫn hiện tại khớp với một trong các sub-menu
  menuItems.forEach(item => {
    if (item.children && isParentActive(item.children)) {
      activeSubMenu.value = item.key;
    }
  });
})

watch(route, () => {
  closeSidebarIfMobile()
  // Đóng tất cả sub-menu khi chuyển route nếu không có sub-menu nào active
  let foundActiveSubMenu = false;
  menuItems.forEach(item => {
    if (item.children && isParentActive(item.children)) {
      activeSubMenu.value = item.key;
      foundActiveSubMenu = true;
    }
  });
  if (!foundActiveSubMenu) {
    activeSubMenu.value = null; // Đóng tất cả nếu không có sub-menu nào active
  }
})


const menuItems = [
  {
    key: '/admin/dashboard',
    to: '/admin/dashboard',
    label: 'Tổng quan',
    icon: DashboardOutlined,
  },
  {
    key: 'products-management', // Key cho mục cha
    label: 'Quản lý sản phẩm',
    icon: ShoppingOutlined,
    children: [
      {
        key: '/admin/products',
        to: '/admin/products',
        label: 'Món ăn',
        icon: AppstoreOutlined, // Bạn có thể dùng icon khác cho sub-menu
      },
      {
        key: '/admin/options',
        to: '/admin/options',
        label: 'Topping',
        icon: TagsOutlined,
      },
      {
        key: '/admin/products/combo',
        to: '/admin/products/combo',
        label: 'Combo',
        icon: AppstoreOutlined,
      },
    ],
  },
  {
    key: 'categories-management', // Key cho mục cha
    label: 'Quản lý danh mục',
    icon: ClusterOutlined,
    children: [
      {
        key: '/admin/categories',
        to: '/admin/categories',
        label: 'Danh mục món ăn',
        icon: AppstoreOutlined,
      },
      {
        key: '/admin/options/category-options',
        to: '/admin/options/category-options',
        label: 'Danh mục topping',
        icon: TagsOutlined,
      },
    ],
  },
  {
    key: 'order-management',
    label: 'Đơn hàng',
    icon: HistoryOutlined,
     children: [
      {
        key: '/admin/orders/history',
        to: '/admin/orders/history',
        label: 'Danh sách đơn hàng',
        icon: AppstoreOutlined,
      },
    ]
  },
  {
    key: 'tables-reservation-management', // Key cho mục cha
    label: 'Bàn và đặt chỗ',
    icon: ClusterOutlined,
    children: [
      {
        key: '/admin/tables',
        to: '/admin/tables',
        label: 'Danh sách bàn',
        icon: AppstoreOutlined,
      },
      {
        key: 'tables/booking-schedules',
        to: '/admin/tables/booking-schedule',
        label: 'Lịch đặt bàn',
        icon: TagsOutlined,
      },
      {
        key: 'tables/current-order',
        to: '/admin/tables/current-order',
        label: 'Đơn hiện thời',
        icon: TagsOutlined,
      },
    ],
  },
  {
    key: 'users-management', // Key cho mục cha
    label: 'Quản lý người dùng',
    icon: ClusterOutlined,
    children: [
      {
        key: 'users/list',
        to: '/admin/users/list',
        label: 'Khách hàng',
        icon: AppstoreOutlined,
      },
      {
        key: 'users/list-role',
        to: '/admin/users/list-role',
        label: 'Nhân viên',
        icon: TagsOutlined,
      },
    ],
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
  overflow-y: auto; /* Thêm scroll nếu nội dung quá dài */
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
  cursor: pointer; /* Đặt con trỏ thành pointer cho tất cả menu-item */
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

/* Kiểu cho mục cha khi có sub-menu đang active */
.menu-item-with-submenu.active-parent .parent-item {
  background-color: #feeceb;
  color: #B71C1C;
  font-weight: bold;
}

.menu-icon {
  font-size: 16px;
}

.sidebar-toggle {
  position: fixed;
  top: 16px;
  left: 16px;
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

/* CSS cho Sub-menu */
.submenu-items {
  display: flex;
  flex-direction: column;
  padding-left: 20px; /* Thụt lề cho các mục con */
  background-color: #f8f8f8; /* Nền nhẹ hơn cho sub-menu */
}

.submenu-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px 10px 30px; /* Thêm padding để thụt vào thêm */
  color: #666;
  text-decoration: none;
  transition: background 0.2s;
}

.submenu-item:hover {
  background-color: #e0e0e0;
  color: #333;
}

.submenu-item.active {
  background-color: #e0e0e0;
  border-right: 3px solid #D9363E;
  font-weight: bold;
  color: #D9363E;
}

.submenu-icon {
  font-size: 14px; /* Kích thước icon nhỏ hơn cho sub-menu */
}

.parent-item {
  position: relative; /* Để đặt mũi tên */
}

.submenu-arrow {
  margin-left: auto; /* Đẩy mũi tên về bên phải */
  transition: transform 0.2s ease;
}

.submenu-arrow.rotated {
  transform: rotate(90deg); /* Xoay mũi tên khi sub-menu mở */
}
</style>
