<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Tổng quan</h1>
    <button @click="handleLogout" class="btn btn-dark" href="">
      <i class="bi bi-box-arrow-right"></i> Logout
    </button>
    <a class="btn btn-dark" href="/home">
      <i class="bi bi-box-arrow-right"></i> Chuyển lại web
    </a>
  </div>
<!--
  <form class="row g-3 align-items-end mb-3">
    <div class="col-md-3">
      <label for="date_from" class="form-label">Từ ngày:</label>
      <input type="date" id="date_from" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="date_to" class="form-label">Đến ngày:</label>
      <input type="date" id="date_to" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="group_by" class="form-label">Thống kê theo:</label>
      <select id="group_by" class="form-select">
        <option value="day">Ngày</option>
        <option value="month">Tháng</option>
        <option value="year">Năm</option>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-50 loc">Lọc</button>
    </div>
  </form>

  <div class="row">
    <div class="col-lg-6 col-md-12 col-12">
      <canvas id="barChart"></canvas>
    </div>
    <div class="col-lg-6 col-md-12 col-12">
      <canvas id="pieChart"></canvas>
    </div>
  </div>

  <hr>

  <div class="row thongke">
    <div class="col-lg-6 col-md-12 col-12">
      <div class="card shadow-sm">
        <div class="card-header">Top 5 sản phẩm bán chạy</div>
        <div class="card-body table-wrapper-scroll-y my-custom-scrollbar">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Số lượng bán</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(product, index) in topSellingProducts" :key="index">
                <td>{{ product.id }}</td>
                <td>{{ product.name }}</td>
                <td class="text-center">{{ product.total_sold }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-12 col-12">
      <div class="card shadow-sm">
        <div class="card-header">Số lượng đơn hàng theo trạng thái</div>
        <div class="card-body table-wrapper-scroll-y my-custom-scrollbar">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>STT</th>
                <th>Trạng thái</th>
                <th>Tổng</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(status, index) in orderStatusStats" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ status.status }}</td>
                <td>{{ status.total }}</td>
              </tr>
            </tbody>
          </table>
</div>
      </div>
    </div>
  </div> -->
</template>

<script>
import axios from "axios";
import { Chart, registerables } from "chart.js";
import { ref } from "vue";

Chart.register(...registerables);

export default {
  setup() {





    // Xóa user và token khỏi localStorage
    const user = ref(JSON.parse(localStorage.getItem("user")));
    const isLoggedIn = ref(!!localStorage.getItem("token"));
    const handleLogout = async () => {


      const confirmLogout = confirm('Bạn chắc chắn muốn đăng xuất?');
      if (!confirmLogout) {
        return;
      }
      try {
        await axios.post('http://127.0.0.1:8000/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });

        localStorage.removeItem('user');
        localStorage.removeItem('token');
        user.value = null;
        isLoggedIn.value = false;

        alert('Đăng xuất thành công');
        window.location.href = '/home';
      } catch (error) {
        console.error('Lỗi đăng xuất:', error);
        alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
      }
    };
    return {
      handleLogout,
      user,
      isLoggedIn
    };
  }

};





</script>

<style scoped>
.loc {
  background-color: #c53f51;
  border: none;
}

canvas {
  width: 100%;
  height: 300px;
}
</style>
