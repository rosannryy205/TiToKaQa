<template>
  <div class="sidebar" :data-color="backgroundColor">
    <div class="logo">
      <a href="/admin/dashboard" class="simple-text logo-mini">
        <div class="logo-image">
          <img :src="logo" />
        </div>
      </a>
      <a href="/admin/dashboard" class="simple-text logo-normal title-admin">
        {{ title }}
      </a>

      <div class="navbar-minimize">
        <button id="minimizeSidebar" class="btn btn-simple btn-icon btn-neutral btn-round" @click="minimizeSidebar">
          <i class="now-ui-icons text_align-center visible-on-sidebar-regular"></i>
          <i class="now-ui-icons design_bullet-list-67 visible-on-sidebar-mini"></i>
        </button>
      </div>
    </div>

    <div class="sidebar-wrapper" ref="sidebarScrollArea">
      <slot></slot>
      <ul class="nav">
        <slot name="links">
          <SidebarItem v-for="(link, index) in menuItems" :key="link.label || index" :link="link">
            <SidebarItem v-for="subLink in link.children" :to="subLink.to" :link="subLink" />
          </SidebarItem>
        </slot>
      </ul>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, provide } from 'vue'
import SidebarItem from '@/components/Admin/SideBarPlugin/SideBarItem.vue'

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
  DeliveredProcedureOutlined,
  FileTextOutlined
} from '@ant-design/icons-vue'
const props = defineProps({
  backgroundColor: {
    type: String,
    default: 'grey',
    validator: (value) =>
      ['grey', ''].includes(value),
  },
  logo: {
    type: String,
    default: '/logonew.png',
  },
  title: {
    type: String,
    default: "TITOKAQA",
  },
  autoClose: {
    type: Boolean,
    default: true,
  }
})

// Provide autoClose cho component con
provide('autoClose', props.autoClose)

// Menu data (dữ liệu tĩnh)
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
        key: '/admin/options/category-options',
        to: '/admin/options/category-options',
        label: 'Danh mục topping',
        icon: AppstoreOutlined,
        permission: 'view_topping', // Quyền xem topping category
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

    ],
  },
  {
    key: 'foods_post',
    label: 'Bài viết',
    icon: FileTextOutlined, // hoặc ReadOutlined, ProfileOutlined, EditOutlined
    permission: 'view_order',
    children: [
      {
        key: '/admin/foods_post',
        to: '/admin/foods_post',
        label: 'Danh sách bài viết',
        icon: AppstoreOutlined,
        permission: 'view_order',
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
    key: 'delivery-management',
    label: 'Giao hàng',
    icon: DeliveredProcedureOutlined,
    permission: 'view_shipper',
    children: [
      {
        key: '/admin/delivery',
        to: '/admin/delivery',
        label: 'Danh sách đơn hàng',
        icon: AppstoreOutlined,
        permission: 'view_order',
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
        key: '/admin/tables/insert-reservation', // Đã thêm /admin/
        to: '/admin/tables/insert-reservation',
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
      // {
      //   key: '/admin/roles/insert', // Đổi key/to để phù hợp với vai trò
      //   to: '/admin/roles/insert',
      //   label: 'Thêm vai trò',
      //   icon: TagsOutlined,
      //   permission: 'create_role',
      // },
    ],
  },
  {
    key: 'employee-management', // Thay đổi key để rõ ràng hơn
    label: 'Nhân viên',
    icon: TeamOutlined,
    permission: 'view_employee', // Quyền xem nhân viên
    children: [
      {
        key: '/admin/users/list-employee', // Đổi key/to để phù hợp
        to: '/admin/users/list-employee',
        label: 'Danh sách nhân viên',
        icon: UnorderedListOutlined,
        permission: 'view_employee',
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
        key: '/admin/users/list-customer', // Đổi key/to để phù hợp
        to: '/admin/users/list-customer',
        label: 'Danh sách khách hàng',
        icon: UnorderedListOutlined,
        permission: 'view_customer',
      },
      {
        key: '/admin/users/chat', // Đổi key/to để phù hợp
        to: '/admin/users/chat',
        label: 'Chat',
        icon: UnorderedListOutlined,
        permission: 'view_customer',
      },

    ],
  },
]
function minimizeSidebar() {
  const body = document.body
  const isMini = body.classList.toggle('sidebar-mini')
  localStorage.setItem('sidebar-mini', isMini)

  const simulateWindowResize = setInterval(() => {
    window.dispatchEvent(new Event('resize'))
  }, 180)
  setTimeout(() => {
    clearInterval(simulateWindowResize)
  }, 1000)
}
onMounted(() => {
  if (localStorage.getItem('sidebar-mini') === 'true') {
    document.body.classList.add('sidebar-mini')
  }
})

</script>

<style scoped>
.title-admin {
  font-family: 'TAN HEADLINE', sans-serif;
}

@media (min-width: 992px) {

  .navbar-search-form-mobile,
  .nav-mobile-menu {
    display: none;
  }
}
</style>
