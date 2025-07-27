<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>
  <div class="container">
    <div class="card p-3">
      <h5 class="border-bottom pb-2">Thông tin</h5>
      <div class="row">
        <div class="col-6">Ngày đặt:</div>
        <div class="col-6 text-end">{{ formatDate(info.order_time) }}</div>
        <div class="col-6" v-show="info.reservations_time">Ngày dự kiến nhận bàn:</div>
        <div class="col-6 text-end" v-show="info.reservations_time">{{ formatDate(info.reservations_time) }}</div>
        <div class="col-6" v-show="info.reservations_time">Giờ nhận bàn dự kiến:</div>
        <div class="col-6 text-end" v-show="info.reservations_time">
          {{ formatTime(info.reservations_time) }} - {{ formatTime(info.expiration_time) }} giờ
        </div>
        <div class="col-6" v-show="info.reservations_time">Lượng khách:</div>
        <div class="col-6 text-end" v-show="info.reservations_time">{{ info.guest_count }} người</div>
        <div class="col-6" v-show="info.reservations_time">Bàn: </div>
        <div class="col-6 text-end" v-show="info.reservations_time">
          {{ tableNumbers || 'Chưa xếp' }}
        </div>
        <div class="col-6">Phương thức thanh toán:</div>
        <div class="col-6 text-end">Chưa rõ</div>
        <div class="col-6">Trạng thái thanh toán:</div>
        <div class="col-6 text-end">Chưa rõ</div>
        <div class="col-6">Trạng thái đơn:</div>
        <div class="col-6 text-end">
          <span v-if="info.reservations_time">{{ info.reservation_status }}</span>
          <span v-else>{{ info.order_status }}</span>
        </div>
        <div class="col-6">Ghi chú:</div>
        <div class="col-6 text-end">{{ info.note || 'không có' }}</div>
      </div>
    </div>

    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Thông tin khách hàng</h5>
      <div class="row">
        <div class="col-6">Họ tên:</div>
        <div class="col-6 text-end">{{ info.guest_name }}</div>
        <div class="col-6">Số điện thoại:</div>
        <div class="col-6 text-end">{{ info.guest_phone }}</div>
        <div class="col-6">Email:</div>
        <div class="col-6 text-end">{{ info.guest_email }}</div>
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
          <tr v-for="item in info.details" :key="item.id">
            <td>{{ item.item_id }}</td>
            <td>
              <img :src="getImageUrl(item.image)" class="me-2" alt="img" width="80px" height="80px">
              {{ item.food_name || item.combo_name }}
              <ul v-if="item.toppings && item.toppings.length" class="mb-0 ps-3 ">
                <li v-for="topping in item.toppings" :key="topping.food_toppings_id">
                  + {{ topping.topping_name }} ({{ Number(topping.price).toLocaleString() }} đ)
                </li>
              </ul>
            </td>
            <td>{{ formatNumber(item.price) }} VNĐ</td>
            <td>{{ item.quantity }}</td>
            <td>{{ formatNumber(item.price * item.quantity) }} VNĐ</td>
          </tr>
        </tbody>
      </table>
      <div class="text-end">
        <p>Phí ship: {{ formatNumber(info.ship_cost) }} VNĐ</p>
        <p>Khuyến mãi: -{{ formatNumber(info.money_reduce) }} VNĐ</p>
        <p>Trừ điểm Tpoint: -{{ formatNumber(info.tpoint_used) }} VNĐ</p>
        <h5>Tổng cộng (VAT): {{ formatNumber(info.total_price) }} VNĐ</h5>
      </div>
    </div>
    <button class="btn btn-secondary mt-2 p-2" @click="goBack">Quay lại</button>
    <button class="btn btn-info mt-2 ms-2 p-2 fw-semibold" style="color: white;"
      v-if="(info.order_status == 'Chờ xác nhận' || info.order_status == 'Đã xác nhận') && info.guest_address"
      @click="showModal">
      Thay đổi địa chỉ nhận
    </button>

    <button v-if="info.order_status == 'Chờ xác nhận' || info.order_status == 'Đã xác nhận'"
      @click="cancelOrderForOrder" class="btn btn-danger1 mt-2 ms-2 p-2" style="width: 100px;">Hủy đơn</button>

    <!-- Khi đơn hàng đang giao -->
    <router-link :to="{ name: 'delivery', params: { id: info.id } }"
      v-if="info.order_status === 'Bắt đầu giao' && info.shipper_id != null">
      <button class="btn btn-sm btn-outline-danger mt-2 ms-2 p-2" style="width: 120px;">
        Theo dõi đơn
      </button>
    </router-link>

    <!-- Khi không phải đang giao -->
    <button v-else class="btn btn-sm btn-outline-secondary mt-2 ms-2 p-2" style="width: 120px;" disabled>
      Theo dõi đơn
    </button>
    <p class="text-muted ms-2 mt-1" style="font-size: 0.9rem;">
      * Nút theo dõi đơn hàng chỉ khả dụng khi đơn hàng trong trạng thái giao hàng
    </p>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
          <div class="modal-header bg-primary text-white rounded-top">
            <h5 class="modal-title" id="staticBackdropLabel">Thay đổi địa chỉ nhận hàng</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form @submit.prevent="updateAddressForOrder">
            <div class="modal-body">
              <label for="addressTextarea" class="form-label">Địa chỉ mới</label>
              <textarea id="addressTextarea" v-model="address" class="form-control border-1  rounded-0" rows="4"
                placeholder="Nhập địa chỉ mới..." required></textarea>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
              <button type="submit" class="btn btn-primary">Thay đổi</button>
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
import { computed } from 'vue';
import Swal from 'sweetalert2';
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
    const isLoading = ref(false)
    const table = ref([])

    const {
      info,
      getInfo,
      formatNumber,
      formatDate,
      getImageUrl,
      formatTime
    } = Info.setup()


    const cancelOrder = async () => {
      const result = await Swal.fire({
        title: 'Bạn có chắc muốn hủy đơn này? Hành động này không thể hoàn tác.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
      });
      if (result.isConfirmed) {
        try {
          isLoading.value = true;
          const status = await axios.put(`http://127.0.0.1:8000/api/order-history-info/cancle/${id}`)
          if (status) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Đã hủy thành công!',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
            })
          }
        } catch (error) {
          console.error(error)
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Hủy thất bại.',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
          })
        } finally {
          isLoading.value = false
        }
      }
    }

    const updateAddress = async () => {
      const result = await Swal.fire({
        title: 'Bạn có chắc muốn thay đổi địa chỉ nhận hàng',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
      });
      if (result.isConfirmed) {
        try {
          isLoading.value = true;
          const status = await axios.put(
            `http://127.0.0.1:8000/api/order-history-info/update-address/${id}`,
            {
              guest_address: address.value,
            }
          );
          if (status) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Thay đổi thành công!',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
            })
            const modal = Modal.getInstance(document.getElementById('staticBackdrop'));
            modal.hide();
          }
        } catch (error) {
          console.error(error);
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Cập nhật thất bại!',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
          })
        } finally {
          isLoading.value = false
        }
      }
    };
    const showModal = () => {
      const modal = new Modal(document.getElementById('staticBackdrop'));
      modal.show();
    };

    const updateAddressForOrder = async () => {
      try {
        isLoading.value = true;
        await updateAddress()
        await getInfo('order', id)
        address.value = info.value.guest_address;
      } catch (error) {
        console.log(error);

      } finally {
        isLoading.value = false
      }

    }

    const cancelOrderForOrder = async () => {
      await cancelOrder()
      await getInfo('order', id)
    }

    const tableNumbers = computed(() => {
      return info.value.tables ? info.value.tables.map(table => table.table_number).join(', ') : '';
    });
    onMounted(async () => {
      try {
        await getInfo('order', id);
        address.value = info.value.guest_address;
        table.value = info.value.tables
        console.log(info.value);

      } catch (error) {
        console.error("getInfo:", error);
      }
    });

    return {
      route,
      id,
      info,
      getImageUrl,
      formatNumber,
      formatDate,
      formatTime,
      cancelOrder,
      updateAddress,
      address,
      showModal,
      updateAddressForOrder,
      cancelOrderForOrder,
      isLoading,
      tableNumbers
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
  color: #c92c3c;
}

.bg-primary {
  background-color: #c92c3c !important;
}

.btn-primary {
  background-color: #c92c3c;
  border: none;
}

.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
