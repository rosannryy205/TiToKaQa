<template>
  <h3 class="title">Quản lý danh mục</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-food' }" class="btn btn-add">
      + Thêm món ăn
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

    <!-- Table Responsive -->
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" /></th>
                    <th>Món ăn</th>
                    <th>Danh mục</th>
                    <th>Giá thành</th>
                    <th>Số lượng</th>
                    <th>Tuỳ chọn</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" /></td>
                    <td>
                        <img src="/img/food/mykimchihaisan.webp" alt="Mỳ kim chi hải sản" class="me-2 img_thumbnail" />
                        Mỳ kim chi hải sản
                    </td>
                    <td>Mỳ cay</td>
                    <td>55,000 VNĐ</td>
                    <td>10</td>
                    <td class="d-flex gap-2">
                        <button type="button" class="btn-outline btn-primary">Sửa</button>
                        <button class="btn-outline btn-danger-delete">Xoá</button>
                        <button class="btn-outline btn-warning">Ẩn</button>
                        <button class="btn-outline btn-dark" data-bs-toggle="modal"
                            data-bs-target="#toppingModal">Toppings</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <button class="btn-outline btn-danger-delete delete_desktop">Xoá</button>

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
                        <h5 class="card-title">Mỳ kim chi hải sản</h5>
                        <p class="card-text"><strong>Danh mục:</strong> Mỳ cay</p>
                        <p class="card-text"><strong>Giá:</strong> 55,000 VNĐ</p>
                        <p class="card-text"><strong>Số lượng:</strong> 10</p>
                        <button class="btn-outline btn-primary btn-sm">Sửa</button>
                        <button class="btn-outline btn-danger-delete btn-sm">Xoá</button>
                        <button class="btn-outline btn-warning btn-sm">Ẩn</button><button class="btn btn-dark btn-sm"
                            data-bs-toggle="modal" data-bs-target="#toppingModal">Toppings</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="btn-outline btn-danger-delete delete_mobile">Xoá</button>


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
.img_thumbnail {
    width: 50px;
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
  .delete_mobile{
    display: block;
  }

}

</style>
