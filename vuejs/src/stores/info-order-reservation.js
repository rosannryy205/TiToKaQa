import axios from 'axios'
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import numeral from 'numeral'

export const Info = {
  setup() {
    const info = ref({});
    const route = useRoute();
    const orderId = route.params.orderId;
    const orders = ref([])

    const getInfo = async (type, orderId) => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/order-reservation-info', {
          params: {
            value: orderId,
            type: type
          }
        })
        info.value = res.data.info
        // console.log(info.value);

      } catch (error) {
        console.log(error);
      }
    }

    const formatNumber = (value) => {
      return numeral(value).format('0,0')
    }

    const getImageUrl = (image) => {
      return `/img/food/${image}`
    }
    const formatDate = (dateStr) => {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return date.toLocaleDateString('vi-VN'); // chỉ hiển thị ngày
    };
    const formatTime = (dateStr) => {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return date.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
      });
    };



    return {
      info,
      getInfo,
      formatNumber,
      getImageUrl,
      orderId,
      formatDate,
      formatTime,
      orders
    }
  }
}
