<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="container">
            <div class="card p-3">
              <h5 class="border-bottom pb-2">Thông tin</h5>
              <div class="row">
                <div class="col-6">Ngày đặt:</div>
                <div class="col-6 text-end">{{ formatDate(info.order_time) }}</div>
                <div class="col-6" v-show="info.reservations_time">Ngày dự kiến nhận bàn:</div>
                <div class="col-6 text-end" v-show="info.reservations_time">{{ formatDate(info.reservations_time) }}
                </div>
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
                <tbody>
                  <tr v-for="item in info.details" :key="item.id">
                    <td>{{ item.food_id }}</td>
                    <td>
                      <img :src="getImageUrl(item.image)" class="me-2" alt="img" width="80px" height="80px">
                      {{ item.food_name }}
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
                <p>Tạm tính: {{ formatNumber(info.total_price) }} VNĐ</p>
                <p>Khuyến mãi: 0 đ</p>
                <h5>Tổng cộng (VAT): {{ formatNumber(info.total_price) }} VNĐ</h5>
              </div>
            </div>

            <button class="btn btn-secondary mt-2 p-2" @click="goBack">Quay lại</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Info } from '@/stores/info-order-reservation';
import { useRoute } from 'vue-router'
import { onMounted } from 'vue';
import { computed } from 'vue';

export default {
  methods: {
    goBack() {
      window.history.back();
    }
  },
  setup() {
    const route = useRoute();
    const orderId = route.params.id;

    const {
      info,
      getInfo,
      formatNumber,
      formatDate,
      getImageUrl,
      formatTime
    } = Info.setup()


    const tableNumbers = computed(() => {
      return info.value.tables ? info.value.tables.map(table => table.table_number).join(', ') : '';
    });
    onMounted(async () => {
      try {
        await getInfo('order', orderId);
        console.log(info.value);

      } catch (error) {
        console.error("getInfo:", error);
      }
    });

    return {
      orderId,
      info,
      formatDate,
      formatNumber,
      getImageUrl,
      formatTime,
      tableNumbers
    };
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
