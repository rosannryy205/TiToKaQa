<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm Combo</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>
  <form class="row mt-2">
    <div class="col-md-8">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">


          <div class="row">
            <div class="col mb-3">
              <label for="name" class="form-label">Tên Combo <span class="text-danger">*</span></label>
              <input type="email" class="form-control rounded-0" id="name" required>
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label">Trạng thái <span class="text-danger">*</span></label>
              <div class="input-group">
                <select class="form-select rounded-0" id="category" required>
                  <option selected>Chọn trạng thái cho món ăn</option>
                  <option value="1">Ẩn</option>
                  <option value="2">Hiện</option>
                </select>

              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control rounded-0" id="description" rows="3"></textarea>
          </div>


          <div class="mb-3">
            <label for="description" class="form-label">Thêm món</label>
            <div class="table-responsive d-none d-lg-block">
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th><input type="checkbox" /></th>
                    <th>
                      Món ăn</th>
                    <th>Giá bán</th>
                    <th>Số lượng</th>
                    <th>Tuỳ chọn</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type="checkbox" /></td>
                    <td>
                      <img src="/img/food/mykimchihaisan.webp" alt="Mỳ kim chi hải sản" class="me-2 img_thumbnail" />

                      Combo 1
                    </td>
                    <td>25,000 VNĐ</td>
                    <td>
                      <div class="qty-control px-2 py-1">
                        <button type="button" class="btn-sm" style="background-color: #fff;">-</button>
                        <span>1</span>
                        <button type="button" class="btn-sm" style="background-color: #fff;">+</button>
                      </div>
                    </td>
                    <td class="d-flex justify-content-center gap-2">
                      <button class="btn btn-danger-delete">Xoá</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button class="btn btn-danger-delete" data-bs-toggle="modal" data-bs-target="#menuModal">Thêm món</button>

          </div>

        </div>
      </div>

    </div>
    <div class="col-md-4 mb-4">
      <div class="card rounded-0 border-0 shadow mb-2">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
              <label for="price" class="form-label">Giá bán<span class="text-danger">*</span></label>
              <input type="number" class="form-control rounded-0" id="price" min="0" required>
            </div>
            <div class="col mb-3">
              <label for="sale_price" class="form-label">Giá gốc</label>
              <div class="form-control rounded-0" style="height: 42px;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="card rounded-0 border-0 shadow">
        <div class="card-body">
          <div class="mb-3">
            <label for="image" class="form-label">Ảnh Combo <span class="text-danger">*</span></label>
            <input class="form-control rounded-0" type="file" id="image">
            <div class="mb-3 p-2 text-center">
              <img src="/img/food/cb1.webp" class="w-50">
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-danger-delete">
    + Thêm Combo
  </button>


  <!-- Modal chi tiết -->
  <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="menuModalLabel">Danh sách món</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Ô tìm kiếm & lọc danh mục -->
          <div class="d-flex mb-3">
            <input type="text" class="form-control me-2 rounded" placeholder="Nhập tên món..." id="searchInput" />
            <select class="form-select w-auto rounded" id="categoryFilter">
              <option value="">Tất cả</option>
              <option value="Khai vị">Khai vị</option>
              <option value="Món chính">Món chính</option>
              <option value="Tráng miệng">Tráng miệng</option>
              <option value="Đồ uống">Đồ uống</option>
            </select>
          </div>

          <!-- Danh sách món ăn -->
          <div class="pe-3">
            <table class="table">
              <thead>
                <tr>
                  <th>Chọn</th>
                  <th>Tên món</th>
                  <th>Giá</th>
                </tr>
              </thead>
              <tbody id="menuList">
                <tr data-category="Khai vị">
                  <td><input type="checkbox" class="menu-checkbox" data-price="60000" /></td>
                  <td>Salad rau củ</td>
                  <td>60.000 đ</td>
                </tr>
                <tr data-category="Món chính">
                  <td><input type="checkbox" class="menu-checkbox" data-price="380000" /></td>
                  <td>Lẩu cua đồng</td>
                  <td>380.000 đ</td>
                </tr>
                <tr data-category="Món chính">
                  <td><input type="checkbox" class="menu-checkbox" data-price="250000" /></td>
                  <td>Gà nướng mật ong</td>
                  <td>250.000 đ</td>
                </tr>
                <tr data-category="Tráng miệng">
                  <td><input type="checkbox" class="menu-checkbox" data-price="20000" /></td>
                  <td>Rau câu dừa</td>
                  <td>20.000 đ</td>
                </tr>
                <tr data-category="Đồ uống">
                  <td><input type="checkbox" class="menu-checkbox" data-price="40000" /></td>
                  <td>Trà sữa</td>
                  <td>40.000 đ</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end fw-bold">Tổng cộng:</td>
                  <td class="fw-bold" id="totalAmount">0 đ</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary">Lưu lại</button>
        </div>
      </div>
    </div>
  </div>
</template>
<style>
.themsp {
  width: 200px;
}

.img_thumbnail {
  width: 50px;
}

.btn-danger-delete {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-danger-delete:hover {
  background-color: #c92c3c;
  color: #fff;
}
.form-select:focus{
  border-color: #c92c3c;
box-shadow: none;
}
</style>
