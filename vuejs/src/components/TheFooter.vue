<template>
  <!-- <a-layout-footer class="footer" style="text-align: center; padding: 12px 50px; background: #f0f2f5;"> -->
  <footer class="footer">
    <div class="container-fluid d-flex flex-wrap justify-content-between">
      <nav>

      </nav>
      <div>
        <div class="copyright">
          © {{ new Date().getFullYear() }}, made with <i class="fa fa-heart heart"></i> by<a
            class="font-weight-bold ml-1" target="_blank" rel="noopener">TITOKAQA</a>
        </div>
      </div>
    </div>
  </footer>
  <!-- </a-layout-footer> -->
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
