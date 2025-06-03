<template>
    <div class="container py-5">
      <h2 class="text-center text-danger fw-bold mb-4">ĐẶT BÀN CÙNG CHÚNG TÔI!</h2>
  
      <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
          <img
            src="/img/mo-hinh-lau-nuong-truyen-thong.png"
            alt="Ảnh nhà hàng"
            class="img-fluid rounded shadow-sm w-100"
            style="height: 100%; object-fit: cover"
          />
        </div>
        <div class="col-md-9">
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input type="date" class="form-control" value="2025-05-25" disabled />
            </div>
            <div class="col-md-3">
              <input type="time" class="form-control" value="18:30" disabled />
            </div>
            <div class="col-md-3">
              <input type="number" class="form-control" value="4" disabled />
            </div>
            <div class="col-md-3">
              <button class="btn btn-danger w-100 h-100" disabled>Tìm bàn</button>
            </div>
          </div>
  
          <h5 class="fw-bold mb-3">Kết quả tìm kiếm</h5>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-1">
          <div class="col">
            <div class="table-vertical">
              <div class="chair-group">
                <div class="chair"></div>
              </div>
              <div class="table-card-one">Bàn 1</div>
              <div class="chair-group">
                <div class="chair"></div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="table-vertical">
              <div class="chair-group">
                <div class="chair"></div>
                <div class="chair"></div>
              </div>
              <div class="table-card-two">Bàn 2</div>
              <div class="chair-group">
                <div class="chair"></div>
                <div class="chair"></div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="table-vertical">
              <div class="chair-group">
                <div class="chair"></div>
                <div class="chair"></div>
                <div class="chair"></div>
              </div>
              <div class="table-card-three">Bàn 3</div>
              <div class="chair-group">
                <div class="chair"></div>
                <div class="chair"></div>
                <div class="chair"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.table-vertical {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.table-card-one,
.table-card-two,
.table-card-three {
  border-radius: 10px;
  background-color: #d32f2f;
  color: white;
  font-weight: bold;
  text-align: center;
  padding: 12px 16px;
  cursor: default;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-card-one {
  min-width: 60px;
}

.table-card-two {
  min-width: 80px;
}

.table-card-three {
  min-width: 100px;
}

.table-card-one:hover,
.table-card-two:hover,
.table-card-three:hover {
  background-color: #b71c1c;
}

.chair-group {
  display: flex;
  justify-content: center;
  gap: 4px;
}

.chair {
  width: 16px;
  height: 16px;
  background-color: #555;
  border-radius: 3px;
}

</style>