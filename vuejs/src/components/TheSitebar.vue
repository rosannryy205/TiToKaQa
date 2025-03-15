<template>
    <div class="wrapper">
        <!-- Nút mở menu trên màn hình nhỏ -->
        <button class="menu-btn d-md-none" @click="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar -->
        <div :class="['sidebar z-3', { 'active': isSidebarOpen || isLargeScreen }]">
            <h2>Admin</h2>
            <ul class="sidebar-menu">
                <li><a href="#"><i class="fas fa-tachometer-alt"></i> Tổng quan</a></li>
                <li><router-link :to="{ name: 'admin-categories' }"><i class="fas fa-list"></i> Danh mục</router-link></li>
                <li>
                    <div class="d-flex justify-content-between align-items-center">
                        <router-link :to="{ name: 'admin-products' }"><i class="fas fa-box"></i> Món ăn</router-link>
                        <a href="#" @click="toggleProductMenu">
                            <i class="fas fa-chevron-down" :class="{ 'rotate': isProductMenuOpen }"></i>
                        </a>
                    </div>
                    <ul v-show="isProductMenuOpen" class="list-unstyled ps-3">
                        <li><a href="#"><i class="fas fa-palette"></i> Kích thước</a></li>
                    </ul>
                    <ul v-show="isProductMenuOpen" class="list-unstyled ps-3">
                        <li><a href="#"><i class="fas fa-palette"></i> Cấp độ</a></li>
                    </ul>
                </li>

                <li><a href="#"><i class="fas fa-shopping-cart"></i> Đơn hàng</a></li>
            </ul>
        </div>

        <!-- Nội dung chính -->
        <div class="content">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isSidebarOpen: false,
            isLargeScreen: window.innerWidth >= 768, 
            isProductMenuOpen: false,
        };
    },
    methods: {
        toggleSidebar() {
            this.isSidebarOpen = !this.isSidebarOpen;
        },
        checkScreenSize() {
            this.isLargeScreen = window.innerWidth >= 768;
        },
        toggleProductMenu() {
            this.isProductMenuOpen = !this.isProductMenuOpen;
        },
    },
    mounted() {
        window.addEventListener("resize", this.checkScreenSize);
    },
    beforeUnmount() {
        window.removeEventListener("resize", this.checkScreenSize);
    }
};
</script>

<style scoped>
.wrapper {
    display: flex;
}

.rotate {
    transform: rotate(180deg);
    transition: transform 0.3s;
}

.sidebar {
    width: 250px;
    background: #F5F5F5;
    padding: 20px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: -250px;
    transition: 0.3s;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

/* Khi sidebar mở */
.sidebar.active {
    left: 0;
}

.sidebar h2 {
    font-size: 22px;
    text-align: center;
    font-weight: bold;
    color: #C92C3C;
}

.sidebar-menu li {
    list-style: none;
    padding: 10px 0;
}

.sidebar-menu a {
    text-decoration: none;
    color: #000000;
    display: flex;
    align-items: center;
    padding: 10px 10px 0 0;
    border-radius: 5px;
    transition: background 0.3s, color 0.3s;
}

.sidebar-menu a i {
    margin-right: 15px;
    font-size: 20px;
}

.sidebar-menu a:hover {
    color: #C92C3C;
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
