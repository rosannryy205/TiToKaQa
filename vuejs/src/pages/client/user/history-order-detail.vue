<template>
  <div class="container">
    <div class="card p-3">
      <h5 class="border-bottom pb-2">Thông tin</h5>
      <div class="row">
        <div class="col-6">Ngày đặt:</div>
        <div class="col-6 text-end">{{ formatDate(info.order_time) }}</div>
        <div class="col-6" v-if="info.reservations_time">Ngày dự kiến nhận bàn:</div>
        <div class="col-6 text-end" v-if="info.reservations_time">{{ formatDate(info.reservations_time) }}</div>
        <div class="col-6" v-if="info.reservations_time">Giờ nhận bàn dự kiến:</div>
        <div class="col-6 text-end" v-if="info.reservations_time">
          {{ formatTime(info.reservations_time) }} - {{ formatTime(info.expiration_time) }} giờ
        </div>
        <div class="col-6" v-if="info.reservations_time">Lượng khách:</div>
        <div class="col-6 text-end" v-if="info.reservations_time">{{ info.guest_count }} người</div>
        <div class="col-6">Phương thức thanh toán:</div>
        <div class="col-6 text-end">Chưa rõ</div>
        <div class="col-6">Trạng thái thanh toán:</div>
        <div class="col-6 text-end">Chưa rõ</div>
        <div class="col-6">Trạng thái đơn:</div>
        <div class="col-6 text-end">
          <span v-if="info.reservations_time">{{ info.reservation_status }}</span>
          <span v-else>{{ info.order_status }}</span>
        </div>
      </div>
    </div>

    <div class="card p-3 mt-3" v-if="info.reservations_time">
      <h5 class="border-bottom pb-2">Bàn và khu vực</h5>
      <div class="row">
        <div class="col-6">Bàn:</div>
        <div class="col-6 text-end">Chưa rõ</div>
        <div class="col-6">Số người:</div>
        <div class="col-6 text-end">Chưa rõ</div>
      </div>
    </div>

    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Thông tin khách hàng</h5>
      <div class="row">
        <div class="col-6">Họ tên:</div>
        <div class="col-6 text-end">{{ user.name }}</div>
        <div class="col-6">Số điện thoại:</div>
        <div class="col-6 text-end">{{ user.phone }}</div>
        <div class="col-6">Email:</div>
        <div class="col-6 text-end">{{ user.email }}</div>
        <div class="col-6">Địa chỉ:</div>
        <div class="col-6 text-end">{{ info.guest_address || '' }}</div>
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
        <tbody v-if="info.details && info.details.length">
          <tr v-for="(detail, index) in info.details" :key="index">
            <td>{{ ++index }}</td>
            <td>
              <img :src="getImageUrl(detail.image)" style="width: 50px; height: auto; margin-right: 10px;" class="me-2"
                alt="img">
              {{ detail.food_name }}
            </td>
            <td>{{ formatNumber(detail.price) }} VND</td>
            <td>{{ detail.quantity }}</td>
            <td>{{ formatNumber(detail.price * detail.quantity) }} VNĐ</td>
          </tr>
        </tbody>
      </table>
      <div class="text-end">
        <p>Tạm tính: {{ formatNumber(info.total_price) }} VNĐ</p>
        <p>Khuyến mãi: 0 đ</p>
        <h5>Tổng cộng (VAT): {{ formatNumber(info.total_price) }} VNĐ</h5>
      </div>
    </div>
    <button class="btn btn-secondary mt-2" @click="goBack">Quay lại</button>
    <button class="btn btn-info mt-2 ms-2" @click="showModal">Thay đổi địa chỉ nhận
      hàng</button>
    <button @click="cancelOrder" class="btn btn-danger mt-2 ms-2" style="width: 100px;">Hủy đơn</button>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Thay đổi địa chỉ nhận hàng</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" @submit.prevent="updateAddress">
            <div class="modal-body">
              <textarea v-model="address" cols="55" rows="5"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button class="btn btn-primary">Thay đổi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Info } from '@/stores/info-order-reservation';
import { useRoute } from 'vue-router'
import { Modal } from 'bootstrap'
import { onMounted } from 'vue';
import axios from 'axios';
import { ref } from 'vue';
export default {
  methods: {
    goBack() {
      window.history.back();
    }
  },
  setup() {
    const route = useRoute()
    const id = route.params.id
    const address = ref('')
    const {
      info,
      user,
      getInfo,
      formatNumber,
      formatDate,
      getImageUrl,
      formatTime
    } = Info.setup()


    const cancelOrder = async () => {
      try {
        if (confirm('Bạn có chắc muốn huỷ đơn này')) {
          const status = await axios.put(`http://127.0.0.1:8000/api/order-history-info/cancle/${id}`)
          if (status) {
            alert('Hủy đơn thành công.')
          }
        }

      } catch (error) {
        console.error(error)
        alert('Cập nhật thất bại.')
      }
    }

    const updateAddress = async () => {
      try {
        if (confirm('Bạn có chắc muốn thay đổi địa chỉ nhận hàng')) {
          const status = await axios.put(
            `http://127.0.0.1:8000/api/order-history-info/update-address/${id}`,
            {
              guest_address: address.value,
            }
          );
          if (status) {
            alert('Thay đổi thành công.');
            const modal = Modal.getInstance(document.getElementById('staticBackdrop'));
            modal.hide();
          }
        }
      } catch (error) {
        console.error(error);
        alert('Cập nhật thất bại.');
      }
    };
    const showModal = () => {
      const modal = new Modal(document.getElementById('staticBackdrop'));
      modal.show();
    };


    onMounted(() => {
      getInfo('order', id).then(() => {
        address.value = info.guest_address || '';
      });
    });

    return {
      route,
      id,
      info,
      user,
      getImageUrl,
      formatNumber,
      formatDate,
      formatTime,
      cancelOrder,
      updateAddress,
      address,
      showModal
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
