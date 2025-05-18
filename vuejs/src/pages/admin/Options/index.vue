<template>
    <h2 class="mb-3">Quản lý toppings</h2>

    <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
        <button type="button" class="btn btn-danger1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            +Thêm toppings
        </button>
        <span class="vd">Tìm kiếm</span>
        <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm" />
        <span class="vd">Lọc</span>
        <select class="form-select w-auto rounded" style="max-width: 250px">
            <option selected>Lọc theo danh mục</option>
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
        <table class="table table-bordered">
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
                    <td class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary">Sửa</button>
                        <button class="btn btn-danger-delete
                        ">Xoá</button>
                    </td>
                </tr>

                <tr>
                    <td><input type="checkbox" /></td>
                    <td>Cấp 1</td>
                    <td>Cấp độ</td>
                    <td>Miễn phí</td>
                    <td class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary">Sửa</button>
                        <button class="btn btn-danger-delete
                        ">Xoá</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <button class="btn btn-danger-delete
     delete_desktop">Xoá</button>


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
            <h5 class="card-title">Cấp 1</h5>
            <p class="card-text"><strong>Danh mục:</strong> Cấp độ </p>
            <p class="card-text"><strong>Giá:</strong>  </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete
             btn-sm">Xoá</button>
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
            <h5 class="card-title">Gà</h5>
            <p class="card-text"><strong>Danh mục:</strong> Món thêm </p>
            <p class="card-text"><strong>Giá:</strong> 25.000 VNĐ </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete
             btn-sm">Xoá</button>
          </div>
        </div>
      </div>


    </div>


  </div>
  <button class="btn btn-danger-delete
   delete_mobile">Xoá</button>



    <!-- Modal Thêm topping -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Toppings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Tên topping -->
                    <label for="toppingName" class="form-label">
                        Tên topping <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="toppingName" class="form-control mb-3" placeholder="Nhập tên" required />

                    <!-- Danh mục -->
                    <label for="category" class="form-label">
                        Danh mục <span class="text-danger">*</span>
                    </label>
                    <select id="category" class="form-select mb-3" required>
                        <option selected disabled>Chọn danh mục</option>
                        <option>Danh mục 1</option>
                        <option>Danh mục 2</option>
                    </select>

                    <!-- Giá -->
                    <label for="price" class="form-label">
                        Giá
                    </label>
                    <input type="number" id="price" class="form-control mb-3" placeholder="Nhập giá" required />
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
.delete_mobile{
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
  .delete_mobile{
    display: block;
  }
}

</style>
