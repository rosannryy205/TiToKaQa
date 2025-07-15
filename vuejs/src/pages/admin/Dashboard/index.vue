<template v-if="hasPermission('view_dashboard')">
  <div>
    <h1>Dashboard</h1>
    <p>Chào mừng bạn đến với trang quản trị!</p>
    <a-card title="Thống kê nhanh">
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :sm="12" :md="8">
          <a-statistic title="Người dùng mới" :value="1128">
            <template #prefix>
              <UserOutlined />
            </template>
          </a-statistic>
        </a-col>
        <a-col :xs="24" :sm="12" :md="8">
          <a-statistic title="Đơn hàng" :value="93">
            <template #prefix>
              <ShoppingCartOutlined />
            </template>
          </a-statistic>
        </a-col>
        <a-col :xs="24" :sm="12" :md="8">
          <a-statistic title="Doanh thu (VND)" :precision="0" :value="15000000">
            <template #prefix>
              <DollarCircleOutlined />
            </template>
          </a-statistic>
        </a-col>
      </a-row>
    </a-card>
  </div>
</template>

<script setup>
import { Permission } from '@/stores/permission'
import { ref, onMounted, computed, watch } from 'vue';

import { UserOutlined, ShoppingCartOutlined, DollarCircleOutlined } from '@ant-design/icons-vue';
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)
</script>

<style scoped>
h1 {
  margin-bottom: 8px;
}

p {
  margin-bottom: 24px;
  color: #555;
}
</style>
