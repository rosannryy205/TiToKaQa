import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import numeral from 'numeral'

export const Discounts = {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
  },
  setup() {
    const cartItems = ref([])
    const discounts = ref([])
    const discountInput = ref('')
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

   

    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        const base = item.price * item.quantity
        const toppings = item.toppings.reduce((tsum, t) => tsum + t.price * item.quantity, 0)
        return sum + base + toppings
      }, 0)
    })

    const totalPriceItem = (item) => {
      const itemPrice = item.price * item.quantity
      const toppingPrice = item.toppings.reduce((sum, t) => sum + t.price * item.quantity, 0)
      return itemPrice + toppingPrice
    }

    const totalQuantity = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    })

  
   

    onMounted(() => {
      getAllDiscount()
    })

    return {
      cartItems,
      totalPrice,
      totalPriceItem,
      totalQuantity,
      discounts,
      applyDiscountCode,
      showMoreDiscounts,
      selectedDiscount,
      discountInput,
      handleDiscountInput,
    }
  }
}
