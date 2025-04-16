<template>
  <div class="container">
    <div class="card p-3">
      <h5 class="border-bottom pb-2">Thông tin đặt bàn</h5>
      <div class="row">
        <div class="col-6">Ngày đặt bàn:</div>
        <div class="col-6 text-end">02/06/2023 - 23:26</div>
        <div class="col-6">Ngày dự kiến nhận bàn:</div>
        <div class="col-6 text-end">02/06/2023</div>
        <div class="col-6">Giờ nhận bàn dự kiến:</div>
        <div class="col-6 text-end">8 - 10 giờ</div>
        <div class="col-6">Số khách hàng:</div>
        <div class="col-6 text-end">3 người</div>
        <div class="col-6">Tham chiếu:</div>
        <div class="col-6 text-end"><a href="#">Đơn 123</a></div>
        <div class="col-6">Trạng thái đơn:</div>
        <div class="col-6 text-end"><span class="badge bg-success">Đã hoàn thành</span></div>
      </div>
    </div>

    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Bàn và khu vực</h5>
      <div class="row">
        <div class="col-6">Khu vực:</div>
        <div class="col-6 text-end">Sảnh chính</div>
        <div class="col-6">Bàn:</div>
        <div class="col-6 text-end">2</div>
        <div class="col-6">Số người:</div>
        <div class="col-6 text-end">Trung bình (Tối đa 5 người)</div>
      </div>
    </div>

    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Thông tin khách hàng</h5>
      <div class="row">
        <div class="col-6">Họ tên:</div>
        <div class="col-6 text-end">Nguyễn Thị Thuỷ Tiên</div>
        <div class="col-6">Số điện thoại:</div>
        <div class="col-6 text-end">0389330759</div>
        <div class="col-6">Email:</div>
        <div class="col-6 text-end"></div>
        <div class="col-6">Địa chỉ:</div>
        <div class="col-6 text-end"></div>
        <div class="col-6">Ngày đặt hàng:</div>
        <div class="col-6 text-end"></div>
      </div>
    </div>


        <!-- Chi tiết hóa đơn -->
        <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Chi tiết hóa đơn</h5>
      <table class="table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Mặt hàng</th>
            <th>Giá bán</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>
              <img src="https://via.placeholder.com/50" class="me-2" alt="img">
              Salad rau củ quả
            </td>
            <td>120.000 đ</td>
            <td>2</td>
            <td>240.000 đ</td>
          </tr>
          <tr>
            <td>2</td>
            <td>
              <img src="https://via.placeholder.com/50" class="me-2" alt="img">
              Lẩu cua đồng
            </td>
            <td>360.000 đ</td>
            <td>1</td>
            <td>360.000 đ</td>
          </tr>
        </tbody>
      </table>
      <div class="text-end">
        <p>Tạm tính: 600.000 đ</p>
        <p>Khuyến mãi: 0 đ</p>
        <h5>Tổng cộng (VAT): 600.000 đ</h5>
      </div>
    </div>

    <button class="btn btn-secondary mt-3" @click="goBack">Quay lại</button>
  </div>
</template>

<script>
export default {
  methods: {
    goBack() {
      window.history.back();
    }
  }
};
</script>

<style scoped>
.card {
  border-radius: 8px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

h5 {
  font-weight: bold;
}

.badge {
  font-size: 0.9rem;
}

a {
  text-decoration: none;
  color: #007bff;
}
</style>
