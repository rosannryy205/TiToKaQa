import axios from 'axios'
import { FoodList } from '@/stores/food'
import { Discounts } from '@/stores/discount'
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
import { useRouter } from 'vue-router'
import { User } from '@/stores/user'

export const Payment = {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },
  setup() {
    const cartItems = ref([])
    const router = useRouter()
    const user1 = ref(null)
    // const fullname = ref('')
    // const email = ref('')
    // const phone = ref('')
    // const address = ref('')
    const note = ref('')
    const {
      user,
      form
    } = User.setup()

    // const user = JSON.parse(localStorage.getItem('user')) || {}
    // const isGuest = !user || !user.id

    // Sửa: Khởi tạo form riêng thay vì dùng User.setup()
    // const form = ref({
    //   fullname: '',
    //   email: '',
    //   phone: '',
    //   address: '',
    //   avatar: '',
    //   username: ''
    // })

    const { isLoading } = FoodList.setup()
    const {
      cartKey,
      totalPrice,
      totalPriceItem,
      finalTotal
    } = Discounts.setup()


    const selectedProvince = ref(null)
    const selectedDistrict = ref(null)
    const selectedWard = ref(null)

    const provinces = ref([])
    const districts = ref([])
    const wards = ref([])



    const getProvinces = async () => {
      try {
        const res = await axios.get(`https://provinces.open-api.vn/api/?depth=1`)
        provinces.value = res.data
      } catch (error) {
        console.error('Lỗi lấy tỉnh thành: ', error)
      }
    }

    const onProvinceChange = async () => {
      selectedDistrict.value = null
      selectedWard.value = null
      districts.value = []
      wards.value = []

      if (selectedProvince.value) {
        try {
          const res = await axios.get(`https://provinces.open-api.vn/api/p/${selectedProvince.value.code}?depth=2`)
          districts.value = res.data.districts
        } catch (error) {
          console.error('Lỗi khi lấy quận/huyện:', error)
        }
      }
    }

    const onDistrictChange = async () => {
      selectedWard.value = null
      wards.value = []

      if (selectedDistrict.value) {
        try {
          const res = await axios.get(`https://provinces.open-api.vn/api/d/${selectedDistrict.value.code}?depth=2`)
          wards.value = res.data.wards
        } catch (error) {
          console.error('Lỗi khi lấy xã/phường:', error)
        }
      }
    }

    const paymentMethod = ref('')

    const check_out = async () => {
      try {
        if (!paymentMethod.value) {
          alert('Vui lòng chọn phương thức thanh toán!')
          return
        }
        const fullAddress = `${form.value.address}, ${selectedWard.value?.name || ''}, ${selectedDistrict.value?.name || ''}, ${selectedProvince.value?.name || ''}`;
        const orderData = {
          user_id: user1.value ? user1.value.id : null,
          guest_name: form.value.fullname,
          guest_email: form.value.email,
          guest_phone: form.value.phone,
          guest_address: fullAddress,
          note: note.value || '',
          total_price: finalTotal.value,
          order_detail: cartItems.value.map(item => ({
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: item.type,
            toppings: item.toppings.map(t => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price
            }))
          }))
        }

        const response = await axios.post('http://127.0.0.1:8000/api/order', orderData)

        if (response && response.data) {
          const { status, order_id } = response.data;
          if (!status || !order_id) {
            alert('Đặt hàng thất bại!');
            return;
          }
          localStorage.setItem('order_id', order_id);
        } else {
          alert('Không nhận được dữ liệu từ server.');
          return;
        }

        if (paymentMethod.value === 'Thanh toán VNPAY' || paymentMethod.value === 'Thanh toán MOMO') {
          const paymentRes = await axios.post('http://127.0.0.1:8000/api/payment', {
            order_id: localStorage.getItem('order_id'),
            amount: finalTotal.value,
          })
          if (paymentRes.data.payment_url) {
            localStorage.setItem('payment_method', paymentMethod.value)
            localStorage.removeItem(cartKey)
            window.location.href = paymentRes.data.payment_url
          } else {
            alert('Không tạo được link thanh toán.')
          }
          return
        }
        if (paymentMethod.value === 'Thanh toán COD') {
          await new Promise(resolve => setTimeout(resolve, 300))
          await axios.post('http://127.0.0.1:8000/api/vnpay-return', {
            order_id: localStorage.getItem('order_id'),
            amount_paid: finalTotal.value,
            payment_method: 'Thanh toán COD',
            payment_status: 'Chưa thanh toán',
            payment_type: 'Thanh toán toàn bộ'
          })
          alert('Đặt hàng thành công!')
          localStorage.setItem('payment_method', paymentMethod.value)
          localStorage.removeItem(cartKey)
          router.push('/payment-result')
        }

      } catch (error) {
        console.error('Lỗi xảy ra:', error.message);
        alert('Lỗi khi gửi đơn hàng. Vui lòng thử lại!');
      }
    }

    const submitOrder = async () => {
      isLoading.value = true
      try {
        console.log('✅ form gửi đi:', form.value)
        await check_out()
        console.log('✅ check_out đã được gọi xong')
      } catch (error) {
        console.error('❌ Lỗi khi gọi check_out:', error)
      } finally {
        isLoading.value = false
      }
    }

    const loadCart = () => {
      const user = JSON.parse(localStorage.getItem('user'))
      const userId = user?.id || 'guest'
      const cartKey = `cart_${userId}`

      const storedCart = localStorage.getItem(cartKey)
      if (storedCart) {
        cartItems.value = JSON.parse(storedCart)
      } else {
        cartItems.value = []
      }
    }

    onMounted(() => {
      getProvinces(),
      loadCart()
      user1.value = JSON.parse(localStorage.getItem('user')) || null

      // if (!isGuest) {
      //   form.value.fullname = user.fullname || ''
      //   form.value.email = user.email || ''
      //   form.value.phone = user.phone || ''
      //   form.value.address = user.address || ''
      // }
    })

    return {
      router,
      cartKey,
      cartItems,
      totalPrice,
      totalPriceItem,
      // fullname,
      // email,
      // phone,
      // address,
      note,
      check_out,
      submitOrder,
      loadCart,
      isLoading,
      paymentMethod,
      form,
      finalTotal,
      user,


      provinces,
      districts,
      wards,
      selectedProvince,
      selectedDistrict,
      selectedWard,
      onDistrictChange,
      onProvinceChange,
    }
  }
}
