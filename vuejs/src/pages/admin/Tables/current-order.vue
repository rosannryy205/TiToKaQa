<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Đơn hiện tại</h2>
      <button class="btn btn-primary">+ Tạo đơn mới</button>
    </div>

    <div class="row mt-3 g-3">
      <!-- Đơn hàng -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="order-card">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="text-secondary">Sảnh phụ</h5>
            <span class="badge bg-secondary">#2</span>
          </div>
          <p>👥 2 người</p>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">🕒 08:05:10</p>
            <p class="fw-bold fs-4">2</p>
            <p class="fw-bold text-danger">2,000,000 đ</p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dropdown">
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                ...
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Xếp bàn</a></li>
                <li><a class="dropdown-item" href="#">Chọn món</a></li>
                <li><a class="dropdown-item" href="#">Chi tiết</a></li>
                <li><a class="dropdown-item text-danger" href="#">Hủy đơn</a></li>
              </ul>
            </div>
            <button class="btn btn-primary btn-sm">Thanh toán</button>
          </div>
        </div>
      </div>

      <!-- Nhân bản các đơn tiếp theo -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="order-card">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="text-secondary">Sảnh phụ</h5>
            <span class="badge bg-secondary">#3</span>
          </div>
          <p>👥 3 người</p>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">🕒 09:30:00</p>
            <p class="fw-bold fs-4">3</p>
            <p class="fw-bold text-danger">3,500,000 đ</p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dropdown">
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                ...
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Xếp bàn</a></li>
                <li><a class="dropdown-item" href="#">Chọn món</a></li>
                <li><a class="dropdown-item" href="#">Chi tiết</a></li>
                <li><a class="dropdown-item text-danger" href="#">Hủy đơn</a></li>
              </ul>
            </div>
            <button class="btn btn-primary btn-sm">Thanh toán</button>
          </div>
        </div>
      </div>


      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="order-card">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="text-secondary">Sảnh phụ</h5>
            <span class="badge bg-secondary">#3</span>
          </div>
          <p>👥 3 người</p>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">🕒 09:30:00</p>
            <p class="fw-bold fs-4">3</p>
            <p class="fw-bold text-danger">3,500,000 đ</p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dropdown">
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                ...
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Xếp bàn</a></li>
                <li><a class="dropdown-item" href="#">Chọn món</a></li>
                <li><a class="dropdown-item" href="#">Chi tiết</a></li>
                <li><a class="dropdown-item text-danger" href="#">Hủy đơn</a></li>
              </ul>
            </div>
            <button class="btn btn-primary btn-sm">Thanh toán</button>
          </div>
        </div>
      </div>



      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="order-card">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="text-secondary">Sảnh phụ</h5>
            <span class="badge bg-secondary">#3</span>
          </div>
          <p>👥 3 người</p>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">🕒 09:30:00</p>
            <p class="fw-bold fs-4">3</p>
            <p class="fw-bold text-danger">3,500,000 đ</p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dropdown">
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                ...
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Xếp bàn</a></li>
                <li><a class="dropdown-item" href="#">Chọn món</a></li>
                <li><a class="dropdown-item" href="#">Chi tiết</a></li>
                <li><a class="dropdown-item text-danger" href="#">Hủy đơn</a></li>
              </ul>
            </div>
            <button class="btn btn-primary btn-sm">Thanh toán</button>
          </div>
        </div>
      </div>


      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="order-card">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="text-secondary">Sảnh chính</h5>
            <span class="badge bg-secondary">#4</span>
          </div>
          <p>👥 4 người</p>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">🕒 10:15:00</p>
            <p class="fw-bold fs-4">4</p>
            <p class="fw-bold text-danger">4,000,000 đ</p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="dropdown">
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                ...
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Xếp bàn</a></li>
                <li><a class="dropdown-item" href="#">Chọn món</a></li>
                <li><router-link :to="{ name: 'admin-orders-detail' }" class="dropdown-item">Chi tiết</router-link></li>
                <li><a class="dropdown-item text-danger" href="#">Hủy đơn</a></li>
              </ul>
            </div>
            <button class="btn btn-primary btn-sm">Thanh toán</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
.order-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 15px;
  transition: transform 0.2s ease-in-out;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.order-card:hover {
  transform: scale(1.02);
}

.text-danger {
  color: rgb(207, 63, 63) !important;
}

/* Giới hạn dropdown */
.dropdown-menu {
  min-width: 150px;
}
</style>
