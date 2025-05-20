<template>
  <h2 class="mb-3">Quản lý món ăn</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-food' }" class="btn btn-danger1">
      + Thêm món ăn
    </router-link>
    <span class="vd ">Tìm kiếm</span>
    <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm món ăn" />
    <span class="vd">Lọc</span>
    <select class="form-select w-auto rounded" style="max-width: 250px">
      <option selected>Lọc theo danh mục</option>
      <option>Danh mục 1</option>
      <option>Danh mục 2</option>
    </select>
  </div>

  <!-- Table Responsive -->
  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered rounded">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>Món ăn</th>
          <th>Danh mục</th>
          <th>Giá thành</th>
          <th>Tồn kho</th>
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
            <button type="button" class="btn btn-primary">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Ẩn</button>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#toppingModal">Toppings</button>
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
        <div class="col-3 d-flex p-1">
          <input type="checkbox" name="" id="">
          <img src="/img/food/mykimchihaisan.webp" alt="Mỳ kim chi hải sản" class="img-fluid rounded" />
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Mỳ kim chi hải sản</h5>
            <p class="card-text"><strong>Danh mục:</strong> Mỳ cay</p>
            <p class="card-text"><strong>Giá:</strong> 55,000 VNĐ</p>
            <p class="card-text"><strong>Tồn kho:</strong> 10</p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning btn-sm">Ẩn</button><button class="btn btn-dark btn-sm" data-bs-toggle="modal"
              data-bs-target="#toppingModal">Toppings</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <button class="btn btn-danger-delete delete_mobile">Xoá</button>



  <!-- Modal topping-->
  <div class="modal fade" id="toppingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Danh sách các lựa chọn</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <button type="button" class="btn btn-primary mb-2">
            +Thêm Topping
          </button>
          <div><strong>Cấp độ</strong></div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="level" id="level1">
            <label class="form-check-label" for="level1">Cấp 1</label><br>
            <input class="form-check-input" type="radio" name="level" id="level2">
            <label class="form-check-label" for="level2">Cấp 2</label><br>
            <input class="form-check-input" type="radio" name="level" id="level3">
            <label class="form-check-label" for="level3">Cấp 3</label><br>
            <input class="form-check-input" type="radio" name="level" id="level4">
            <label class="form-check-label" for="level4">Cấp 4</label><br>
            <input class="form-check-input" type="radio" name="level" id="level5">
            <label class="form-check-label" for="level5">Cấp 5</label>
          </div>

          <div><strong>Món thêm</strong></div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="topping2">
            <label class="form-check-label" for="topping2">Bò mỹ (+30.000đ)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="topping3">
            <label class="form-check-label" for="topping3">Kim chi (+10.000đ)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="topping4">
            <label class="form-check-label" for="topping4">Bắp cải tím (+12.000đ)</label>
          </div>
          <button class="btn btn-danger-delete mt-3">Xoá</button>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary">Lưu</button>
        </div>

      </div>
    </div>
  </div>

</template>

<style scoped>
.img_thumbnail {
  width: 50px;
}

.delete_mobile {
  display: none;
}

.btn-danger-delete {
  background-color: #C92C3C;
  color: white;
}

.btn-danger-delete:hover {
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
