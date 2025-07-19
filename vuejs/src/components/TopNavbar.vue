<template>
  <Navbar :show-navbar="showNavbar">
    <div class="navbar-wrapper">
      <div class="navbar-toggle" :class="{ toggled: sidebar.showSidebar }">
        <NavbarToggleButton @click="toggleSidebar" />
      </div>
      <a class="navbar-brand" href="/admin/dashboard">
        {{ routeName }}
      </a>
    </div>
    <button @click="toggleNavbar" class="navbar-toggler" type="button">
      <span class="navbar-toggler-bar navbar-kebab"></span>
      <span class="navbar-toggler-bar navbar-kebab"></span>
      <span class="navbar-toggler-bar navbar-kebab"></span>
    </button>

    <template #navbar-menu slot="navbar-menu">
      <a-space :size="16">
        <a-tooltip title="Tìm kiếm">
          <a-button type="text" shape="circle" :icon="h(SearchOutlined)" />
        </a-tooltip>
      </a-space>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a-dropdown :trigger="['click']">
              <a class="ant-dropdown-link no-underline" @click.prevent style="display:flex; align-items:center;">
                <a-avatar style=" margin-right: 8px;">
                  <template #icon>
                    <UserOutlined />
                  </template>
                </a-avatar>
                <span style="color: var(--text-color-white);">Admin</span>
                <DownOutlined style="margin-left: 8px; color: var(--text-color-dark);" class="nav-item" />
              </a>
              <template #overlay>
                <a-menu>
                  <a-menu-item key="profile">
                    <template #icon>
                      <IdcardOutlined />
                    </template>
                    Hồ sơ
                  </a-menu-item>
                  <a-menu-item key="settings">
                    <template #icon>
                      <SettingOutlined />
                    </template>
                    Cài đặt tài khoản
                  </a-menu-item>
                  <a-menu-divider />
                  <a-menu-item @click="handleLogout" key="logout" style="color: red;">
                    <template #icon>
                      <LogoutOutlined />
                    </template>
                    Đăng xuất
                  </a-menu-item>
                </a-menu>
              </template>
            </a-dropdown>
          </li>
        </ul>
    </template>
  </Navbar>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useSidebarStore } from '@/stores/sidebar.js'

import Navbar from '@/components/Admin/Navbar/Navbar.vue'
import NavbarToggleButton from '@/components/Admin/Navbar/NavbarToggleButton.vue'

import { h } from 'vue'; // Import h để dùng với icon trong a-button
import {
  MenuUnfoldOutlined,
  MenuFoldOutlined,
  SearchOutlined,
  BellOutlined,
  UserOutlined,
  DownOutlined,
  LogoutOutlined,
  SettingOutlined,
  IdcardOutlined,
} from '@ant-design/icons-vue';
import Swal from 'sweetalert2';

const route = useRoute()
const routeName = computed(() => {
  const name = route.name || ''
  return name.charAt(0).toUpperCase() + name.slice(1)
})


// Sidebar Pinia store
const sidebar = useSidebarStore()
const searchText = ref('')

// Toggle states
const showNavbar = ref(false)
const toggleNavbar = () => (showNavbar.value = !showNavbar.value)
function toggleSidebar() {
  sidebar.toggleSidebar()
}
const handleLogout = async () => {
  const confirmResult = await Swal.fire({
    title: 'Đăng xuất khỏi hệ thống?',
    text: 'Bạn sẽ cần đăng nhập lại để tiếp tục sử dụng!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Đăng xuất',
    cancelButtonText: 'Huỷ bỏ',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    reverseButtons: true,
    focusCancel: true,
  });
  if (confirmResult.isConfirmed) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login';
  }
};

</script>
<style scoped>
.no-underline {
  text-decoration: none;
}

.ant-dropdown-link {
  cursor: pointer;
}

.ant-dropdown-link:hover {
  opacity: 0.8;
}
</style>
