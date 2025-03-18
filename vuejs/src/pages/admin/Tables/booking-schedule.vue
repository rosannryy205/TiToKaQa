<template>
  <div class="d-flex justify-content-between mb-3">
    <h2>Lịch đặt bàn</h2>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
    + Thêm đơn đặt bàn
  </button>
  </div>

  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-sm-6 col-md-4" v-for="n in 4" :key="n">
        <div class="card custom-card shadow-sm">
          <div class="card-body p-3">
            <h6 class="card-title mb-2 text-truncate">Nguyễn Thị Thuỷ Tiên - 038999299</h6>
            <p class="card-text small text-muted mb-1">
              <i class="fa fa-calendar"></i>17/03/2025 | <i class="bi bi-clock"></i> 8h - 10h |
              <i class="bi bi-people"></i> 3 | <i class="fa-solid fa-table"></i> 1
            </p>
            <p class="card-text small text-muted mb-1">Khu vực: Ngoài trời</p>
            <p class="fw-bold text-danger mb-2">425.000 đ</p>
            <div class="d-flex justify-content-between align-items-center">
              <button class="btn btn-primary btn-sm">Khách nhận bàn</button>
              <div class="dropdown">
                <button class="btn btn-light btn-sm border" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#tableModal" href="#">Xếp bàn</a>
                  </li>
                  <li>
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#menuModal" href="#">Chọn món</a>
                  </li>
                  <li><router-link :to="{ name: 'admin-orders-detail' }" class="dropdown-item">Chi tiết</router-link></li>
                  <li><a class="dropdown-item text-danger" href="#">Hủy đơn</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal xếp bàn -->
    <div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tableModalLabel">Sơ Đồ Bàn</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Tabs bộ lọc trạng thái -->
            <ul class="nav nav-tabs mb-3">
              <li class="nav-item">
                <a class="nav-link active">Tất cả</a>
              </li>
              <li class="nav-item">
                <a class="nav-link">Bàn trống</a>
              </li>
              <li class="nav-item">
                <a class="nav-link">Có khách</a>
              </li>
            </ul>

            <!-- Bộ lọc khu vực -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <label class="me-2">Khu vực:</label>
                <select class="form-select w-auto d-inline-block">
                  <option>Tất cả</option>
                  <option>Ngoài trời</option>
                  <option>Trong phòng</option>
                </select>
              </div>
            </div>

            <!-- Danh sách bàn -->
            <div class="row row-cols-2 row-cols-md-4 g-4">
              <div class="col" v-for="n in 6" :key="n">
                <div class="table-card text-center p-3">
                  <div class="table-number">{{ n }}</div>
                  <p class="status">Bàn trống - 2 người</p>
                  <hr />
                  <p class="area">Ngoài trời</p>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary">Lưu</button>
          </div>
        </div>
      </div>
    </div>

    <!-- modal chọn món -->
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="menuModalLabel">Chọn món</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <!-- Ô tìm kiếm & lọc danh mục -->
            <div class="d-flex mb-3">
              <input type="text" class="form-control me-2" placeholder="Nhập tên món..." id="searchInput" />
              <select class="form-select w-auto" id="categoryFilter">
                <option value="">Tất cả</option>
                <option value="Khai vị">Khai vị</option>
                <option value="Món chính">Món chính</option>
                <option value="Tráng miệng">Tráng miệng</option>
                <option value="Đồ uống">Đồ uống</option>
              </select>
            </div>

            <div class="d-flex">
              <!-- Danh sách món ăn -->
              <div class="w-50 pe-3">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Chọn</th>
                      <th>Tên món</th>
                      <th>Topping</th>
                      <th>Giá</th>
                    </tr>
                  </thead>
                  <tbody id="menuList">
                    <tr data-category="Khai vị">
                      <td><input type="checkbox" /></td>
                      <td>Salad rau củ</td>
                      <td><button class="btn btn-sm btn-primary">Topping</button></td>
                      <td>60.000 đ</td>
                    </tr>
                    <tr data-category="Món chính">
                      <td><input type="checkbox" /></td>
                      <td>Lẩu cua đồng</td>
                      <td><button class="btn btn-sm btn-primary">Topping</button></td>
                      <td>380.000 đ</td>
                    </tr>
                    <tr data-category="Món chính">
                      <td><input type="checkbox" /></td>
                      <td>Gà nướng mật ong</td>
                      <td><button class="btn btn-sm btn-primary">Topping</button></td>
                      <td>250.000 đ</td>
                    </tr>
                    <tr data-category="Tráng miệng">
                      <td><input type="checkbox" /></td>
                      <td>Rau câu dừa</td>
                      <td><button class="btn btn-sm btn-primary">Topping</button></td>
                      <td>20.000 đ</td>
                    </tr>
                    <tr data-category="Đồ uống">
                      <td><input type="checkbox" /></td>
                      <td>Trà sữa</td>
                      <td><button class="btn btn-sm btn-primary">Topping</button></td>
                      <td>40.000 đ</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Giỏ hàng -->
              <div class="w-50 ps-3 border-start">
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th>Món ăn</th>
                      <th class="text-center">SL</th>
                      <th class="text-end">Thành tiền</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Salad rau củ</td>
                      <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <button class="btn btn-sm btn-outline-secondary">-</button>
                          <span class="mx-2">2</span>
                          <button class="btn btn-sm btn-outline-secondary">+</button>
                        </div>
                      </td>
                      <td class="text-end">120.000 đ</td>
                      <td><button class="btn btn-sm btn-danger">X</button></td>
                    </tr>
                    <tr>
                      <td>Lẩu cua đồng</td>
                      <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <button class="btn btn-sm btn-outline-secondary">-</button>
                          <span class="mx-2">1</span>
                          <button class="btn btn-sm btn-outline-secondary">+</button>
                        </div>
                      </td>
                      <td class="text-end">380.000 đ</td>
                      <td><button class="btn btn-sm btn-danger">X</button></td>
                    </tr>
                  </tbody>
                </table>
                <h4 class="text-end">
                  Tổng tiền: <span class="fw-bold">500.000 đ</span>
                </h4>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Đóng
            </button>
            <button type="button" class="btn btn-primary">Lưu lại</button>
            <button class="btn btn-success">Thanh toán</button>
          </div>
        </div>
      </div>
    </div>




  </div>
</template>

<style scoped>
.left-side {
  width: 50%;
  float: left;
}

.right-side {
  width: 45%;
  float: right;
}

table {
  width: 100%;
}

table th,
table td {
  text-align: left;
  padding: 8px;
}

table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.custom-card {
  overflow: visible !important;
}

.card-title {
  font-weight: bold;
}

.table-card {
  background: #f8f9fa;
  border-radius: 10px;
  border: 1px solid #dee2e6;
  padding: 15px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.table-card:hover {
  transform: scale(1.05);
}

.table-number {
  width: 40px;
  height: 40px;
  background: #d12d43;
  color: white;
  font-weight: bold;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.status,
.area {
  margin-top: 10px;
  color: #6c757d;
}

.nav-tabs .nav-link {
  cursor: pointer;
}

.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.table td,
.table th {
  vertical-align: middle;
}

.btn-sm {
  padding: 4px 8px;
  font-size: 14px;
}

.btn-danger {
  padding: 4px 8px;
}

.d-flex .w-50 {
  flex: 1;
}
</style>
