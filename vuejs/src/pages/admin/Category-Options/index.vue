<template>
  <h2 class="mb-3">Quản lý danh mục toppings</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <button type="button" class="btn btn-danger1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
      +Thêm danh mục
    </button>
    <span class="vd">Tìm kiếm</span>
    <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm" />
  </div>


  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>STT</th>
          <th class="d-none d-md-table-cell">Tên</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><input type="checkbox" /></th>
          <td>1</td>
          <td>Cấp độ</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm Topping</button>
          </td>
        </tr>

        <tr>
          <td><input type="checkbox" /></td>
          <td>2</td>
          <td>Món thêm</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm Topping</button>

          </td>
        </tr>

        <tr>
          <td><input type="checkbox" /></td>
          <td>3</td>
          <td>Nước chấm</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm Topping</button>

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
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox">
          1
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Cấp độ</h5>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning">Thêm Topping</button>
          </div>
        </div>
      </div>


    </div>


    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox">
          2
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Cấp độ</h5>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning">Thêm Topping</button>

          </div>
        </div>
      </div>
    </div>

  </div>
  <button class="btn btn-danger-delete delete_mobile">Xoá</button>


  <!-- Modal Thêm Danh Mục -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thêm danh mục</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="toppingName" class="form-label">
            Tên danh mục <span class="text-danger">*</span>
          </label>
          <input type="text" class="form-control mb-3 rounded" placeholder="Nhập tên" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary">Thêm</button>
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
.delete_mobile {
  display: none;
}

.btn-danger-delete{
  background-color: #C92C3C;
  color: white;
}
.btn-danger-delete:hover{
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
