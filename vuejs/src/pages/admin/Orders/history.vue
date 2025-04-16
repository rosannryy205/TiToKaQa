<template>
  <div class="container">
    <h2 class="mb-3 text-md-start">Lịch Sử Đơn Hàng</h2>

    <!-- Tabs lọc trạng thái đơn -->
    <!-- Tabs -->
    <ul class="nav nav-tabs fs-6">
      <li class="nav-item">
        <router-link :to="{ name: 'orders-history' }" class="nav-link" :class="{ active: activeTab === 'Tất cả' }"
          @click.prevent="setActive('Tất cả')">
          Tất cả
        </router-link>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Chưa xác nhận' }"
          @click.prevent="setActive('Chưa xác nhận')">
          Chưa xác nhận
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Đã thanh toán' }"
          @click.prevent="setActive('Đã thanh toán')">
          Đã thanh toán
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link" :class="{ active: activeTab === 'Đã hủy' }" @click.prevent="setActive('Đã hủy')">
          Đã hủy
        </a>
      </li>
    </ul>

    <!-- Bộ lọc -->
    <div class="row mt-3 g-2">
      <div class="col-12 col-sm-6 col-md-4">
        <label>Hiển thị:</label>
        <select class="form-select">
          <option selected>5</option>
          <option>10</option>
          <option>15</option>
        </select>
      </div>

      <div class="col-12 col-sm-6 col-md-4">
        <label>Loại đơn:</label>
        <select class="form-select">
          <option selected>Tất cả</option>
          <option>Mua mang về</option>
          <option>Đặt bàn</option>
        </select>
      </div>
    </div>

    <!-- Bảng đơn hàng -->
    <div class="table-responsive mt-3 d-none d-lg-block">
      <table class="table table-bordered dh">
        <thead class="table-light">
          <tr>
            <th>STT</th>
            <!-- <th>Nhân viên</th> -->
            <th>Khu vực/Bàn</th>
            <th>Thông tin KH</th>
            <th>Loại đơn</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in order" :key="item.id">
            <td>{{ item.id }}</td>
            <!-- <td class="text-truncate" title="Nguyễn Thị Thuỷ Tiên">Nguyễn Thị Thuỷ Tiên</td> -->
            <td>
              {{item.tables && item.tables.length ? item.tables.map(t => t.table_number).join(', ') : 'Không có'}}
            </td>
            <td class="text-truncate" :title="item.guest_name + ' - ' + item.guest_phone">{{ item.guest_name }} - {{
              item.guest_phone }}</td>
            <td>{{ item.reservations_time ? 'Đặt bàn' : 'Mang về' }}</td>
            <td>{{ formatNumber(item.total_price) }} VNĐ</td>
            <td>
              <select class="form-select">
                <option selected>{{ item.order_status }}</option>
                <option>Chưa xác nhận</option>
                <option>Đã hủy</option>
              </select>
            </td>
            <td>
              <router-link :to="{ name: 'admin-orders-detail', params: { id: item.id } }"
                class="btn btn-primary btn-sm">Chi tiết</router-link>
              <button class="btn btn-secondary btn-sm">In</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile View -->
    <div class="d-block d-lg-none mt-2">
      <div class="card mb-3">
        <div class="card-body" v-for="(item, index) in order" :key="item.id">
          <h5 class="card-title fw-bold">Đơn #1</h5>
          <p><strong>Nhân viên:</strong> Nguyễn Thị Thuỷ Tiên</p>
          <p><strong>Khu vực/Bàn:</strong> {{item.tables && item.tables.length ? item.tables.map(t =>
            t.table_number).join(', ') : 'Không có' }}</p>
          <p :title="item.guest_name + ' - ' + item.guest_phone"><strong>Thông tin KH:</strong> {{ item.guest_name }} -
            {{ item.guest_phone }}</p>
          <p><strong>Loại đơn:</strong>{{ item.reservations_time ? 'Đặt bàn' : 'Mang về' }}</p>
          <p><strong>Tổng tiền:</strong> {{ formatNumber(item.total_price) }} đ</p>
          <p><strong>Trạng thái:</strong>
            <select class="form-select">
              <option selected>{{ item.order_status }}</option>
              <option>Chưa xác nhận</option>
              <option>Đã hủy</option>
            </select>
          </p>
          <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm flex-grow-1">Chi tiết</button>
            <button class="btn btn-secondary btn-sm flex-grow-1">In</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
export default {
  data() {
    return {
      activeTab: 'Tất cả',
    };
  },


  methods: {
    setActive(tab) {
      this.activeTab = tab;
    },
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },



  setup() {
    const order = ref([])

    const getOrders = () => {
      axios.get(`http://127.0.0.1:8000/api/get_all_orders`)
        .then(response => {
          order.value = (response.data.orders ?? []).sort((a, b) => a.id - b.id)

          console.log('Danh sách order:', order.value)
        })
        .catch(error => {
          console.error('Lỗi khi lấy đơn hàng:', error)
        })
    }



    onMounted(async () => {
      await getOrders();
    });


    return {
      order,
      getOrders,
    }
  }



};
</script>
<style>
/* Giữ nội dung gọn gàng và dễ đọc trên mọi thiết bị */
.dh td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}

.dh td:hover {
  position: relative;
  white-space: normal;
  overflow: visible;
  z-index: 10;
  background: white;
  padding: 5px;
}

@media (max-width: 768px) {
  .table-responsive {
    display: none;
  }

  /* Căn chỉnh padding & margin để tiết kiệm không gian */
  .card-body p {
    margin-bottom: 5px;
  }

  .card-title {
    font-size: 1rem;
  }

  /* Đảm bảo các nút vừa với màn hình nhỏ */
  .btn {
    font-size: 0.875rem;
    padding: 5px 10px;
  }
}
</style>
