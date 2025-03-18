<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h5>Danh sách vai trò</h5>
      <button class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Thêm vai trò
      </button>
    </div>

    <div class="d-flex gap-2 mb-2">
      <select class="form-select form-select-sm w-auto">
        <option>Hiển thị 10</option>
        <option>Hiển thị 20</option>
        <option>Hiển thị 50</option>
      </select>
      <input type="text" class="form-control form-control-sm w-25" placeholder="Tìm kiếm...">
    </div>

    <ul class="list-group list-group-flush">
      <li class="list-group-item py-2"><a href="#">Quản lý</a></li>
      <li class="list-group-item py-2"><a href="#">Nhân viên</a></li>
    </ul>
  </div>
</template>
