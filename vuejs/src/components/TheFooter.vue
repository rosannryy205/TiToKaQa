<template>
    <a-layout-footer style="text-align: center; padding: 12px 50px; background: #f0f2f5;">
      Admin Template ©{{ new Date().getFullYear() }} - Created with AntDV & Vue
    </a-layout-footer>
  </template>

  <script setup>
  // Không cần script gì đặc biệt ở đây
  </script>

  <style scoped>
  .ant-layout-footer {
    color: #888;
    font-size: 0.85em;
    position: sticky;
    bottom: 0%;
  }
  </style>
