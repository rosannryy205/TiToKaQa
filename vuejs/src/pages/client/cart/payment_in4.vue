<template>
  <div class="container-sm">
    <div class="row custom-row">
      <div class="col-7 col-in4-customer">
        <div class="box-in4-customer">
          <div class="title-in4">
            <h3>Thông tin đặt hàng</h3>
          </div>
          <div class="body-in4-customer">
            <form action="">
              <div class="input-in4-customer">
                <input type="text" placeholder="Tên của bạn">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Số điện thoại">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Địa chỉ">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Tỉnh thành">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Quận huyện">
              </div>
              <div class="input-in4-customer">
                <input type="text" placeholder="Phường xã">
              </div>
              <div class="input-in4-customer">
                <textarea name="" id="" placeholder="Ghi chú"></textarea>
              </div>
              <div class="btn-complete">
                <span><i class="bi bi-chevron-left"></i>Quay về trang giỏ hàng</span>
                <button class="btn btn-complete-order">Đặt hàng</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div style="background-color: #EABEB8;" class="col col-in4-payment">
        <div class="box-in4-payment">
          <div class="title-in4">
            <h3>Đơn hàng (2 sản phẩm)</h3>
          </div>
          <hr>
          <div class="list-product">
            <div class="item">
              <div class="left-item">
                <img src="../../../../public/img/food/mykimchihaisan.webp" height="100" width="100" alt="">
                <div class="in4-item">
                  <span>Mì kim chi chả cá Nha Trang</span>
                  <span>Tên biến thể</span>
                </div>
              </div>
              <div class="price-item">
                46.000 VND
              </div>
            </div>
            <div class="item">
              <div class="left-item">
                <img src="../../../../public/img/food/mykimchihaisan.webp" height="100" width="100" alt="">
                <div class="in4-item">
                  <span>Mì kim chi chả cá Nha Trang</span>
                  <span>Tên biến thể</span>
                </div>
              </div>
              <div class="price-item">
                46.000 VND
              </div>
            </div>
          </div>
          <hr>
          <div class="in4-ship">
            <div class="content-ship">
              <span>Tạm tính</span>
              <span><strong>92.000 VND</strong></span>
            </div>
            <div class="content-ship">
              <span>Giảm giá</span>
              <span><strong>0 VND</strong></span>
            </div>
            <div class="content-ship">
              <span>Phí vận chuyển</span>
              <span><strong>0 VND</strong></span>
            </div>
            <hr>
            <div class="content-ship">
              <span>Tổng cộng</span>
              <span><strong>92.000 VND</strong></span>
            </div>
          </div>
          <div class="discount-code">
            <label for="discount">Mã giảm giá:</label>
            <div class="input-group">
              <input type="text" id="discount" placeholder="Nhập mã giảm giá...">
              <button>Áp dụng</button>
            </div>
          </div>
          <hr>
          <div class="title-in4">
            <h5>Phương thức thanh toán</h5>
          </div>
          <div class="payment-method">
            <div class="content"><span class="left-content"><input type="radio"> Thanh toán qua VNPAY-QR</span><img
                src="../../../../public/img/Logo-VNPAY-QR-1 (1).png" height="20px" width="60px" alt=""></div>
            <div class="content"><span class="left-content"><input type="radio" checked> Thanh toán khi nhận hàng
                (COD)</span><img src="../../../../public/img/cod.png" height="30px" width="30px" alt=""></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style>

</style>
