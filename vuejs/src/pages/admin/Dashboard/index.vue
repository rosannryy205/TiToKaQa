<template>
    <div>
      <h1>Dashboard</h1>
      <p>Chào mừng bạn đến với trang quản trị!</p>
      <a-card title="Thống kê nhanh">
        <a-row :gutter="16">
          <a-col :span="8">
            <a-statistic title="Người dùng mới" :value="1128" style="margin-right: 50px">
              <template #prefix><UserOutlined /></template>
            </a-statistic>
          </a-col>
          <a-col :span="8">
            <a-statistic title="Đơn hàng" :value="93" class="demo-class">
              <template #prefix><ShoppingCartOutlined /></template>
            </a-statistic>
          </a-col>
           <a-col :span="8">
            <a-statistic title="Doanh thu (VND)" :precision="0" :value="15000000">
              <template #prefix><DollarCircleOutlined /></template>
            </a-statistic>
          </a-col>
        </a-row>
      </a-card>
    </div>
  </template>
  <script setup>
  import { UserOutlined, ShoppingCartOutlined, DollarCircleOutlined } from '@ant-design/icons-vue';
  </script>
