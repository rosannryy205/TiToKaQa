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
        <a class="nav-link" :class="{ active: activeTab === 'Chờ xác nhận' }"
          @click.prevent="setActive('Chờ xác nhận')">
          Chờ xác nhận
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Đã xác nhận' }" @click.prevent="setActive('Đã xác nhận')">
          Đã xác nhận
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Đang xử lý' }" @click.prevent="setActive('Đang xử lý')">
          Đang xử lý
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Đang giao hàng' }"
          @click.prevent="setActive('Đang giao hàng')">
          Đang giao hàng
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Giao thành công' }"
          @click.prevent="setActive('Giao thành công')">
          Giao thành công
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Giao thất bại' }"
          @click.prevent="setActive('Giao thất bại')">
          Giao thất bại
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'Đã hủy' }" @click.prevent="setActive('Đã hủy')">
          Đã hủy
        </a>
      </li>
    </ul>

    <!-- Bộ lọc -->
    <div class="row mt-3 g-2">
      <div class="col-12 col-sm-6 col-md-4">
        <label>Hiển thị:</label>
        <select class="form-select rounded">
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
          <tr v-for="(item, index) in filteredOrders" :key="item.id">
            <td>{{ item.id }}</td>
            <td>
              <div v-if="item.reservations_time && item.tables.length ">
                {{ item.tables.map(table => table.table_number).join(', ') }}
              </div>
              <div v-else>
                Không có bàn hoặc chưa xếp bàn
              </div>
            </td>
            <td class="text-truncate" :title="item.guest_name + ' - ' + item.guest_phone">{{ item.guest_name }} - {{
              item.guest_phone }}</td>
            <td>{{ item.reservations_time ? 'Đặt bàn' : 'Mang về' }}</td>
            <td>{{ formatNumber(item.total_price) }} VNĐ</td>
            <td>
              <div v-if="item.reservations_time">
                <select class="form-select">
                  <option value="Chờ xác nhận"> {{ item.reservation_status }} </option>
                </select>
              </div>
              <div v-else>
                <select class="form-select" :value="item.selectedStatus" @change="handleSelectChange(item, $event)">
                  <option value="Chờ xác nhận">Chờ xác nhận</option>
                  <option value="Đã xác nhận">Đã xác nhận</option>
                  <option value="Đang xử lý">Đang xử lý</option>
                  <option value="Đang giao hàng">Đang giao hàng</option>
                  <option value="Giao thành công">Giao thành công</option>
                  <option value="Giao thất bại">Giao thất bại</option>
                  <option value="Đã hủy">Đã hủy</option>
                </select>
              </div>
            </td>
            <td>
              <router-link :to="{ name: 'admin-orders-detail', params: { id: item.id } }"
                class="btn btn-primary btn-sm">Chi tiết
              </router-link>
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
          <p><strong>Khu vực/Bàn:</strong> {{ item.reservations_time ? item.table_number : 'Không có' }}</p>
          <p :title="item.guest_name + ' - ' + item.guest_phone"><strong>Thông tin KH:</strong> {{ item.guest_name }} -
            {{ item.guest_phone }}</p>
          <p><strong>Loại đơn:</strong>{{ item.reservations_time ? 'Đặt bàn' : 'Mang về' }}</p>
          <p><strong>Tổng tiền:</strong> {{ formatNumber(item.total_price) }} đ</p>
          <p><strong>Trạng thái:</strong>
          <div v-if="item.reservations_time">
            <select class="form-select">
              <option value="Chờ xác nhận"> {{item.reservation_status}}</option>
            </select>
          </div>
          <div v-else>
            <select class="form-select" :value="item.selectedStatus" @change="handleSelectChange(item, $event)">
              <option value="Chờ xác nhận">Chờ xác nhận</option>
              <option value="Đã xác nhận">Đã xác nhận</option>
              <option value="Đang xử lý">Đang xử lý</option>
              <option value="Đang giao hàng">Đang giao hàng</option>
              <option value="Giao thành công">Giao thành công</option>
              <option value="Giao thất bại">Giao thất bại</option>
              <option value="Đã hủy">Đã hủy</option>
            </select>
          </div>
          </p>
          <div class="d-flex gap-2">
            <router-link :to="{ name: 'admin-orders-detail', params: { id: item.id } }">
              <button class="btn btn-primary btn-sm flex-grow-1">Chi tiết</button>
            </router-link>
            <button class="btn btn-secondary btn-sm flex-grow-1">In</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import numeral from 'numeral'
export default {
  data() {
    return {
      activeTab: 'Tất cả',
    };
  },

  computed: {
    filteredOrders() {
      if (this.activeTab === 'Tất cả') {
        return this.order;
      } else {
        return this.order.filter(item => item.order_status === this.activeTab);
      }
    }
  },


  methods: {
    setActive(tab) {
      this.activeTab = tab;
    },

    getOrders() {
      axios.get(`http://127.0.0.1:8000/api/get_all_orders`)
        .then(response => {
          this.order = (response.data.orders ?? []).sort((a, b) => a.id - b.id)
          this.order.forEach(item => {
            item.selectedStatus = item.order_status;  // Gán lại giá trị ban đầu cho selectedStatus
          });
          console.log('Danh sách order:', this.order)
        })
        .catch(error => {
          console.error('Lỗi khi lấy đơn hàng:', error)
        });
    },


    isValidUpdateStatus(currentStatus, newStatus) {
      const orderFlow = [
        'Chờ xác nhận',
        'Đã xác nhận',
        'Đang xử lý',
        'Đang giao hàng',
        'Giao thành công',
        'Giao thất bại',
        'Đã hủy'
      ];

      const currentIndex = orderFlow.indexOf(currentStatus);
      const newIndex = orderFlow.indexOf(newStatus);

      // Trường hợp muốn hủy đơn
      if (newStatus === 'Đã hủy') {
        return currentStatus === 'Chờ xác nhận'; // Chỉ được hủy khi đơn đang "Chờ xác nhận"
      }

      if (currentStatus === 'Đang giao hàng' && (newStatus === 'Giao thành công' || newStatus === 'Giao thất bại')) {
        return true; // Cho phép nhảy trực tiếp từ "Đang giao hàng" đến "Giao thành công" hoặc "Giao thất bại"
      }

      // Không được cập nhật ngược (ngược dòng trạng thái)
      if (newIndex < currentIndex) return false;

      // Chỉ được cập nhật đúng 1 bước
      return newIndex === currentIndex + 1;
    },



    updateStatus(id, newStatus) {
      axios
        .put(`http://127.0.0.1:8000/api/update/${id}/status`, {
          order_status: newStatus
        })
        .then(response => {
          console.log(response.data.mess);
          this.getOrders(); // Cập nhật lại đơn hàng từ DB sau khi thay đổi trạng thái
        })
        .catch(error => {
          console.error('Lỗi khi cập nhật', error);
          alert('Cập nhật trạng thái thất bại!');
        });
    },



    handleSelectChange(item, event) {
      const newStatus = event.target.value; // Lấy giá trị từ dropdown
      const currentStatus = item.order_status;

      // Lưu lại giá trị gốc của selectedStatus
      const originalSelectedStatus = item.selectedStatus;

      // Kiểm tra xem trạng thái cập nhật có hợp lệ không
      if (!this.isValidUpdateStatus(currentStatus, newStatus)) {
        alert('Không thể cập nhật trạng thái. Vui lòng đảm bảo cập nhật đúng thứ tự và chỉ được hủy khi đơn đang ở trạng thái "Chờ xác nhận".');

        // Nếu có lỗi, giữ lại giá trị ban đầu của selectedStatus
        item.selectedStatus = originalSelectedStatus;

        // Đảm bảo dropdown cũng phản hồi đúng trạng thái cũ
        event.target.value = originalSelectedStatus;
        return;
      }

      // Tiến hành cập nhật trạng thái nếu không có lỗi
      this.updateStatus(item.id, newStatus);
    },


    // Hàm reload orders sau khi cập nhật
    getOrders() {
      axios.get(`http://127.0.0.1:8000/api/get_all_orders`)
        .then(response => {
          this.order = (response.data.orders ?? []).sort((a, b) => a.id - b.id)
          this.order.forEach(item => {
            item.selectedStatus = item.order_status;  // Gán lại giá trị ban đầu cho selectedStatus
          });
          console.log('Danh sách order:', this.order)
        })
        .catch(error => {
          console.error('Lỗi khi lấy đơn hàng:', error)
        });
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
          order.value.forEach(item => {
            item.selectedStatus = item.order_status; // gán trạng thái hiện tại cho dropdown
          });
          console.log('Danh sách order:', order.value)
        })
        .catch(error => {
          console.error('Lỗi khi lấy đơn hàng:', error)
        })
    };


    onMounted(() => {
      getOrders()
    })

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
