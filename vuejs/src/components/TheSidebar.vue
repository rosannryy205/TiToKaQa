<template>
  <a-layout-sider
    :collapsed="props.collapsed"
    collapsible
    breakpoint="lg"
    :collapsed-width="isMobile ? 0 : 80"
    :trigger="isMobile ? null : undefined"
    :style="{ background: '#ffffff' }"
    @breakpoint="onBreakpoint"
    @update:collapsed="handleSiderCollapseChange"
  >
    <div class="logo">
      <router-link to="/admin/dashboard">
        <img :src="logoUrl" alt="Logo" v-if="!props.collapsed || isMobile" />
        <img :src="logoUrl" alt="S Logo" width="40" v-else />
      </router-link>
    </div>
    <a-menu
      theme="light"
      mode="inline"
      :style="{ background: '#ffffff', borderRight: 0 }"
      v-model:selectedKeys="selectedKeys"
      v-model:openKeys="openKeys"
    >
      <a-menu-item key="/admin/dashboard">
        <router-link :to="{ name: 'admin' }" style="color: #D9363E; text-decoration: none;">
          <dashboard-outlined />
          <span>Tổng quan</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="/products">
        <router-link :to="{ name: 'admin-products' }" style="color: #D9363E; text-decoration: none;">
          <dashboard-outlined />
          <span>Món ăn</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="/options">
        <router-link :to="{ name: 'admin-options' }" style="color: #D9363E; text-decoration: none;">
          <dashboard-outlined />
          <span>Topping</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="products/combo">
        <router-link :to="{ name: 'admin-products-combo' }" style="color: #D9363E; text-decoration: none;">
          <dashboard-outlined />
          <span>Combo</span>
        </router-link>
      </a-menu-item>

      <a-sub-menu key="sub1">
        <template #title>
          <span style="color: #D9363E; text-decoration: none;">
            <setting-outlined />
            <span>Danh mục</span>
          </span>
        </template>
        <a-menu-item key="/categories">
          <router-link :to="{ name: 'admin-categories' }" style="color: #D9363E; text-decoration: none;">
            <user-outlined />
            <span> Món ăn</span>
          </router-link>
        </a-menu-item>
        <a-menu-item key="/options/category-options">
          <router-link :to="{ name: 'admin-category-options' }" style="color: #D9363E; text-decoration: none;">
            <history-outlined /> <span>Topping</span>
          </router-link>
        </a-menu-item>
      </a-sub-menu>

      <a-menu-item key="/orders/history">
        <router-link :to="{ name: 'orders-history' }" style="color: #D9363E; text-decoration: none;">
          <dashboard-outlined />
          <span>Đơn hàng</span>
        </router-link>
      </a-menu-item>

      <a-sub-menu key="sub2">
        <template #title>
          <span style="color: #D9363E; text-decoration: none;">
             <router-link :to="{ name: 'admin-tables' }" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; width: 100%;">
                <setting-outlined />
                <span>Sơ đồ bàn</span>
            </router-link>
          </span>
        </template>
        <a-menu-item key="/tables/booking-schedule">
          <router-link :to="{ name: 'admin-tables-booking-schedule' }" style="color: #D9363E; text-decoration: none;">
            <user-outlined />
            <span> Lịch đặt bàn</span>
          </router-link>
        </a-menu-item>
        <a-menu-item key="/tables/current-order">
          <router-link :to="{ name: 'admin-tables-current-order' }" style="color: #D9363E; text-decoration: none;">
            <history-outlined /> <span>Đơn hiện tại</span>
          </router-link>
        </a-menu-item>
      </a-sub-menu>

      <a-sub-menu key="sub3">
        <template #title>
           <span style="color: #D9363E; text-decoration: none;">
            <router-link :to="{ name: 'users-list' }" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; width: 100%;">
                <setting-outlined />
                <span>Người dùng</span>
            </router-link>
          </span>
        </template>
        <a-menu-item key="/users/list-role">
          <router-link :to="{ name: 'users-list-role' }" style="color: #D9363E; text-decoration: none;">
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
  // ToolOutlined, // Not used in template
  // PieChartOutlined, // Not used in template
  HistoryOutlined,
} from '@ant-design/icons-vue'

const logoUrl = ref('/logonew.png')

const props = defineProps({
  collapsed: Boolean,
})

const emit = defineEmits(['update:collapsed'])

const isMobile = ref(false)
const route = useRoute()
const selectedKeys = ref([])
const openKeys = ref([])

// Consider updating menuStructure if you rely heavily on findParentSubMenuKey
// to include all submenu keys and their respective children paths for accurate openKeys behavior.
const menuStructure = [
  { key: 'sub1', childrenPaths: ['/categories', '/options/category-options'] }, // Adjusted to actual paths in sub1
  { key: 'sub2', childrenPaths: ['/tables/booking-schedule', '/tables/current-order'] },
  { key: 'sub3', childrenPaths: ['/users/list-role'] },
  // Add paths for direct menu items if they should close other submenus, or adjust logic
];


const onBreakpoint = (broken) => {
  isMobile.value = broken
}

const handleSiderCollapseChange = (newCollapsedValue) => {
  emit('update:collapsed', newCollapsedValue)
}

function findParentSubMenuKey(currentPath) {
  for (const item of menuStructure) {
    // Ensure paths in menuStructure are prefixed correctly if needed (e.g. /admin/)
    // For now, assuming paths in menuStructure match the `key` of the menu items
    if (item.childrenPaths.some(childPath => currentPath.includes(childPath))) {
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
        openKeys.value = [parentKey] // Open only the current parent
      }
    } else {
       // If it's a top-level item or no parent found, and not collapsed (or mobile), close other submenus
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
    } else {
      // Re-evaluate open keys based on current path when uncollapsing
      const parentKey = findParentSubMenuKey(route.path);
      if (parentKey) {
        openKeys.value = [parentKey];
      } else {
        openKeys.value = [];
      }
    }
  },
)
</script>

<style scoped>
.logo {
  height: 40px; /* Increased logo container height */
  margin: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  overflow: hidden;
}
.logo img {
  max-height: 40px; /* Increased max logo image height */
  max-width: 100%;
  object-fit: contain;
}
.ant-layout-sider {
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05); /* Softer shadow for white background */
  transition:
    width 0.2s,
    flex 0.2s;
}

/* Selected menu item style for light theme */
:deep(.ant-menu-light.ant-menu-inline .ant-menu-item-selected) {
  background-color: #FEECEB !important; /* Light red/pink background for selected item */
}
/* Ensure selected item's link text (and icon) remains red */
:deep(.ant-menu-light.ant-menu-inline .ant-menu-item-selected a),
:deep(.ant-menu-light.ant-menu-inline .ant-menu-item-selected a .anticon) {
  color: #D9363E !important;
}

/* Hover style for menu item links */
:deep(.ant-menu-light .ant-menu-item .ant-menu-title-content a:hover),
:deep(.ant-menu-light .ant-menu-item .ant-menu-title-content a:hover .anticon) {
  color: #B71C1C !important; /* Darker red on hover */
}

/* Hover style for submenu titles */
/* Target the span that has the inline color style */
:deep(.ant-menu-light .ant-menu-submenu-title:hover > .ant-menu-title-content > span),
:deep(.ant-menu-light .ant-menu-submenu-title:hover > .ant-menu-title-content > span .anticon),
:deep(.ant-menu-light .ant-menu-submenu-title:hover > .ant-menu-title-content > span a) /* if router-link is inside */
 {
  color: #B71C1C !important; /* Darker red on hover */
}


/* Style for the right border indicator on selected inline menu items */
:deep(.ant-menu-light.ant-menu-inline .ant-menu-item::after) {
  border-right: 3px solid #D9363E; /* Red indicator */
}

/* Ensure icons in sub-menu titles also get the hover color if they are part of the styled span */
:deep(.ant-menu-light .ant-menu-submenu .ant-menu-submenu-title:hover > .ant-menu-title-content > span > .anticon) {
    color: #B71C1C !important;
}
/* Ensure text in sub-menu titles also get the hover color */
:deep(.ant-menu-light .ant-menu-submenu .ant-menu-submenu-title:hover > .ant-menu-title-content > span > span:not(.anticon)) {
    color: #B71C1C !important;
}


/* If sub-menu title itself is a router-link, style its hover state */
:deep(.ant-menu-light .ant-menu-submenu-title > .ant-menu-title-content > span > a:hover),
:deep(.ant-menu-light .ant-menu-submenu-title > .ant-menu-title-content > span > a:hover .anticon),
:deep(.ant-menu-light .ant-menu-submenu-title > .ant-menu-title-content > span > a:hover span:not(.anticon))
{
  color: #B71C1C !important;
}

</style>