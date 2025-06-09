// src/stores/discount.js
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import { watchEffect } from 'vue'
import { toast } from 'vue3-toastify'
import { useShippingStore } from './shippingStore'
import { Cart } from '@/stores/cart'



export function Discounts() {
  const shippingStore = useShippingStore()
  const shippingFee = computed(() => shippingStore.shippingFee)
  const discounts = ref([])
  const discountInput = ref('')
  const selectedDiscount = ref('')
  const discountInputId = ref(null)
  const showMoreDiscounts = ref(false)
  const discountId = ref(null)

  const {
    cartItems,
    totalPrice,
    totalQuantity,
    totalPriceItem,
    loadCart
  } = Cart()


 // src/stores/discount.js
watchEffect(() => {
  const selected = discounts.value.find((d) => d.code === selectedDiscount.value)
  if (selected) {
    discountId.value = selected.id
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
        `⚠️ Đơn hàng cần tối thiểu ${discount.min_order_value.toLocaleString()}đ để dùng mã ${code}`,
      )
      return
    }

    discountInputId.value = discount.id
    applyDiscountCode(code)
    discountInput.value = ''
    toast.success(`✅ Mã ${code} đã được áp dụng!`)
  }

  const discountFoodAmount = computed(() => {
    const discount = discounts.value.find((d) => d.code === selectedDiscount.value)
    if (!discount || discount.discount_type !== 'salefood') return 0
  
    const value = parseFloat(discount.discount_value)
    const max = parseFloat(discount.max_discount_amount || Infinity)
    let amount = 0
  
    if (discount.discount_method === 'percent') {
      amount = (totalPrice.value * value) / 100
    } else if (discount.discount_method === 'fixed') {
      amount = value
    }
  
    return Math.min(amount, max)
  })
  const discountShipAmount = computed(() => {
    const discount = discounts.value.find((d) => d.code === selectedDiscount.value)
    if (!discount || discount.discount_type !== 'freeship') return 0
  
    const value = parseFloat(discount.discount_value)
    const max = parseFloat(discount.max_discount_amount || Infinity)
    let amount = 0
  
    if (discount.discount_method === 'percent') {
      amount = (shippingFee.value * value) / 100
    } else if (discount.discount_method === 'fixed') {
      amount = value
    }
  
    return Math.min(amount, max)
  })
  
  const finalTotal = computed(() => {
    return Math.max(
      totalPrice.value - discountFoodAmount.value + shippingFee.value - discountShipAmount.value,
      0
    )
  })

  onMounted(() => {
    getAllDiscount()
  })

  return {
    discounts,
    discountInput,
    selectedDiscount,
    discountId,
    discountInputId,
    showMoreDiscounts,
    applyDiscountCode,
    handleDiscountInput,
    finalTotal,
    cartItems,
    totalPrice,
    totalQuantity,
    totalPriceItem,
    loadCart,
    discountFoodAmount,
    discountShipAmount  }
}
