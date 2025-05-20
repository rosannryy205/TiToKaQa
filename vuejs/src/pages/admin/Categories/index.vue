<template>
  <h2 class="mb-3">Quản lý danh mục</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-category' }" class="btn btn-danger1">
      + Thêm danh mục
    </router-link>
    <span class="vd">Tìm kiếm</span>
    <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm" />
    <span class="vd">Lọc</span>
    <select class="form-select w-auto rounded" style="max-width: 250px">
      <option selected>Lọc theo danh mục cha</option>
      <option>Danh mục 1</option>
      <option>Danh mục 2</option>
    </select>


    <span class="vd">Hiển thị</span>
    <select class="form-select w-auto rounded">
      <option selected>5</option>
      <option>10</option>
      <option>15</option>
    </select>
  </div>


  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered rounded">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>STT</th>
          <th class="d-none d-md-table-cell">Tên</th>
          <th>Danh mục cha</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td>1</td>
          <td>Chưa phân loại</td>
          <td>Danh mục mặc định</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>

          </td>
        </tr>

        <tr>
          <td><input type="checkbox" /></td>
          <td>2</td>
          <td>Mỳ</td>
          <td></td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>

          </td>
        </tr>

        <tr>
          <td><input type="checkbox" /></td>
          <td>3</td>
          <td>Mỳ trộn</td>
          <td>Mỳ</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>

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
            <h5 class="card-title">Chưa phân loại</h5>
            <p class="card-text"><strong>Danh mục cha:</strong> </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>

          </div>
        </div>
      </div>


    </div>

    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox">
          1
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Chưa phân loại</h5>
            <p class="card-text"><strong>Danh mục cha:</strong> </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>

          </div>
        </div>
      </div>


    </div>


    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox">
          1
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Chưa phân loại</h5>
            <p class="card-text"><strong>Danh mục cha:</strong> </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>

          </div>
        </div>
      </div>


    </div>

  </div>
  <button class="btn btn-danger-delete delete_mobile">Xoá</button>



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
.btn-danger-delete {
  background-color: #C92C3C;
  color: white;
}

.btn-danger-delete:hover {
  background-color: #a51928;
  color: white;
}

.delete_mobile {
  display: none;
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
