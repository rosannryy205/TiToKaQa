import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import numeral from 'numeral'
import { useRouter } from 'vue-router'

export const Discounts = {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
  },
  setup() {

    const router = useRouter()
    const cartItems = ref([])
    const user = JSON.parse(localStorage.getItem('user')) || {}
    const userId = user?.id || 'guest'
    const cartKey = `cart_${userId}`
    const discounts = ref([])
    const discountInput = ref('')
    const moreDiscounts = ['HELLO50', 'NEWUSER', 'BUY2GET1', 'FLASH15', 'WELCOME30']
    const selectedDiscount = ref('')
    const showMoreDiscounts = ref(false)

    const getAllDiscount = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/discounts')
        discounts.value = res.data
      } catch (err) {
        console.error(err)
      }
    }

    const handleDiscountInput = () => {
      const code = discountInput.value.trim().toUpperCase()
      const discount = discounts.value.find((d) => d.code === code)
      if (discount) {
        applyDiscountCode(code)
        discountInput.value = ''
      } else {
        alert('Mã giảm giá không hợp lệ')
      }
    }

    const applyDiscountCode = (code) => {
      if (selectedDiscount.value === code) return
      selectedDiscount.value = code
      console.log(selectedDiscount.value)
      showMoreDiscounts.value = false
    }

    const finalTotal = computed(() => {
      return Math.max(totalPrice.value - discountAmount.value, 0)
    })
    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => {
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

    const totalQuantity = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    })

    const discountAmount = computed(() => {
      const discount = discounts.value.find((d) => d.code === selectedDiscount.value)
      if (!discount) return 0

      if (discount.discount_method === 'percent') {
        return (totalPrice.value * discount.discount_value) / 100
      }
      if (discount.discount_method === 'fixed') {
        return discount.discount_value
      }

      return 0
    })

    const loadCart = () => {
      const storedCart = localStorage.getItem(cartKey)
      cartItems.value = storedCart ? JSON.parse(storedCart) : []
    }

    onMounted(() => {
      getAllDiscount()
      loadCart()
    })

    return {

      cartKey,
      cartItems,
      totalPrice,
      totalPriceItem,
      totalQuantity,
      discounts,
      applyDiscountCode,
      showMoreDiscounts,
      selectedDiscount,
      moreDiscounts,
      discountInput,
      handleDiscountInput,
      finalTotal,
      discountAmount,
      loadCart
    }
  }
}
