<template>
  <div class="main-content">
    <h2 class="mb-3">Quản lý danh mục</h2>
    <div class="mb-4 d-flex gap-3 flex-wrap">
      <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#addCategoryModal"
      >
        +Thêm danh mục
      </button>
      <input type="text" class="form-control w-auto" placeholder="Tìm kiếm danh mục" />
      <select class="form-select w-auto">
        <option selected>Lọc theo danh mục cha</option>
        <option>Danh mục 1</option>
        <option>Danh mục 2</option>
      </select>
    </div>

    <!-- Modal Thêm Danh Mục -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thêm danh mục</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="text" class="form-control mb-3" placeholder="Nhập tên danh mục" />
            <select class="form-select mb-3">
              <option selected>Chọn danh mục cha (không bắt buộc)</option>
              <option>Danh mục 1</option>
              <option>Danh mục 2</option>
            </select>
            <select class="form-select mb-3">
              <option>Không</option>
              <option>Có</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn themdm">Thêm</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal sửa Danh Mục -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thêm danh mục</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="text" class="form-control mb-3" placeholder="Nhập tên danh mục" />
            <select class="form-select mb-3">
              <option selected>Chọn danh mục cha (không bắt buộc)</option>
              <option>Danh mục 1</option>
              <option>Danh mục 2</option>
            </select>
            <select class="form-select mb-3">
              <option>Không</option>
              <option>Có</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn themdm">Sửa</button>
          </div>
        </div>
      </div>
    </div>



    <!-- Danh Mục -->
    <div class="row">
      <div class="col-md-4 col-sm-6 col-12">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Mỳ</h5>
            <p class="card-text fw-bold">Danh mục mặc định</p>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#editCategoryModal">Sửa</button>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6 col-12">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Mỳ cay</h5>
            <p class="card-text">Danh mục cha: <b>Mỳ</b></p>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger btn-sm">Xóa</button>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6 col-12">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Mỳ trộn</h5>
            <p class="card-text">Danh mục cha: <b>Mỳ</b></p>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger btn-sm">Xóa</button>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6 col-12">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Mỳ tương đen</h5>
            <p class="card-text">Danh mục cha: <b>Mỳ</b></p>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#editCategoryModal">Sửa</button>
            <button class="btn btn-danger btn-sm">Xóa</button>
          </div>
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
.themdm{
    background-color: #cf384c;
    color: white;
}
</style>
