<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm danh mục</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>

  <form class="row mt-2">
    <div class="col-12 col-md-6">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">
          <div class="row">
            <div class="mb-3">
              <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
              <input type="email" class="form-control rounded-0" id="name" required>
            </div>
            <div class="mb-3">
              <label for="category" class="form-label">Danh mục cha <span class="text-danger">*</span></label>
              <div class="input-group">
                <select class="form-select rounded-0" id="category" required>
                  <option selected>Chọn danh mục</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
            </div>
            </div>
            <div class="mb-3">
              <label for="category" class="form-label">Làm danh mục mặc định <span class="text-danger">*</span></label>
              <div class="input-group">
                <select class="form-select rounded-0" id="category" required>
                  <option selected>Có</option>
                  <option value="1">Không</option>
                </select>
            </div>
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
