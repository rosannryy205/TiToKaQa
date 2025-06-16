<template>
  <a-layout-header
    :style="{ background: 'var(--background-light)', padding: '0 16px', boxShadow: '0 2px 8px rgba(0,0,0,0.09)' }">
    <div style="display: flex; align-items: center; justify-content: space-between; height: 100%;">
      <div class="header-left">
        <component :is="props.collapsed ? MenuUnfoldOutlined : MenuFoldOutlined" class="trigger"
          @click="() => emit('toggle-collapse')" />
      </div>
      <div class="header-right">
        <a-space :size="16">
          <a-tooltip title="Tìm kiếm">
            <a-button type="text" shape="circle" :icon="h(SearchOutlined)" />
          </a-tooltip>
          <a-tooltip title="Thông báo">
            <a-badge count="5">
              <a-button type="text" shape="circle" :icon="h(BellOutlined)" />
            </a-badge>
          </a-tooltip>
          <a-dropdown :trigger="['click']">
            <a class="ant-dropdown-link" @click.prevent style="display:flex; align-items:center;">
              <a-avatar style="background-color: var(--primary-color); margin-right: 8px;">
                <template #icon>
                  <UserOutlined />
                </template>
              </a-avatar>
              <span style="color: var(--text-color-dark);">Admin User</span>
              <DownOutlined style="margin-left: 8px; color: var(--text-color-dark);" />
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
        </a-space>
      </div>
    </div>
  </a-layout-header>
</template>

<script setup>
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

const props = defineProps({
  collapsed: Boolean,
});

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


const emit = defineEmits(['toggle-collapse']);
</script>

<style scoped>
.trigger {
  font-size: 18px;
  line-height: 64px;
  /* Căn giữa theo chiều dọc nếu header cao 64px */
  padding: 0 12px;
  cursor: pointer;
  transition: color 0.3s;
  color: var(--text-color-dark);
}

.trigger:hover {
  color: var(--primary-color);
}

.header-right .ant-btn-text {
  /* Cho icon button trong header */
  color: var(--text-color-dark);
}

.header-right .ant-btn-text:hover {
  background-color: rgba(0, 0, 0, 0.05);
  color: var(--primary-color);
}

.ant-dropdown-link {
  cursor: pointer;
}

.ant-dropdown-link:hover {
  opacity: 0.8;
}
</style>
