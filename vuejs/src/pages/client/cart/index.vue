<template>
  <div class="container-sm">
    <span>Trang chủ / Giỏ hàng</span>
    <h3 class="mb-4">Giỏ hàng của bạn</h3>
    <div class="row">
      <!-- Danh sách sản phẩm -->
      <div class="col-12 col-lg-8 mb-4">
        <!-- Sản phẩm -->
        <div class="card mb-3" v-for="i in 1" :key="i">
          <div class="card-body d-flex align-items-center flex-wrap">
            <i class="bi bi-x-circle me-3 mb-2"></i>
            <img src="../../../../public/img/food/mykimchihaisan.webp" class="cart-img me-3 mb-2"
              alt="Mì kim chi Nha Trang" />
            <div class="flex-grow-1 mb-2">
              <h5 class="mb-1 product-title"><strong>Mì kim chi Nha Trang</strong></h5>
              <p class="text-muted mb-2">Tên biến thể</p>
              <p class="mb-0 "><strong>Giá:</strong> 46.000 VND</p>
            </div>
            <div class="text-center me-3 mb-2">
              <div class="qty-control border rounded px-2 py-1">
                <button class="btn btn-sm btn-outline-secondary">-</button>
                <span>1</span>
                <button class="btn btn-sm btn-outline-secondary">+</button>
              </div>
            </div>
            <div class="mb-2 price">
              <strong>46.000 VND</strong>
            </div>
          </div>
        </div>

      </div>

      <!-- Thông tin thanh toán -->
      <div class="col-12 col-lg-4">
        <div class="payment-box">
          <h5 class="mb-3">Thông tin thanh toán</h5>
          <div class="d-flex justify-content-between">
            <span>Giá sản phẩm</span>
            <strong>92.000 VND</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span>Vận chuyển</span>
            <span>Tính khi thanh toán</span>
          </div>
          <hr />
          <div class="d-flex justify-content-between">
            <span><strong>Tổng tiền thanh toán</strong></span>
            <strong>92.000 VND</strong>
          </div>
          <router-link to="/payment_if">
            <button class="btn btn-checkout w-100 mt-4">Thanh toán ngay</button>
          </router-link>

          <div class="mt-4 d-flex align-items-center flex-wrap">
            <i class="bi bi-telephone-fill me-2 fs-4"></i>
            <div>
              <small>Hotline hỗ trợ (8h – 22h)</small><br />
              <strong class="text-danger">09123456789</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-img {
  width: 100px;
  max-width: 100%;
  height: auto;
  border-radius: 10px;
}

.qty-control {
  min-width: 90px;
  width: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 5px;
  flex-wrap: nowrap;
}

.payment-box {
  background-color: #f0f0f0;
  padding: 20px;
  border-radius: 8px;
  position: sticky;
  top: 20px;
}

.btn-checkout {
  background-color: #b71c1c;
  color: white;
}

.btn-checkout:hover {
  background-color: #C92C3C;
}

.product-title {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Responsive tweaks */
@media (max-width: 576px) {

  .payment-box .d-flex {
    align-items: flex-start !important;
    gap: 8px;
  }

  .qty-control {
    flex-wrap: wrap;
    justify-content: center;
  }
}

@media (max-width: 492px) {
  .card-body {
    align-items: flex-start !important;
  }

  .card-body .text-center {
    margin-left: 300px;
  }
}

@media (max-width: 767px) {
  .price {
    display: none;
  }
}
</style>
