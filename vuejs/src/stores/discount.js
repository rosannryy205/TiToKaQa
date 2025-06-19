// src/stores/discount.js
import axios from 'axios'
import { ref, onMounted, computed, watchEffect } from 'vue'
import { toast } from 'vue3-toastify'
import { useShippingStore } from './shippingStore'
import { Cart } from '@/stores/cart'
import { FoodList } from './food'
import { useUserStore } from '@/stores/userAuth'

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

  const { allCates, getAllCategory } = FoodList.setup()
  const getAllChildCategoryIds = (categoryId) => {
    const result = [Number(categoryId)]
    const queue = [Number(categoryId)]

    while (queue.length > 0) {
      const current = queue.shift()
      allCates.value.forEach((cat) => {
        if (Number(cat.parent_id) === current) {
          result.push(cat.id)
          queue.push(cat.id)
        }
      })
    }

    return result
  }

  const userDiscounts = ref([])
  const userStore = useUserStore()

  const allDiscounts = computed(() => {
    const userCodes = userDiscounts.value.map(d => d.code)
    const systemDiscounts = discounts.value.filter(d => !userCodes.includes(d.code))
    return [...userDiscounts.value, ...systemDiscounts]
  })

  const getAllDiscount = async (query = {}) => {
    try {
      const params = new URLSearchParams(query).toString()
      const res = await axios.get(`http://127.0.0.1:8000/api/discounts?${params}`)

      if (query.source) {
        discounts.value = res.data.filter(d => d.source === query.source)
      } else {
        discounts.value = res.data
      }
    } catch (error) {
      console.error('Lỗi khi lấy mã giảm giá:', error)
    }
  }

  const fetchUserDiscounts = async () => {
    try {
      const res = await axios.get('http://127.0.0.1:8000/api/user-vouchers', {
        headers: {
          Authorization: `Bearer ${userStore.token}`,
        },
      })
      userDiscounts.value = res.data
    } catch (error) {
      console.error(error)
    }
  }

  const applyDiscountCode = (code) => {
    const selected = allDiscounts.value.find((d) => d.code === code)
    if (!selected) {
      alert('Mã giảm giá không hợp lệ')
      return
    }
    selectedDiscount.value = code
    showMoreDiscounts.value = false
  }

  const handleDiscountInput = () => {
    const code = discountInput.value.trim().toUpperCase()
    const discount = allDiscounts.value.find((d) => d.code === code)

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

  watchEffect(() => {
    const selected = allDiscounts.value.find((d) => d.code === selectedDiscount.value)
    if (selected) {
      discountId.value = selected.id
    }
  })

  const discountFoodAmount = computed(() => {
    const discount = allDiscounts.value.find((d) => d.code === selectedDiscount.value)
    if (!discount || discount.discount_type !== 'salefood') return 0

    const value = parseFloat(discount.discount_value)
    const max = parseFloat(discount.max_discount_amount || Infinity)
    let amount = 0

    if (discount.category_id) {
      const subtotalByCategory = cartItems.value
        .filter((item) =>
          getAllChildCategoryIds(discount.category_id).includes(Number(item.category_id))
        )
        .reduce((sum, item) => sum + item.price * item.quantity, 0)

      if (subtotalByCategory < discount.min_order_value) return 0

      amount = discount.discount_method === 'percent'
        ? (subtotalByCategory * value) / 100
        : value
    } else {
      if (totalPrice.value < discount.min_order_value) return 0

      amount = discount.discount_method === 'percent'
        ? (totalPrice.value * value) / 100
        : value
    }

    return Math.min(amount, max)
  })

  const discountShipAmount = computed(() => {
    const discount = allDiscounts.value.find((d) => d.code === selectedDiscount.value)
    if (!discount || discount.discount_type !== 'freeship') return 0

    const value = parseFloat(discount.discount_value)
    const max = parseFloat(discount.max_discount_amount || Infinity)
    let amount = 0

    amount = discount.discount_method === 'percent'
      ? (shippingFee.value * value) / 100
      : value

    return Math.min(amount, max)
  })

  const finalTotal = computed(() => {
    return Math.max(
      totalPrice.value - discountFoodAmount.value + shippingFee.value - discountShipAmount.value,
      0
    )
  })

  const getImageByType = (type) =>
    type === 'freeship' ? '/img/freeship-icon.png' : '/img/discount-icon.png'

  const formatCurrency = (num) => {
    if (!num) return '0'
    return Number(num).toLocaleString('vi-VN')
  }

  onMounted(async () => {
    await getAllCategory()
  })

  return {
    discounts,
    getAllDiscount,
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
    discountShipAmount,
    getAllChildCategoryIds,
    getImageByType,
    formatCurrency,
    fetchUserDiscounts,
    userDiscounts,
    allDiscounts // nếu cần dùng ở template
  }
}
