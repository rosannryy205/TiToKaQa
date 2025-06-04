<template>
  <h3 class="title">Quản lý danh mục</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-category' }" class="btn btn-add">
      + Thêm danh mục
    </router-link>
    <span class="vd">Tìm kiếm</span>
    <input type="text" class="custom-input" placeholder="Tìm kiếm danh mục" />
    <span class="vd">Lọc</span>
    <select class="custom-select">
      <option selected>Lọc theo danh mục cha</option>
      <option>Danh mục 1</option>
      <option>Danh mục 2</option>
    </select>
  </div>

  <!-- Table Desktop -->
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
          <td><input type="checkbox" /></td>
          <td>1</td>
          <td>Chưa phân loại</td>
          <td>Danh mục mặc định</td>
          <td class="d-flex justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline">Sửa</button>
            <button class="btn btn-outline">Thêm sản phẩm</button>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" /></td>
          <td>2</td>
          <td>Mỳ</td>
          <td></td>
          <td class="d-flex justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-outline">Thêm sản phẩm</button>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" /></td>
          <td>3</td>
          <td>Mỳ trộn</td>
          <td>Mỳ</td>
          <td class="d-flex justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-outline">Thêm sản phẩm</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <button class="btn btn-danger-delete delete_desktop">Xoá</button>

  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3" v-for="(item, index) in [1,2,3]" :key="index">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox" />
          {{ index + 1 }}
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Chưa phân loại</h5>
            <p class="card-text"><span class="label">Danh mục cha:</span> </p>
            <button class="btn btn-outline btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning btn-sm">Thêm sản phẩm</button>
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