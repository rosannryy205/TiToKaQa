<template>
  <h2 class="mb-3">Quản lý combo</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-combo' }" class="btn btn-danger1">
      + Thêm Combo
    </router-link>
    <span class="vd">Tìm kiếm</span>
    <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm" />
    <span class="vd">Lọc</span>




  </div>


  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>

            Tên combo</th>
          <th>Giá bán</th>
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
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#menuModal">Chi tiết</button>

          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <button class="btn btn-danger-delete delete_desktop">Xoá</button>


  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 d-flex p-1">
          <input type="checkbox" name="" id="">
          <img src="/img/food/mykimchihaisan.webp" alt="Mỳ kim chi hải sản" class="img-fluid rounded" />
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Combo 1</h5>
            <p class="card-text"><strong>Giá bán:</strong> 150.000đ </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#menuModal">Chi tiết</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <button class="btn btn-danger-delete delete_mobile">Xoá</button>



  <!-- Modal chi tiết -->
  <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="menuModalLabel">Danh sách món</h5>
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

          <!-- Danh sách món ăn -->
          <div class="pe-3">
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
                  <td><input type="checkbox" class="menu-checkbox" data-price="60000" /></td>
                  <td>Salad rau củ</td>
                  <td><button class="btn btn-sm btn-primary">Topping</button></td>
                  <td>60.000 đ</td>
                </tr>
                <tr data-category="Món chính">
                  <td><input type="checkbox" class="menu-checkbox" data-price="380000" /></td>
                  <td>Lẩu cua đồng</td>
                  <td><button class="btn btn-sm btn-primary">Topping</button></td>
                  <td>380.000 đ</td>
                </tr>
                <tr data-category="Món chính">
                  <td><input type="checkbox" class="menu-checkbox" data-price="250000" /></td>
                  <td>Gà nướng mật ong</td>
                  <td><button class="btn btn-sm btn-primary">Topping</button></td>
                  <td>250.000 đ</td>
                </tr>
                <tr data-category="Tráng miệng">
                  <td><input type="checkbox" class="menu-checkbox" data-price="20000" /></td>
                  <td>Rau câu dừa</td>
                  <td><button class="btn btn-sm btn-primary">Topping</button></td>
                  <td>20.000 đ</td>
                </tr>
                <tr data-category="Đồ uống">
                  <td><input type="checkbox" class="menu-checkbox" data-price="40000" /></td>
                  <td>Trà sữa</td>
                  <td><button class="btn btn-sm btn-primary">Topping</button></td>
                  <td>40.000 đ</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                  <td class="fw-bold" id="totalAmount">0 đ</td>
                </tr>
              </tfoot>
            </table>
          </div>
          <button class="btn btn-danger-delete btn-sm" id="deleteSelected">Xoá</button>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary">Lưu lại</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useMenu } from '@/stores/use-menu'
export default {
  setup() {
    useMenu().onSelectedKeys(['admin-roles'])
  },
}
</script>

<style scoped>
.img_thumbnail {
  width: 50px;
}

.delete_mobile {
  display: none;
}

.btn-danger-delete {
  background-color: #C92C3C;
  color: white;
}

.btn-danger-delete:hover {
  background-color: #a51928;
  color: white;
}

@media (max-width: 768px) {
  .table-responsive {
    display: none;
  }

  .vd {
    display: none;
  }

  .delete_desktop {
    display: none;
  }

  .delete_mobile {
    display: block;
  }

}
</style>
