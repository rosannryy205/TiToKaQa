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
        <template v-for="item in filteredMenuItems" :key="item.key">
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
import { ref, watch, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Permission } from '@/stores/permission'

const userId = ref(null);
const userString = localStorage.getItem('user');
if (userString) {
  const user = JSON.parse(userString);
  if (user && user.id !== undefined) {
    userId.value = user.id;
  }
}


const { hasPermission, permissions } = Permission(userId);

import {
  DashboardOutlined,
  AppstoreOutlined,
  ShoppingOutlined,
  FieldTimeOutlined,
  OrderedListOutlined,
  TagsOutlined,
  GoldOutlined,
  TeamOutlined,
  FileAddOutlined,
  HistoryOutlined,
  UnorderedListOutlined,
  TableOutlined,
} from '@ant-design/icons-vue'

const sidebarOpen = ref(false)
const showToggleIcon = ref(true)
const isMobile = ref(false)
const activeSubMenu = ref(null)

const route = useRoute()

const toggleSidebar = () => {
  if (sidebarOpen.value) {
    sidebarOpen.value = false
    showToggleIcon.value = false
    setTimeout(() => {
      showToggleIcon.value = true
    }, 300)
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

const toggleSubMenu = (key) => {
  activeSubMenu.value = activeSubMenu.value === key ? null : key
}

const isParentActive = (children) => {
  return children.some(child => route.path === child.to);
}

const filterMenuByPermissions = (menuItems) => {
  return menuItems
    .map((item) => {
      if (item.children) {
        const filteredChildren = item.children.filter((child) =>
          hasPermission(child.permission)
        )
        if (filteredChildren.length > 0 && (!item.permission || hasPermission(item.permission))) {
          return { ...item, children: filteredChildren }
        }
        return null
      } else if (hasPermission(item.permission)) {
        return item
      }
      return null
    })
    .filter(Boolean)
}

const filteredMenuItems = computed(() => {
  return filterMenuByPermissions(menuItems)
})


onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile);

  watch(filteredMenuItems, (newItems) => {
    let foundActiveSubMenu = false;
    newItems.forEach(item => {
      if (item.children && isParentActive(item.children)) {
        activeSubMenu.value = item.key;
        foundActiveSubMenu = true;
      }
    });
    if (!foundActiveSubMenu) {
      activeSubMenu.value = null;
    }
  }, { immediate: true });

  watch(route, () => {
    closeSidebarIfMobile()
  }, { immediate: true })
})


const menuItems = [
  {
    key: '/admin/dashboard',
    to: '/admin/dashboard',
    label: 'Thống kê',
    icon: DashboardOutlined,
    permission: 'view_dashboard',
  },
  {
    key: 'categories-management',
    label: 'Danh mục',
    icon: UnorderedListOutlined,
    permission: 'view_category', // Quyền để xem mục Danh mục cha
    children: [
      {
        key: '/admin/categories',
        to: '/admin/categories',
        label: 'Danh mục món ăn',
        icon: AppstoreOutlined,
        permission: 'view_category',
      },
      {
        key: '/admin/insert-food-category', // Đổi key/to cho rõ ràng
        to: '/admin/insert-food-category',
        label: 'Thêm DM món ăn',
        icon: FileAddOutlined,
        permission: 'create_category',
      },
      {
        key: '/admin/options/category-options',
        to: '/admin/options/category-options',
        label: 'Danh mục topping',
        icon: AppstoreOutlined,
        permission: 'view_topping', // Quyền xem topping category
      },
      {
        key: '/admin/insert-topping-category', // Đổi key/to cho rõ ràng
        to: '/admin/insert-topping-category',
        label: 'Thêm DM topping',
        icon: FileAddOutlined,
        permission: 'create_topping', // Quyền tạo topping category
      },
    ],
  },
  {
    key: 'products-management',
    label: 'Món ăn',
    icon: ShoppingOutlined,
    permission: 'view_food',
    children: [
      {
        key: '/admin/products',
        to: '/admin/products',
        label: 'Danh sách món ăn',
        icon: UnorderedListOutlined,
        permission: 'view_food',
      },
      {
        key: '/admin/insert-food',
        to: '/admin/insert-food',
        label: 'Thêm món ăn',
        icon: FileAddOutlined,
        permission: 'create_food',
      },
    ],
  },
  {
    key: 'combo-management',
    label: 'Combo',
    icon: AppstoreOutlined,
    permission: 'view_combo',
    children: [
      {
        key: '/admin/products/combo',
        to: '/admin/products/combo',
        label: 'Danh sách combo',
        icon: UnorderedListOutlined,
        permission: 'view_combo',
      },
      {
        key: '/admin/insert-combo',
        to: '/admin/insert-combo',
        label: 'Thêm combo',
        icon: FileAddOutlined,
        permission: 'create_combo',
      },
    ],
  },
  {
    key: 'topping-management',
    label: 'Topping',
    icon: GoldOutlined,
    permission: 'view_topping',
    children: [
      {
        key: '/admin/options',
        to: '/admin/options',
        label: 'Danh sách topping',
        icon: UnorderedListOutlined,
        permission: 'view_topping',
      },
      {
        key: '/admin/insert-topping', // Đổi key/to cho rõ ràng
        to: '/admin/insert-topping',
        label: 'Thêm topping',
        icon: FileAddOutlined,
        permission: 'create_topping',
      },
    ],
  },
  {
    key: 'order-management',
    label: 'Đơn hàng',
    icon: HistoryOutlined,
    permission: 'view_order',
    children: [
      {
        key: '/admin/orders/history',
        to: '/admin/orders/history',
        label: 'Danh sách đơn hàng',
        icon: AppstoreOutlined,
        permission: 'view_order',
      },
      {
        key: '/admin/tables/current-order', // Đã thêm /admin/
        to: '/admin/tables/current-order',
        label: 'Đơn hiện thời',
        icon: FieldTimeOutlined,
        permission: 'view_order',
      },
      {
        key: '/admin/order-create',
        to: '/admin/order-create',
        label: 'Thêm đơn hàng',
        icon: FileAddOutlined,
        permission: 'create_order',
      },
    ],
  },
  {
    key: 'tables-reservation-management',
    label: 'Bàn và đặt chỗ',
    icon: TableOutlined,
    permission: 'view_table',
    children: [
      {
        key: '/admin/tables',
        to: '/admin/tables',
        label: 'Danh sách bàn',
        icon: AppstoreOutlined,
        permission: 'view_table',
      },
      {
        key: '/admin/tables/booking-schedule', // Đã thêm /admin/
        to: '/admin/tables/booking-schedule',
        label: 'Lịch đặt bàn',
        icon: OrderedListOutlined,
        permission: 'view_booking',
      },
      {
        key: '/admin/insert-reservation', // Đã thêm /admin/
        to: '/admin/insert-reservation',
        label: 'Thêm đơn đặt bàn',
        icon: FileAddOutlined,
        permission: 'create_booking',
      },
    ],
  },
  {
    key: 'role-management', // Thay đổi key để rõ ràng hơn
    label: 'Vai trò',
    icon: TeamOutlined,
    permission: 'view_role', // Quyền xem vai trò
    children: [
      {
        key: '/admin/users/list-role', // Đổi key/to để phù hợp với vai trò
        to: '/admin/users/list-role',
        label: 'Danh sách vai trò',
        icon: UnorderedListOutlined,
        permission: 'view_role',
      },
      {
        key: '/admin/roles/insert', // Đổi key/to để phù hợp với vai trò
        to: '/admin/roles/insert',
        label: 'Thêm vai trò',
        icon: TagsOutlined,
        permission: 'create_role',
      },
    ],
  },
  {
    key: 'employee-management', // Thay đổi key để rõ ràng hơn
    label: 'Nhân viên',
    icon: TeamOutlined,
    permission: 'view_employee', // Quyền xem nhân viên
    children: [
      {
        key: '/admin/users/list', // Đổi key/to để phù hợp
        to: '/admin/users/list',
        label: 'Danh sách nhân viên',
        icon: UnorderedListOutlined,
        permission: 'view_employee',
      },
      {
        key: '/admin/employees/insert', // Đổi key/to để phù hợp
        to: '/admin/employees/insert',
        label: 'Thêm nhân viên',
        icon: FileAddOutlined,
        permission: 'create_employee',
      },
    ],
  },
  {
    key: 'customer-management',
    label: 'Khách hàng',
    icon: TeamOutlined,
    permission: 'view_customer', // Quyền xem khách hàng
    children: [
      {
        key: '/admin/users/list', // Đổi key/to để phù hợp
        to: '/admin/users/list',
        label: 'Danh sách khách hàng',
        icon: UnorderedListOutlined,
        permission: 'view_customer',
      },
      {
        key: '/admin/customers/insert', // Thêm mục để tạo khách hàng
        to: '/admin/customers/insert',
        label: 'Thêm khách hàng',
        icon: FileAddOutlined,
        permission: 'create_customer',
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
