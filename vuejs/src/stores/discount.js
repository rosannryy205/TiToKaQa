import axios from 'axios'
import { ref, onMounted } from 'vue'
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
    const discountId = ref(null)
    const discountInputId = ref(null)
    const showMoreDiscounts = ref(false)

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
      discountId.value = selected.id
      showMoreDiscounts.value = false
    }

    const handleDiscountInput = () => {
      const code = discountInput.value.trim().toUpperCase()
      const discount = discounts.value.find((d) => d.code === code)
      if (discount) {
        discountInputId.value = discount.id
        applyDiscountCode(code)
        discountInput.value = ''
      } else {
        alert('Mã giảm giá không hợp lệ')
      }
    }

    onMounted(() => {
      getAllDiscount()
    })

    return {
      cartItems,
      discounts,
      discountInput,
      selectedDiscount,
      discountId,
      discountInputId,
      applyDiscountCode,
      handleDiscountInput,
      showMoreDiscounts,
    }
  },
}
