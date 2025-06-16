<template>

  <body>
    <div class="wrapper">
      <!-- Nút mở menu trên màn hình nhỏ -->
      <button class="menu-btn d-lg-none" @click="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>
      <div :class="['sidebar z-3', { 'active': isSidebarOpen || isLargeScreen }]">
        <div class="mb-4 d-flex align-items-center justify-content-between">
          <div class="profile d-flex align-items-center gap-2 sticky-top">
            <!-- <i class="bi bi-person-circle fs-4"></i> -->
            <img src="/img/logonew.png" alt="" class="w-25" />
            <div>
              <div class="fw-bold text-danger" v-if="user">{{ user.username }}</div>
              <small class="text-muted"> <i class="bi bi-box-arrow-right"></i> Logout </small>
            </div>
          </div>
        </div>
        <nav class="nav flex-column">
          <router-link :to="{ name: 'admin' }" class="nav-link1" active-class="active" exact>
            <i class="bi bi-speedometer2"></i> Tổng quan
          </router-link>

          <router-link :to="{ name: 'admin-products' }"
            class="nav-link1 d-flex justify-content-between align-items-center" @click="toggleProductMenu">
            <span><i class="fa-solid fa-utensils"></i> Món ăn</span>
            <i class="bi bi-chevron-right toggle-icon"></i>
          </router-link>
          <div class="ps-4" v-show="isProductMenuOpen">
            <router-link :to="{ name: 'admin-categories' }" class="nav-link1"><i class="bi bi-card-list"></i> Danh
              mục</router-link>
            <router-link :to="{ name: 'admin-products-combo' }" class="nav-link1"><i class="bi bi-boxes"></i>
              Combo</router-link>
          </div>
          <router-link :to="{ name: 'admin-options' }"
            class="nav-link1 d-flex justify-content-between align-items-center" @click="toggleProductMenu1">
            <span><i class="bi bi-egg-fried"></i> Món ăn kèm</span>
            <i class="bi bi-chevron-right toggle-icon"></i>
          </router-link>
          <div class="ps-4" v-show="isProductMenuOpen1">
            <router-link :to="{ name: 'admin-category-options' }" class="nav-link1"><i class="bi bi-card-list"></i> Danh
              mục</router-link>
          </div>
          <router-link :to="{ name: 'orders-history' }" class="nav-link1"><i class="bi bi-card-heading"></i> Đơn
            hàng</router-link>
          <router-link :to="{ name: 'admin-tables' }"
            class="nav-link1 d-flex justify-content-between align-items-center" @click="toggleProductMenu2">
            <span><i class="bi bi-aspect-ratio"></i> Sơ đồ bàn</span>
            <i class="bi bi-chevron-right toggle-icon"></i>
          </router-link>
          <div class="ps-4" v-show="isProductMenuOpen2">
            <router-link :to="{ name: 'admin-tables-current-order' }" class="nav-link1"><i class="bi bi-stopwatch"></i>
              Đơn hiện thời</router-link>
            <router-link :to="{ name: 'admin-tables-booking-schedule' }" class="nav-link1"><i
                class="bi bi-card-list"></i> Lịch đặt bàn</router-link>
          </div>
          <router-link :to="{ name: 'users-list' }" class="nav-link1 d-flex justify-content-between align-items-center"
            @click="toggleProductMenu3">
            <span><i class="bi bi-person-vcard"></i> Người dùng</span>
            <i class="bi bi-chevron-right toggle-icon"></i>
          </router-link>
          <div class="ps-4" v-show="isProductMenuOpen3">
            <router-link :to="{ name: 'users-list-role' }" class="nav-link1"><i class="bi bi-person-rolodex"></i> Vai
              trò</router-link>
          </div>

        </nav>
      </div>
      <div class="content">
        <router-view></router-view>
      </div>
    </div>

  </body>

</template>
<script>
export default {
  data() {
    return {
      isSidebarOpen: false,
      isLargeScreen: window.innerWidth > 768,
      isProductMenuOpen: false,
      isProductMenuOpen1: false,
      isProductMenuOpen2: false,
      isProductMenuOpen3: false,
      user: null,
    };
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    checkScreenSize() {
      this.isLargeScreen = window.innerWidth > 768;
    },
    toggleProductMenu() {
      this.isProductMenuOpen = !this.isProductMenuOpen;
    },

    toggleProductMenu1() {
      this.isProductMenuOpen1 = !this.isProductMenuOpen1;
    },
    toggleProductMenu2() {
      this.isProductMenuOpen2 = !this.isProductMenuOpen2;
    },
    toggleProductMenu3() {
      this.isProductMenuOpen3 = !this.isProductMenuOpen3;
    }

  },
  mounted() {
    window.addEventListener("resize", this.checkScreenSize);
  },

  beforeUnmount() {
    window.removeEventListener("resize", this.checkScreenSize);
  },


};
</script>
<style>
body {
  font-family: Arial, sans-serif;
  background: url('Background.png') no-repeat center center;
  background-size: cover;
}

.sidebar {
  height: 100vh;
  width: 260px;
  position: fixed;
  top: 0;
  left: 0;
  background-color: #fff;
  border-right: 1px solid #eee;
  padding: 1rem;
  overflow-y: auto;
}

.sidebar .nav-link1 {
  color: #333;
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  align-items: center;
  text-decoration: none;
}

.sidebar .nav-link1:hover {
  background-color: #f3f4f6;
}

.sidebar .nav-link1.active {
  background-color: #f0ebff;
  color: #e04c4c;
  font-weight: 700;
}

.sidebar .nav-link1 i {
  margin-right: 10px;
}

.sidebar .section-title {
  font-size: 0.75rem;
  color: #999;
  text-transform: uppercase;
  margin: 1.5rem 0 0.5rem;
}

.sidebar .quick-link span {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-right: 8px;
}

.toggle-icon {
  transition: transform 0.3s ease;
  margin-left: auto;
}

.toggle-icon.rotate {
  transform: rotate(90deg);
}

.wrapper {
  display: flex;
}

.menu-btn {
  position: fixed;
  top: 10px;
  left: 10px;
  background: #C92C3C;
  color: white;
  border: none;
  padding: 8px 12px;
  font-size: 20px;
  cursor: pointer;
  border-radius: 5px;
  z-index: 1000;
}

.content {
  margin-left: 250px;
  padding: 20px;
  width: 100%;
  transition: 0.3s;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    left: -250px;
  }

  .sidebar.active {
    left: 0;
  }

  .content {
    margin-left: 0;
    margin-top: 50px;
  }
}
</style>
