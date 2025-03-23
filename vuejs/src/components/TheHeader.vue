<template>
  <!-- top header-->
  <div class="header">
    <div class="container">
      <div class="navbar-top">
        <nav class="navbar navbar-expand-lg navbar-bottom">
          <div class="container">
            <!-- menu small screen  -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
        </nav>
        <div class="nav-icons d-none d-lg-block">
          <button class="icon-btn me-3" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="bi bi-people"></i>
          </button>

          <button class="icon-btn"><i class="bi bi-search"></i></button>
        </div>

        <div class="logo-container">
          <img src="/img/logonew.png" alt="Logo" class="logo">
        </div>

        <div class="nav-icons d-none d-lg-block">
          <button class="icon-btn me-3"><i class="bi bi-telephone"></i></button>
          <router-link to="/cart" style="color: black;">
            <button class="icon-btn"><i class="bi bi-cart"></i></button>
          </router-link>
        </div>
      </div>

      <!-- menu bottom -->
      <nav class="navbar navbar-expand-lg navbar-bottom">
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
      </nav>
    </div>

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
          <div class="icon">
            <button class="icon-btn me-3" data-bs-toggle="modal" data-bs-target="#loginModal">
              <i class="bi bi-people"></i>
            </button>
            <button class="icon-btn"><i class="bi bi-search"></i></button>
            <button class="icon-btn me-3"><i class="bi bi-telephone"></i></button>
            <router-link to="/cart" style="color: black;">
              <button class="icon-btn"><i class="bi bi-cart"></i></button>
            </router-link>
          </div>
        </ul>
      </div>
    </div>
  </div>

  <!-- Modal đăng nhập -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title w-100 text-center fw-bold" id="loginModalLabel">Đăng nhập</h5>
        </div>
        <div class="modal-body px-4 py-3">
          <form>
            <div class="mb-3 position-relative">
              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="email" placeholder="Tên đăng nhập">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password" placeholder="Nhập mật khẩu">
            </div>

            <div class="mb-3 d-flex justify-content-end gap-3 small">
              <a href="#" class="text-decoration-none">Quên mật khẩu</a>
              <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal"
                class="text-decoration-none">Đăng ký</a>
            </div>

            <div class="mb-3">
              <button type="button" class="btn btn-login form-control fw-semibold">Đăng nhập</button>
            </div>

            <div class="divider d-flex align-items-center mb-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">hoặc đăng nhập</span>
              <hr class="flex-grow-1">
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button type="button" class="btn btn-social rounded-circle"><i class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social rounded-circle"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social rounded-circle"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="registerModalLabel">Đăng ký</h5>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 position-relative">
              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="username" placeholder="Tên đăng nhập">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="email" placeholder="Email">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-telephone position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="phone" placeholder="Số điện thoại">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password" placeholder="Mật khẩu">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password_confirm" placeholder="Nhập lại mật khẩu">
            </div>

            <div class="mb-3">
              <div class="function-btn text-end">
                <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Đã có tài
                  khoản</a>
              </div>
            </div>
            <div class="mb-3">
              <button type="button" class="btn btn-login form-control">Đăng ký</button>
            </div>
            <div class="divider d-flex align-items-center mb-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">hoặc</span>
              <hr class="flex-grow-1">
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button type="button" class="btn btn-social rounded-circle"><i class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social rounded-circle"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social rounded-circle"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <router-view></router-view>
</template>
