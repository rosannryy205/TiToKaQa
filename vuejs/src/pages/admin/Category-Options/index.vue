<template>
  <div>
    <h3 class="title">Quản lý danh mục toppings</h3>

    <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
      <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        +Thêm danh mục
      </button>
      <span class="vd">Tìm kiếm</span>
      <input type="text" class="custom-input" placeholder="Tìm kiếm danh mục" />
      <span class="vd">Lọc</span>
      <select class="custom-select">
        <option selected>Lọc theo danh mục cha</option>
        <option>Danh mục 1</option>
        <option>Danh mục 2</option>
      </select>
    </div>

    <!-- Desktop Table -->
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
          <tr v-for="(item, index) in [1, 2, 3]" :key="index">
            <td><input type="checkbox" /></td>
            <td>{{ index + 1 }}</td>
            <td>Danh mục {{ index + 1 }}</td>
            <td class="d-flex justify-content-center gap-2">
              <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#editCategoryModal">Sửa</button>
              <button class="btn btn-danger-delete btn-sm">Xoá</button>
              <button class="btn btn-outline">Thêm Topping</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <button class="btn btn-danger-filled delete_desktop mt-2">Xoá</button>

    <!-- Mobile View -->
    <div class="d-block d-lg-none">
      <div class="card mb-3" v-for="(item, index) in [1, 2]" :key="index">
        <div class="row g-0 align-items-center">
          <div class="col-3 fs-4 fw-bold ps-4">
            <input type="checkbox" />
            {{ index + 1 }}
          </div>
          <div class="col-9">
            <div class="card-body">
              <h5 class="card-title">Danh mục {{ index + 1 }}</h5>
              <button class="btn btn-outline btn-sm">Sửa</button>
              <button class="btn btn-danger-delete btn-sm">Xoá</button>
              <button class="btn btn-outline btn-sm">Thêm Topping</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <button class="btn btn-danger-filled delete_mobile">Xoá</button>

    <!-- Modal Thêm Danh Mục -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thêm danh mục</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" class="form-control mb-3 rounded" placeholder="Nhập tên" />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary">Thêm</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Sửa Danh Mục -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Sửa danh mục</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Tên danh mục</label>
            <input type="text" class="form-control mb-3 rounded" placeholder="Nhập tên mới" />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
            <button type="button" class="btn btn-success">Lưu</button>
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
.title {
  font-weight: normal;
  margin-bottom: 1rem;
  font-size: 1.5rem;
  color: #333;
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

/* Buttons */
.btn-outline {
  background: none;
  border: 1px solid #ccc;
  padding: 4px 10px;
  border-radius: 4px;
  color: #555;
  font-weight: normal;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-outline:hover {
  background-color: #eee;
  color: #333;
}

.btn-add {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: all 0.3s ease;
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

.btn-danger-filled {
  background-color: #c92c3c;
  color: white;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-danger-filled:hover {
  background-color: #a51928;
}

/* Responsive */
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
    margin-top: 1rem;
  }
}
</style>
