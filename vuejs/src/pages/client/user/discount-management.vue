<template>
    <div class="container col-12 col-md-8 col-lg-9 py-4">
      <h4 class="fw-bold mb-3">Kho Voucher</h4>
      <!---->
      <div class="d-flex align-items-center mb-3" style="gap: 10px">
        <input
          v-model="voucherCode"
          type="text"
          class="form-control"
          placeholder="Nhập mã voucher tại đây"
          style="max-width: 400px; font-size: 14px; border-radius: 0.25rem"
        />
        <button class="btn btn-outline-secondary px-4">Lưu</button>
      </div>
  
      <!---->
      <div class="d-flex border-bottom mb-3" style="gap: 20px; font-size: 14px">
        <div
          v-for="(tab, index) in tabs"
          :key="index"
          @click="activeTab = index"
          class="pb-2 position-relative"
          :class="{ 'fw-bold text-danger': activeTab === index, 'text-muted': activeTab !== index }"
          style="cursor: pointer"
        >
          {{ tab.label }}
          <span v-if="tab.count" class="text-secondary">({{ tab.count }})</span>
          <span
            v-if="activeTab === index"
            class="position-absolute start-0 bottom-0 w-100"
            style="height: 2px; background-color: #d9363e"
          ></span>
        </div>
      </div>
  
      <!---->
      <div class="row g-3">
        <div v-for="voucher in vouchers" :key="voucher.id" class="col-md-6">
          <div
            class="d-flex shadow-sm bg-white rounded overflow-hidden"
            style="min-height: 110px"
          >
            <!---->
            <div
              class="text-white d-flex flex-column justify-content-center align-items-center"
              style="background-color: #f44336; width: 30%"
            >
              <img :src="voucher.icon" alt="icon" style="width: 30px" />
              <div class="fw-bold small mt-2 text-center">{{ voucher.category }}</div>
            </div>
  
            <!---->
            <div class="flex-grow-1 px-3 py-2" style="width: 70%">
              <div class="fw-bold mb-1">{{ voucher.title }}</div>
              <div class="text-muted small mb-1">{{ voucher.condition }}</div>
              <div class="text-muted small mb-2">
                <i class="bi bi-clock me-1"></i> {{ voucher.expiry }}
                <a href="#" class="ms-2 text-primary">Điều Kiện</a>
              </div>
              <button class="btn btn-outline-danger btn-sm float-end">Dùng Sau</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const voucherCode = ref('')
  const activeTab = ref(0)
  
  const tabs = [
    { label: 'Tất cả', count: 1554 },
    { label: 'Shopee', count: 1494 },
    { label: 'Shop', count: 3 },
    { label: 'Nạp thẻ & Dịch vụ', count: 14 },
    { label: 'Scan & Pay', count: 42 },
    { label: 'Tài chính', count: 1 },
  ]
  
  const vouchers = [
    {
      id: 1,
      icon: '/img/icon-fashion.png',
      category: 'Thời Trang',
      title: 'Giảm 15% Giảm tối đa ₫600k',
      condition: 'Đơn tối thiểu ₫300k',
      expiry: 'Hiệu lực từ: 20.06.2025',
    },
    {
      id: 2,
      icon: '/img/icon-shopee.png',
      category: 'SHOPEE',
      title: 'Giảm 12% Giảm tối đa ₫300k',
      condition: 'Đơn tối thiểu ₫1tr',
      expiry: 'Hiệu lực sau: 2 giờ',
    },
  ]
  </script>
  