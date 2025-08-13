<template>
  <!-- <a-layout-footer style="text-align: center; padding: 12px 50px; background: #f0f2f5;">
    Admin Template ©{{ new Date().getFullYear() }} - Created with AntDV & Vue
  </a-layout-footer> -->
  <footer class="footer">
    <div class="container-fluid d-flex flex-wrap justify-content-between">
      <nav>
        <ul class="footer-menu">
          <li>
            <!-- <a href="/admin/dashboard" target="_blank" rel="noopener">CREATIVE TIM</a> -->
          </li>
        </ul>
      </nav>
      <div>
        <div class="copyright">
          © {{ new Date().getFullYear() }}, crafted by
          <a href="/home" class="font-weight-bold ml-1 titokaqua" target="_blank" rel="noopener">
            TITOKAQUA
          </a>
           Admin Dashboard. All rights reserved.

        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Không cần script gì đặc biệt ở đây
</script>

<style scoped>
/* .ant-layout-footer {
  color: #888;
  font-size: 0.85em;
  position: sticky;
  bottom: 0%;
} */
</style>
