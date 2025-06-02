import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
// import numeral from 'numeral'
import { useRoute } from 'vue-router'
import { watchEffect } from 'vue'
import { toast } from 'vue3-toastify'
// export const discountId = ref(null)
export function Discounts()  {

    // const router = useRouter()
    const cartItems = ref([])
    const user = JSON.parse(localStorage.getItem('user')) || {}
    const userId = user?.id || 'guest'
    const route = useRoute()
    const orderId = route.params.orderId
    // const cartKey = `cart_${userId}`
    // const cartKey1 = `cart_${userId}_reservation_${orderId}`
    const discounts = ref([])
    const discountInput = ref('')
    const selectedDiscount = ref('')
    const discountInputId = ref(null)
    const showMoreDiscounts = ref(false)
    const discountId = ref(null)
    watchEffect(() => {
      const selected = discounts.value.find(d => d.code === selectedDiscount.value)
      if (selected) {
        discountId.value = selected.id
        console.log('✅ Tiền giảm:', discountAmount.value)
        console.log('✅ Tổng sau giảm:', finalTotal.value)
      }
    })

    const getAllDiscount = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/discounts')
        discounts.value = res.data
      } catch (err) {
        console.error(err)
      }
    }

    const applyDiscountCode = (code) => {
      const selected = discounts.value.find((d) => d.code === code)
      if (!selected) {
        alert('Mã giảm giá không hợp lệ')
        return
      }
      selectedDiscount.value = code
      showMoreDiscounts.value = false
    }

    const handleDiscountInput = () => {
      const code = discountInput.value.trim().toUpperCase()
      const discount = discounts.value.find((d) => d.code === code)
      if (!discount) {    
        toast.error('❌ Mã giảm giá không hợp lệ')
        return
      }
      if (totalPrice.value < discount.min_order_value) {
        toast.warning(
          `⚠️ Đơn hàng cần tối thiểu ${discount.min_order_value.toLocaleString()}đ để dùng mã ${code}`
        )
        return
      }
    
      discountInputId.value = discount.id
      applyDiscountCode(code)
      discountInput.value = ''
      toast.success(`✅ Mã ${code} đã được áp dụng!`)
    }
    const loadCart = () => {
      const cartKey = orderId
        ? `cart_${userId}_reservation_${orderId}`
        : `cart_${userId}`

      const storedCart = localStorage.getItem(cartKey)
      cartItems.value = storedCart ? JSON.parse(storedCart) : []
      console.log('ggggg' + orderId);

    }


    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        const basePrice = Number(item.price) * item.quantity
        const toppingPrice = item.toppings.reduce((tsum, topping) => {
          return tsum + (Number(topping.price) * item.quantity)
        }, 0)
        return sum + basePrice + toppingPrice
      }, 0)
    })
    const discountAmount = computed(() => {
      const discount = discounts.value.find((d) => d.code === selectedDiscount.value)
      if (!discount) return 0;

      const value = parseFloat(discount.discount_value) 
      const max = parseFloat(discount.max_discount_amount || Infinity)
      let amount = 0

      if (discount.discount_method === 'percent') {
        amount = (totalPrice.value * value) / 100
      }
      if (discount.discount_method === 'fixed') {
        amount = value
      }

      return Math.min(amount, max)
    })

    const finalTotal = computed(() => {
      return Math.max(totalPrice.value - discountAmount.value, 0)
    })





    const totalQuantity = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    })

    const totalPriceItem = (item) => {
      const itemPrice = Number(item.price) * item.quantity
      const toppingPrice = item.toppings.reduce((sum, topping) => {
        return sum + (Number(topping.price) * item.quantity)
      }, 0)
      return itemPrice + toppingPrice
    }

    // ⚠️ Load giỏ hàng trước khi lấy mã giảm giá để tránh lỗi tính toán
    onMounted(() => {
      loadCart()
      console.log(cartItems.value);

      getAllDiscount()
    })

    return {
      cartItems,
      discounts,
      discountInput,
      selectedDiscount,
      discountId,
      discountInputId,
      showMoreDiscounts,
      applyDiscountCode,
      handleDiscountInput,
      finalTotal,
      totalPrice,
      discountAmount,
      totalQuantity,
      totalPriceItem,
      loadCart,
    }
}
