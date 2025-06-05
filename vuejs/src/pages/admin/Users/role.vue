<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h5>Danh sách vai trò</h5>
      <button class="btn btn-add btn-sm">
        <i class="fas fa-plus"></i> Thêm vai trò
      </button>
    </div>

    <div class="filter-row">
      <label class="vd" for="search">Tìm kiếm</label>
      <input id="search" type="text" class="custom-input" placeholder="Tìm kiếm danh mục" />

      <label class="vd" for="filter">Lọc</label>
      <select id="filter" class="custom-select">
        <option selected>Lọc theo danh mục cha</option>
        <option>Danh mục 1</option>
        <option>Danh mục 2</option>
      </select>
    </div>

    <ul class="list-group list-group-flush mt-3">
      <li class="list-group-item py-2"><a href="#">Quản lý</a></li>
      <li class="list-group-item py-2"><a href="#">Nhân viên</a></li>
    </ul>
  </div>
</template>

<style>
.filter-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap; /* responsive khi nhỏ màn hình */
  margin-bottom: 12px;
}

.filter-row .vd {
  white-space: nowrap;
  font-weight: 500;
  color: #333;
}

/* Đặt input và select bằng nhau, cùng chiều cao */
.custom-input,
.custom-select {
  flex: 1 1 200px; /* tự co giãn, tối thiểu 200px */
  border: 1px solid #bbb;
  padding: 6px 10px; /* tăng padding cho đẹp */
  height: 34px;
  font-size: 13px;
  border-radius: 4px;
  outline: none;
  box-shadow: none !important;
  transition: border-color 0.3s ease;
  box-sizing: border-box;
}

.custom-input:focus,
.custom-select:focus {
  border-color: #999;
  box-shadow: none;
}

.btn-add {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-add:hover {
  background-color: #c92c3c;
  color: #fff;
}
</style>
