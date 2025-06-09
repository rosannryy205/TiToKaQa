<template>
  <h3 class="title">Quản lý toppings</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
      + Thêm toppings
    </button>

    <span class="vd">Tìm kiếm</span>
    <input
      type="text"
      class="custom-input"
      style="max-width: 200px"
      placeholder="Tìm kiếm"
    />

    <span class="vd">Lọc</span>
    <select class="custom-select" style="max-width: 250px">
      <option selected>Lọc theo danh mục</option>
      <option>Danh mục 1</option>
      <option>Danh mục 2</option>
    </select>

    <span class="vd">Hiển thị</span>
    <select class="custom-select" style="max-width: 80px">
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
          <th>Tên</th>
          <th>Danh mục</th>
          <th>Giá</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" /></td>
          <td>Gà</td>
          <td>Món thêm</td>
          <td>25,000 VNĐ</td>
          <td class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" /></td>
          <td>Cấp 1</td>
          <td>Cấp độ</td>
          <td>Miễn phí</td>
          <td class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
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
        <div class="col-3 d-flex align-items-center gap-2 p-2">
          <input type="checkbox" />
          <span>1</span>
        </div>
        <div class="col-9">
          <div class="card-body p-2">
            <h5 class="card-title mb-1">Cấp 1</h5>
            <p class="card-text mb-1"><span class="label">Danh mục:</span> Cấp độ</p>
            <p class="card-text mb-2"><span class="label">Giá:</span> Miễn phí</p>
            <button class="btn btn-outline btn-sm me-2">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 d-flex align-items-center gap-2 p-2">
          <input type="checkbox" />
          <span>2</span>
        </div>
        <div class="col-9">
          <div class="card-body p-2">
            <h5 class="card-title mb-1">Gà</h5>
            <p class="card-text mb-1"><span class="label">Danh mục:</span> Món thêm</p>
            <p class="card-text mb-2"><span class="label">Giá:</span> 25,000 VNĐ</p>
            <button class="btn btn-outline btn-sm me-2">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button class="btn btn-danger-delete delete_mobile">Xoá</button>

  <!-- Modal Thêm topping -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thêm Toppings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label for="toppingName" class="form-label label">Tên topping <span class="text-danger">*</span></label>
          <input
            type="text"
            id="toppingName"
            class="custom-input mb-3"
            placeholder="Nhập tên"
            required
          />

          <label for="category" class="form-label label">Danh mục <span class="text-danger">*</span></label>
          <select id="category" class="custom-select mb-3" required>
            <option selected disabled>Chọn danh mục</option>
            <option>Danh mục 1</option>
            <option>Danh mục 2</option>
          </select>

          <label for="price" class="form-label label">Giá</label>
          <input
            type="number"
            id="price"
            class="custom-input mb-3"
            placeholder="Nhập giá"
            required
          />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-add">Thêm</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useMenu } from '@/stores/use-menu';
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
export default {
  setup() {
    useMenu().onSelectedKeys(['admin-roles'])
  },
}
</script>

<style scoped>
.title {
  font-weight: normal;
  margin-bottom: 1rem;
  font-size: 1.5rem;
  color: #333;
}

.label {
  font-weight: normal;
  color: #555;
}

.custom-input,
.custom-select {
  border: 1px solid #bbb;
  padding: 2px 6px;
  height: 28px;
  font-size: 13px;
  border-radius: 4px;
  outline: none;
  box-shadow: none !important;
  transition: border-color 0.3s ease;
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

.btn-outline {
  background: none;
  border: 1px solid #ccc;
  padding: 4px 10px;
  border-radius: 4px;
  color: #555;
  font-weight: normal;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-outline:hover {
  background-color: #eee;
  color: #333;
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
    display: inline-block;
  }

  .custom-input,
  .custom-select {
    width: 100%;
    max-width: 100%;
    font-size: 14px;
    height: 32px;
  }
}
</style>
