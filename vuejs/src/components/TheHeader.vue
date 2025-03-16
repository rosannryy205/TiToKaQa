<template>
  <div class="header">
      <div class="navbar-top container">
          <div>
              <button class="icon-btn me-3"><i class="bi bi-people"></i></button>
              <button class="icon-btn"><i class="bi bi-search"></i></button>
          </div>
          <div class="logo-container">
              <img src="/img/Black_and_White_Flat_Retro_BBQ_Grill_Vintage_Logo-removebg-preview.png"
                  alt="Logo" class="logo">
          </div>
          <div>
              <button class="icon-btn me-3"><i class="bi bi-telephone"></i></button>
              <button class="icon-btn"><i class="bi bi-cart"></i></button>
          </div>

      </div>

      <!-- menu bottom -->
      <nav class="navbar navbar-expand-lg navbar-bottom">
          <div class="container">
              <!-- menu small screen  -->
              <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                  <span class="navbar-toggler-icon"></span>
              </button>

              <!-- menu -->
              <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex">
                  <ul class="navbar-nav fs-5">
                      <li class="nav-item"><a class="nav-link" href="#">Trang chủ</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Mì cay</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Mì trộn</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Lẩu</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Món ăn khác</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Thức uống</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Topping</a></li>
                      <li class="nav-item"><a class="nav-link" href="#">Đặt bàn</a></li>
                  </ul>
              </div>
          </div>
      </nav>

      <!-- offcanvas menu small screen -->
      <div class="offcanvas offcanvas-start" id="offcanvasMenu">
          <div class="offcanvas-header">
              <h5 class="offcanvas-title">Menu</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
              <ul class="navbar-nav">
                  <li class="nav-item"><a class="nav-link" href="#">Trang chủ</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Mì cay</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Mì trộn</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Lẩu</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Món ăn khác</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Thức uống</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Topping</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Đặt bàn</a></li>
              </ul>
          </div>
      </div>
  </div>
  <router-view></router-view>
</template>