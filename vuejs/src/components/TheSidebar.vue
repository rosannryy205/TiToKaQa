<template>
  <a-layout-sider
    :collapsed="props.collapsed"
    শক্তি
    collapsible
    breakpoint="lg"
    :collapsed-width="isMobile ? 0 : 80"
    :trigger="isMobile ? null : undefined"
    :style="{ background: 'var(--background-dark)', color: 'var(--text-color-light)' }"
    @breakpoint="onBreakpoint"
    @update:collapsed="handleSiderCollapseChange"
  >
    <div class="logo">
      <router-link to="/admin/dashboard">
        <img :src="logoUrl" alt="Logo" v-if="!props.collapsed || isMobile" />
        <img :src="logoUrl" alt="S Logo" width="32" v-else />
      </router-link>
    </div>
    <a-menu
      theme="dark"
      mode="inline"
      :style="{ background: 'var(--background-dark)', borderRight: 0 }"
      v-model:selectedKeys="selectedKeys"
      v-model:openKeys="openKeys"
    >
      <a-menu-item key="/admin/dashboard">
        <router-link :to="{ name: 'admin' }" style="color: var(--text-color-light); text-decoration: none;">
          <dashboard-outlined />
          <span>Tổng quan</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="/products">
        <router-link :to="{ name: 'admin-products' }" style="color: var(--text-color-light); text-decoration: none;">
          <dashboard-outlined />
          <span>Món ăn</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="/options">
        <router-link :to="{ name: 'admin-options' }" style="color: var(--text-color-light); text-decoration: none;">
          <dashboard-outlined />
          <span>Topping</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="products/combo">
        <router-link :to="{ name: 'admin-products-combo' }" style="color: var(--text-color-light); text-decoration: none;">
          <dashboard-outlined />
          <span>Combo</span>
        </router-link>
      </a-menu-item>

      <a-sub-menu key="sub1">
        <template #title>
          <span style="color: var(--text-color-light); text-decoration: none;">
            <setting-outlined />
            <span>Danh mục</span>
          </span>
        </template>
        <a-menu-item key="/categories">
          <router-link :to="{ name: 'admin-categories' }" style="color: var(--text-color-light); text-decoration: none;">
            <user-outlined />
            <span> Món ăn</span>
          </router-link>
        </a-menu-item>
        <a-menu-item key="/options/category-options">
          <router-link :to="{ name: 'admin-category-options' }" style="color: var(--text-color-light); text-decoration: none;">
            <history-outlined /> <span>Topping</span>
          </router-link>
        </a-menu-item>
      </a-sub-menu>

      <a-menu-item key="/orders/history">
        <router-link :to="{ name: 'orders-history' }" style="color: var(--text-color-light); text-decoration: none;">
          <dashboard-outlined />
          <span>Đơn hàng</span>
        </router-link>
      </a-menu-item>

      <a-sub-menu key="sub2">
        <template #title>
          <router-link :to="{ name: 'admin-tables' }" style="color: var(--text-color-light); text-decoration: none;">
            <setting-outlined />
            <span>Sơ đồ bàn</span>
          </router-link>
        </template>
        <a-menu-item key="/tables/booking-schedule">
          <router-link :to="{ name: 'admin-tables-booking-schedule' }" style="color: var(--text-color-light); text-decoration: none;">
            <user-outlined />
            <span> Lịch đặt bàn</span>
          </router-link>
        </a-menu-item>
        <a-menu-item key="/tables/current-order">
          <router-link :to="{ name: 'admin-tables-current-order' }" style="color: var(--text-color-light); text-decoration: none;">
            <history-outlined /> <span>Đơn hiện tại</span>
          </router-link>
        </a-menu-item>
      </a-sub-menu>

      <a-sub-menu key="sub3">
        <template #title>
          <router-link :to="{ name: 'users-list' }" style="color: var(--text-color-light); text-decoration: none;">
            <setting-outlined />
            <span>Người dùng</span>
          </router-link>
        </template>
        <a-menu-item key="/users/list-role">
          <router-link :to="{ name: 'users-list-role' }" style="color: var(--text-color-light); text-decoration: none;">
            <user-outlined />
            <span> Vai trò</span>
          </router-link>
        </a-menu-item>
      </a-sub-menu>
    </a-menu>
  </a-layout-sider>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import {
  DashboardOutlined,
  SettingOutlined,
  UserOutlined,
  ToolOutlined,
  PieChartOutlined,
  HistoryOutlined,
} from '@ant-design/icons-vue'

// Import logo (ví dụ, bạn cần thay đổi đường dẫn này cho đúng)
// import appLogo from '@/assets/images/your-logo.svg';
// Hoặc nếu logo nằm trong public:
const logoUrl = ref('/logonew.png') // Hoặc /ten-logo-cua-ban.svg

const props = defineProps({
  collapsed: Boolean,
})

// Thêm emit để thông báo cho component cha khi trạng thái collapsed thay đổi từ bên trong sider
const emit = defineEmits(['update:collapsed'])

const isMobile = ref(false)
const route = useRoute()
const selectedKeys = ref([])
const openKeys = ref([])

const menuStructure = [
  { key: 'sub1', childrenPaths: ['/admin/users', '/admin/orders', '/admin/settings'] },
]

const onBreakpoint = (broken) => {
  isMobile.value = broken
  // Khi breakpoint thay đổi, sider có thể tự động thay đổi trạng thái collapsed.
  // Chúng ta cần emit trạng thái mới này lên.
  // Tuy nhiên, Ant Design Sider khi có breakpoint sẽ tự xử lý collapsed và phát ra @update:collapsed
  // nên chúng ta không cần làm gì đặc biệt ở đây ngoài việc cập nhật isMobile.
}

// Hàm này sẽ được gọi khi a-layout-sider tự thay đổi trạng thái collapsed
// (ví dụ: do breakpoint hoặc người dùng click vào trigger mặc định của sider nếu có)
const handleSiderCollapseChange = (newCollapsedValue) => {
  emit('update:collapsed', newCollapsedValue)
}

function findParentSubMenuKey(currentPath) {
  for (const item of menuStructure) {
    if (item.childrenPaths.includes(currentPath)) {
      return item.key
    }
  }
  return null
}

watch(
  () => route.path,
  (newPath) => {
    selectedKeys.value = [newPath]
    const parentKey = findParentSubMenuKey(newPath)

    if (props.collapsed && !isMobile.value) {
      openKeys.value = []
    } else if (parentKey) {
      if (!openKeys.value.includes(parentKey)) {
        openKeys.value = [parentKey]
      }
    } else {
      if (!props.collapsed || isMobile.value) {
        openKeys.value = []
      }
    }
  },
  { immediate: true },
)

watch(
  () => props.collapsed,
  (isNowCollapsed) => {
    if (isNowCollapsed && !isMobile.value) {
      openKeys.value = []
    }
  },
)
</script>

<style scoped>
.logo {
  height: 32px;
  margin: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  overflow: hidden;
}
.logo img {
  max-height: 32px;
  max-width: 100%;
  object-fit: contain;
}
.ant-layout-sider {
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
  transition:
    width 0.2s,
    flex 0.2s; /* Thêm transition cho mượt mà */
}
:deep(.ant-menu-dark.ant-menu-inline .ant-menu-item-selected) {
  background-color: var(--primary-color) !important;
}
:deep(.ant-menu-dark .ant-menu-item > .ant-menu-title-content > a:hover),
:deep(.ant-menu-dark .ant-menu-item-active > .ant-menu-title-content > a) {
  color: var(--primary-color);
}
:deep(.ant-menu-dark .ant-menu-submenu-title:hover) {
  color: var(--primary-color) !important;
}
:deep(.ant-menu-dark.ant-menu-inline .ant-menu-item::after) {
  border-right: 3px solid var(--primary-color);
}
</style>
