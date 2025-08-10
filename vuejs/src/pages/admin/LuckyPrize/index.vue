<template v-if="hasPermission('view_luckyprizes')">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-stats card-raised">
          <div class="card-body">
            <h3 class="title">Quản lý Quà Vòng Quay</h3>
            <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <router-link
              v-if="hasPermission('create_discount')"
              :to="{ name: 'insert-discount' }"
              class="btn btn-add"
            >
              + Thêm Mã Giảm Giá
            </router-link>

            <input v-model="searchQuery" type="text" class="clean-input" placeholder="Tìm kiếm" />
          </div>
            </div>
            </div>
            </div>
            </div>
  </template>
  <script setup>
  import { ref, onMounted, computed, watch } from 'vue'
  import axios from 'axios'
  import numeral from 'numeral'
  import { Permission } from '@/stores/permission'
  
  const userId = ref(null)    
  const { hasPermission } = Permission(userId)
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  </script>
  <style scoped>
  .title {
    font-weight: normal;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: #333;
  }
  
  .img_thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
  }
  
  .clean-input,
  .clean-select {
    border: 1px solid #ccc;
    border-radius: 4px;
    height: 28px;
    padding: 4px 8px;
    font-size: 0.85rem;
    background-color: transparent;
    outline: none;
    transition: border-color 0.2s ease;
    box-shadow: none;
    appearance: none;
    cursor: pointer;
  }
  
  .clean-input:focus,
  .clean-select:focus {
    border-color: #c92c3c;
  }
  
  .custom-select {
    box-shadow: none;
    border: 1px solid #bbb;
    padding: 2px 6px;
    height: 28px;
    font-size: 13px;
    border-radius: 4px;
  }
  
  .btn-add {
    background: none;
    color: #c92c3c;
    border: 1px solid #c92c3c;
    padding: 4px 10px;
    border-radius: 4px;
    font-weight: normal;
  }
  
  .btn-add:hover {
    background-color: #c92c3c;
    color: #fff;
  }
  
  .btn-update {
    background: none;
    color: #ab9c00;
    border: 1px solid #ab9c00;
    padding: 4px 10px;
    border-radius: 4px;
    font-weight: normal;
  }
  
  .btn-update:hover {
    background-color: #ab9c00;
    color: #fff;
  }
  
  .btn-outline {
    background: none;
    border: 1px solid #ccc;
    padding: 4px 10px;
    border-radius: 4px;
    color: #555;
  }
  
  .btn-outline:hover {
    background-color: #eee;
    color: #333;
  }
  
  .btn-clean {
    background-color: transparent !important;
    border: 1px solid #c92c3c;
    color: #c92c3c;
    padding: 4px 12px;
    font-size: 0.85rem;
    border-radius: 4px;
  }
  
  .btn-clean:hover {
    background-color: #c92c3c !important;
    color: white !important;
  }
  
  .btn-delete {
    border-color: #c92c3c !important;
    color: #c92c3c !important;
  }
  
  .btn-delete:hover {
    background-color: #c92c3c !important;
    color: white !important;
  }
  
  @media (max-width: 576px) {
    .img_thumbnail {
      width: 36px;
      height: 36px;
    }
  
    .clean-input,
    .clean-select,
    .custom-select {
      width: 100% !important;
      margin-top: 5px;
    }
  
    .btn-outline,
    .btn-clean {
      padding: 4px 8px;
      font-size: 0.8rem;
    }
  }
  </style>