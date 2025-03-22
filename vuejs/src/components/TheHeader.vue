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
          <img src="../../public/img/logonew.png" alt="Logo" class="logo">
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
            <button class="icon-btn me-3"><i class="bi bi-people"></i></button>
            <button class="icon-btn"><i class="bi bi-search"></i></button>
            <button class="icon-btn me-3"><i class="bi bi-telephone"></i></button>
            <button class="icon-btn"><i class="bi bi-cart"></i></button>
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
            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="email" placeholder="Tên đăng nhập">
            </div>

            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu">
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

  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Đăng ký</h5>
        </div>

        <div class="modal-body">
          <form>
            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="username" placeholder="Tên đăng nhập">
            </div>
            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
              <input type="text" class="form-control" id="email" placeholder="Email">
            </div>
            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
              <input type="text" class="form-control" id="phone" placeholder="Số điện thoại">
            </div>
            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="password" placeholder="Mật khẩu">
            </div>
            <div class="mb-3 input-group">
              <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
              <input type="password" class="form-control" id="password_confirm" placeholder="Nhập lại mật khẩu">
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
<style>
.modal-body .btn-login {
  background-color: #C92C3C;
  color: white;
  transition: background-color 0.3s ease;
}

.modal-body .btn-login:hover {
  background-color: #e04b5a;
}

.modal-header .modal-title {
    margin: auto;
    font-weight: 600;
}

.function-btn {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.function-btn a {
    text-decoration: none;@media (max-width: 576px) {
        .modal-body {
          padding: 1rem;
        }

        .modal-content {
          border-radius: 0.5rem;
        }

        .form-control {
          font-size: 14px;
          padding: 8px 10px;
        }

        .modal-header .modal-title {
          font-size: 18px;
        }

        .btn-login {
          font-size: 16px;
        }
      }
}


.btn-login {
  background-color: #C92C3C;
  color: #fff;
  border-radius: 30px;
  transition: background-color 0.3s ease;
}

.btn-login:hover {
  background-color: #e4505f;
}

.input-group-text {
  border-right: none;
  background-color: #f8f9fa;
}

.input-group .form-control {
  border-left: none;
}

.input-group:focus-within {
  box-shadow: 0 0 0 0.2rem rgba(201, 44, 60, 0.25);
}

.divider hr {
  border-top: 1px solid #ccc;
  margin: 0;
}

.btn-social {
  width: 40px;
  height: 40px;
  border: 1px solid #ccc;
  background-color: #fff;
  font-size: 18px;
  color: #333;
  transition: all 0.3s ease;

  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  /* Đảm bảo không bị đẩy icon */
}
.btn-social:hover {
  background-color: #f1f1f1;
  transform: scale(1.1);
}

</style>
