<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm món ăn</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>
  <form class="row mt-2">
    <div class="col-md-8">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">


          <div class="row">
            <div class="col mb-3">
              <label for="name" class="form-label">Tên món <span class="text-danger">*</span></label>
              <input type="email" class="form-control rounded-0" id="name" required>
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label">Trạng thái <span class="text-danger">*</span></label>
              <div class="input-group">
                <select class="form-select rounded-0" id="category" required>
                  <option selected>Chọn trạng thái cho món ăn</option>
                  <option value="1">Ẩn</option>
                  <option value="2">Hiện</option>
                </select>

              </div>
            </div>
          </div>

          <div class="row">
            <div class="col mb-3">
              <label for="instock" class="form-label">Tồn kho <span class="text-danger">*</span></label>
              <input type="number" class="form-control rounded-0" id="instock" min="0" required>
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
              <div class="input-group">
                <select class="form-select rounded-0" id="category" required>
                  <option selected>Chọn danh mục</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>

              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control rounded-0" id="description" rows="6"></textarea>
          </div>

        </div>
      </div>

    </div>
    <div class="col-md-4 mb-4">
      <div class="card rounded-0 border-0 shadow mb-2">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
              <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
              <input type="number" class="form-control rounded-0" id="price" min="0" required>
            </div>
            <div class="col mb-3">
              <label for="sale_price" class="form-label">Giá giảm</label>
              <input type="number" class="form-control rounded-0" id="sale_price" min="0">
            </div>
          </div>
        </div>
      </div>
      <div class="card rounded-0 border-0 shadow">
        <div class="card-body">
          <div class="mb-3">
            <label for="image" class="form-label">Ảnh món ăn <span class="text-danger">*</span></label>
            <input class="form-control rounded-0" type="file" id="image">
            <div class="mb-3 p-2 text-center">
              <img src="/img/food/cb1.webp" class="w-50">
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-danger1 themsp">
    + Thêm
  </button>
</template>
<style>
.themsp {
  width: 200px;
}
</style>
