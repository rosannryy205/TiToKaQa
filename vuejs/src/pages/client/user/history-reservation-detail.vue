<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <div class="container" v-else>
    <div class="row gx-3">
      <div class="card p-3 mt-3 w-100">
        <h5 class="border-bottom pb-2 mb-3">Thông tin đơn hàng & khách hàng</h5>

        <div class="d-flex flex-wrap row-gap-2">
          <!-- Left column -->
          <div class="flex-fill min-w-200 me-3">
            <div class="d-flex justify-content-between">
              <span>Họ tên:</span>
              <span>{{ info.guest_name }}</span>
            </div>

            <div class="d-flex justify-content-between">
              <span>Số điện thoại:</span>
              <span>{{ info.guest_phone }}</span>
            </div>

            <div class="d-flex justify-content-between">
              <span>Email:</span>
              <span>{{ info.guest_email }}</span>
            </div>

            <div class="d-flex justify-content-between">
              <span>Ngày đặt:</span>
              <span>{{ formatDate(info.order_time) }}</span>
            </div>

            <template v-if="info.tables?.length">
              <div class="d-flex justify-content-between">
                <span>Ngày dự kiến nhận bàn:</span>
                <span>{{ formatDate(info.tables[0].reserved_from) }}</span>
              </div>

              <div class="d-flex justify-content-between">
                <span>Giờ nhận bàn:</span>
                <span>{{ formatTime(info.tables[0].reserved_from) }} - {{ formatTime(info.tables[0].reserved_to)
                  }}</span>
              </div>

              <div class="d-flex justify-content-between">
                <span>Số lượng khách:</span>
                <span>{{ info.guest_count || 0 }} người</span>
              </div>

              <div class="d-flex justify-content-between">
                <span>Bàn:</span>
                <span>{{ tableNumbers || 'Chưa xếp' }}</span>
              </div>
            </template>
          </div>

          <!-- Right column -->
          <div class="flex-fill min-w-200 me-3">
            <div v-for="(payment, index) in info.payment_info" :key="payment.id">
              <div class="d-flex justify-content-between">
                <span>Phương thức thanh toán {{ info.payment_info.length > 1 ? index + 1 : '' }}:</span>
                <span>{{ payment.payment_method }}</span>
              </div>
              <div class="d-flex justify-content-between">
                <span>Trạng thái thanh toán {{ info.payment_info.length > 1 ? index + 1 : '' }}:</span>
                <span>{{ payment.payment_status }}</span>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <span>Trạng thái đơn:</span>
              <span>{{ info.tables?.length ? (info.tables[0].reservation_status || info.order_status) :
                info.order_status }}</span>
            </div>

            <div class="d-flex justify-content-between">
              <span>Ghi chú:</span>
              <span>{{ info.note || 'Không có' }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Chi tiết hóa đơn -->
    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Chi tiết hóa đơn</h5>
      <div class="table-responsive">
        <table class="table table-striped">
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
            <tr v-for="(item, index) in info.details || []" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>
                <img :src="getImageUrl(item.image)" class="me-2 rounded" width="60" height="60" alt="img">
                {{ item.food_name || item.combo_name }}
                <ul v-if="item.toppings?.length" class="mb-0 ps-3">
                  <li v-for="topping in item.toppings" :key="topping.food_toppings_id">
                    + {{ topping.topping_name }} ({{ Number(topping.price).toLocaleString() }} VNĐ)
                  </li>
                </ul>
              </td>
              <td>{{ formatNumber(item.price) }} VNĐ</td>
              <td>{{ item.quantity }}</td>
              <td>{{ formatNumber(item.price * item.quantity) }} VNĐ</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tổng cộng -->
      <div class="text-end mt-3">
        <p>Phí giữ bàn: {{ formatNumber(Number(info.table_fee) || 0) }} VNĐ</p>
        <p>Tổng tiền món: {{ formatNumber(Number(info.total_price) || 0) }} VNĐ</p>
        <p>Đã thanh toán: {{ formatNumber(Number(info.total_paid) || 0) }} VNĐ</p>
        <h5>Tổng cộng (VAT): {{ formatNumber((Number(info.total_price) || 0) + (Number(info.table_fee) || 0)) }} VNĐ
        </h5>
      </div>
    </div>

    <!-- Nút hành động -->
    <div class="d-flex mt-3 gap-2">
      <button class="btn btn-secondary mt-2 p-2" @click="goBack">Quay lại</button>
      <button v-if="info.order_status == 'Chờ xác nhận' || info.order_status == 'Đã xác nhận'"
        @click="cancelOrderForOrder" class="btn btn-danger1 mt-2 ms-2 p-2" style="width: 100px;">Hủy đơn</button>
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
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
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
          const status = await axios.put(`${API_URL}/order-history-info/cancel/${id}`)
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

    const showModal = () => {
      const modal = new Modal(document.getElementById('staticBackdrop'));
      modal.show();
    };

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
      address,
      showModal,
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

.table-scroll {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  /* Ẩn trên Firefox */
}

.table-scroll::-webkit-scrollbar {
  display: none;
  /* Ẩn trên Chrome, Safari */
}

@media (max-width: 768px) {
  .table-scroll {
    display: block;
    white-space: nowrap;
  }

  .table-scroll table {
    min-width: 600px;
    /* đảm bảo có thể cuộn */
  }
}
</style>
