<template>
  <h2 class="mb-3">Quản lý người dùng</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
      +Thêm người dùng
    </button>
    <span class="vd">Tìm kiếm</span>
    <input type="text" class="form-control" style="max-width: 200px" placeholder="Tìm kiếm" />
    <span class="vd">Lọc</span>
    <select class="form-select w-auto" style="max-width: 250px">
      <option selected>Lọc theo vai trò</option>
      <option>Admin</option>
      <option>Nhân viên</option>
      <option>Khách hàng</option>
    </select>

    <span class="vd">Hiển thị</span>
    <select class="form-select w-auto">
      <option selected>5</option>
      <option>10</option>
      <option>15</option>
    </select>
  </div>

  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>Username</th>
          <th>Họ và tên</th>
          <th>Số điện thoại</th>
          <th>Email</th>
          <th>Địa chỉ</th>
          <th>Vai trò</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" /></td>
          <td>nguyenvanA</td>
          <td>Nguyễn Văn A</td>
          <td>0901234567</td>
          <td>nguyenvana@gmail.com</td>
          <td>Hồ Chí Minh</td>
          <td>
            <select class="form-select">
              <option>Admin</option>
              <option selected>Nhân viên</option>
              <option>Khách hàng</option>
            </select>
          </td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Khoá</button>
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
          <input type="checkbox" />
          1
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Nguyễn Văn A</h5>
            <p class="card-text"><strong>SĐT:</strong> 0901234567</p>
            <p class="card-text"><strong>Email:</strong> nguyenvana@gmail.com</p>
            <p class="card-text"><strong>Vai trò:</strong>
              <select class="form-select w-auto">
                <option>Admin</option>
                <option selected>Nhân viên</option>
                <option>Khách hàng</option>
              </select>
            </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning">Khoá</button>

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
