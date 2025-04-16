<template>
  <div class="container" v-if="orderById">
    <div class="card p-3">
      <h5 class="border-bottom pb-2">Thông tin đặt bàn</h5>
      <div class="row">
        <div class="col-6">Ngày đặt bàn: </div>
        <div class="col-6 text-end">{{ orderById.reservations_time ? orderById.reservations_time : 'Không có' }}</div>
        <div class="col-6">Ngày dự kiến nhận bàn:</div>
        <div class="col-6 text-end">02/06/2023</div>
        <div class="col-6">Giờ nhận bàn dự kiến:</div>
        <div class="col-6 text-end">{{ orderById.reservations_time ? orderById.expiration_time : 'Không có' }}</div>
        <div class="col-6">Số khách hàng:</div>
        <div class="col-6 text-end">{{ orderById.reguest_count ? orderById.guest_count : 'Không có' }}</div>
        <div class="col-6">Tham chiếu:</div>
        <div class="col-6 text-end"><a href="#">{{ orderById.id }}</a></div>
        <div class="col-6">Trạng thái đơn:</div>
        <div class="col-6 text-end"><span class="badge bg-success">{{ orderById.order_status }}</span></div>
      </div>
    </div>

    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Bàn và khu vực</h5>
      <div class="row">
        <div class="col-6">Khu vực:</div>
        <div class="col-6 text-end">{{ orderById.reservations_time ? orderById.reservations_time : 'Không có' }}</div>
        <div class="col-6">Bàn:</div>
        <div class="col-6 text-end">{{ orderById.reservations_time ? orderById.reservations_time : 'Không có' }}</div>
        <div class="col-6">Số người:</div>
        <div class="col-6 text-end">{{ orderById.reservations_time ? orderById.reservations_time : 'Không có' }}</div>
      </div>
    </div>

    <div class="card p-3 mt-3">
      <h5 class="border-bottom pb-2">Thông tin khách hàng</h5>
      <div class="row">
        <div class="col-6">Họ tên:</div>
        <div class="col-6 text-end">{{ orderById.guest_name }}</div>
        <div class="col-6">Số điện thoại:</div>
        <div class="col-6 text-end">{{ orderById.guest_phone }}</div>
        <div class="col-6">Email:</div>
        <div class="col-6 text-end">{{ orderById.guest_email }}</div>
        <div class="col-6">Địa chỉ:</div>
        <div class="col-6 text-end">{{ orderById.guest_address }}</div>
        <div class="col-6">Ngày đặt hàng:</div>
        <div class="col-6 text-end">{{ orderById.order_time }}</div>
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
          <tr v-for="(item, index) in orderById.details" :key="item.id">
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
            <td>{{ formatNumber(totalPriceItem(item)) }} VNĐ</td>
          </tr>
        </tbody>
      </table>
      <div class="text-end">
        <p>Tạm tính: {{ formatNumber(totalPrice) }} VNĐ</p>
        <p>Khuyến mãi: 0 đ</p>
        <h5>Tổng cộng (VAT): {{ formatNumber(totalPrice) }} VNĐ</h5>
      </div>
    </div>

    <button class="btn btn-secondary mt-3" @click="goBack">Quay lại</button>
  </div>
</template>

<script>
import axios from 'axios';
import { useRoute } from 'vue-router';
import { ref, onMounted, computed } from 'vue';
import numeral from 'numeral'; // đúng cú pháp import

export default {
  setup() {
    const route = useRoute();
    const orderId = route.params.id;
    const orderById = ref([]);

    const getOrderById = () => {
      axios.get(`http://127.0.0.1:8000/api/order_detail/${orderId}`)
        .then(response => {
          orderById.value = response.data.order_detail;
          console.log('Danh sách chi tiết:', orderById.value);
        })
        .catch(error => {
          console.error('Lỗi khi lấy đơn hàng:', error);
          console.log("orderId từ route:", orderId);
        });
    };


    const totalPrice = computed(() => {
      if (!orderById.value.details || !Array.isArray(orderById.value.details)) {
        return 0;
      }
      return orderById.value.details.reduce((sum, item) => {
        const basePrice = Number(item.price) * item.quantity
        const toppingPrice = item.toppings.reduce((tsum, topping) => {
          return tsum + (Number(topping.price) * item.quantity)
        }, 0)
        return sum + basePrice + toppingPrice
      }, 0)
    })


    const totalPriceItem = (item) => {
      const itemPrice = Number(item.price) * item.quantity;
      const toppingPrice = item.toppings.reduce((sum, topping) => {
        return sum + (Number(topping.price) * item.quantity);
      }, 0);
      return itemPrice + toppingPrice;
    };

    const formatNumber = (value) => {
      return numeral(value).format('0,0');
    };

    const getImageUrl = (image) => {
      return `/img/food/${image}`;
    };

    const goBack = () => {
      window.history.back();
    };

    onMounted(() => {
      getOrderById();
    });

    return {
      orderById,
      orderId,
      getOrderById,
      formatNumber,
      getImageUrl,
      goBack,
      totalPriceItem,
      totalPrice
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
