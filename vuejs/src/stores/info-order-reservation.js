import axios from 'axios'
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import numeral from 'numeral'
import { API_URL } from '@/config'
<<<<<<< HEAD
import { STORAGE_URL } from '@/config'
=======
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
export const Info = {
  setup() {
    const info = ref({});
    const route = useRoute();
    const orderId = route.params.orderId;
    const orders = ref([])

    const getInfo = async (type, orderId) => {
      try {
        const res = await axios.get(`${API_URL}/order-reservation-info`, {
          params: {
            value: orderId,
            type: type
          }
        })
        info.value = res.data.info

      } catch (error) {
        console.log(error);
      }
    }

    const formatNumber = (value) => {
      return numeral(value).format('0,0')
    }

    const getImageUrl = (image) => {
<<<<<<< HEAD
      return `${STORAGE_URL}/img/food/${image}`
=======
      return `http://127.0.0.1:8000/storage/img/food/${image}`
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
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

    const formatDateTime = (dateStr) => {
      if (!dateStr) return '';

      const d = new Date(dateStr);

      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0'); // tháng bắt đầu từ 0
      const day = String(d.getDate()).padStart(2, '0');
      const hours = String(d.getHours()).padStart(2, '0'); // 24h format
      const minutes = String(d.getMinutes()).padStart(2, '0');

      return `${year}-${month}-${day} ${hours}:${minutes}`;
    };

    return {
      info,
      getInfo,
      formatNumber,
      getImageUrl,
      orderId,
      formatDate,
      formatTime,
      orders,
      formatDateTime
    }
  }
}
