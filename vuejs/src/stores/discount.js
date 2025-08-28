// src/stores/discount.js
import axios from 'axios'
import { ref, onMounted, computed, watch, watchEffect } from 'vue'
import Swal from 'sweetalert2'
import { useShippingStore } from './shippingStore'
import { Cart } from '@/stores/cart'
import { FoodList } from './food'
import { useUserStore } from '@/stores/userAuth'
import { User } from '@/stores/user'
import { API_URL } from '@/config'

const { form } = User.setup()

export function Discounts() {
  // ====== stores & states ======
  const shippingStore = useShippingStore()
  const shippingFee = computed(() => shippingStore.shippingFee)

  const discounts = ref([])          // mã hệ thống (đã normalize)
  const userDiscounts = ref([])      // mã cá nhân (đã normalize)

  const discountInput = ref('')
  const selectedDiscount = ref('')   // code đang áp
  const discountInputId = ref(null)  // id để submit (ưu tiên discount_user_id)
  const discountId = ref(null)       // id để submit (ưu tiên discount_user_id)
  const showMoreDiscounts = ref(false)

  const {
    cartItems,
    totalPrice,
    totalQuantity,
    totalPriceItem,
    loadCart
  } = Cart()

  const { allCates, getAllCategory } = FoodList.setup()
  const userStore = useUserStore()

  // ====== helpers: category tree ======
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

  // ====== normalize two sources into one shape ======
  const normalizeSystem = (d) => ({
    id: Number(d.id),                         // discount gốc
    discount_user_id: null,                   // không có cho mã hệ thống
    discount_id: Number(d.id),
    code: d.code,
    name: d.name,
    usage_limit: Number(d.usage_limit ?? 0),
    used: Number(d.used ?? 0),
    discount_type: d.discount_type,           // 'salefood' | 'freeship'
    discount_method: d.discount_method,       // 'percent' | 'amount'
    discount_value: Number(d.discount_value ?? 0),
    min_order_value: Number(d.min_order_value ?? 0),
    category_id: d.category_id ? Number(d.category_id) : null,
    start_date: d.start_date,
    end_date: d.end_date,
    expiry_at: null,
    exchanged_at: null,
    point_used: 0,
    source: d.source ?? 'system',
    max_discount_amount: Number(d.max_discount_amount ?? Number.POSITIVE_INFINITY),
    is_user: false,
  })

  const normalizeUser = (d) => ({
    id: Number(d.discount_id),                // discount gốc
    discount_user_id: Number(d.discount_user_id), // pivot id (QUAN TRỌNG)
    discount_id: Number(d.discount_id),
    code: d.code,
    name: d.name,
    usage_limit: Number(d.usage_limit ?? 0),
    used: Number(d.used ?? 0),
    discount_type: d.discount_type,
    discount_method: d.discount_method,
    discount_value: Number(d.discount_value ?? 0),
    min_order_value: Number(d.min_order_value ?? 0),
    category_id: d.category_id ? Number(d.category_id) : null,
    start_date: d.start_date,
    end_date: d.end_date,
    expiry_at: d.expiry_at,                 
    exchanged_at: d.exchanged_at,
    point_used: Number(d.point_used ?? 0),
    source: d.source ?? 'user',
    max_discount_amount: Number(d.max_discount_amount ?? Number.POSITIVE_INFINITY),
    is_user: true,
  })

  // ====== time/usage guards ======
  const isActive = (d) => {
    const now = new Date()
    const inRange = (!d.start_date || new Date(d.start_date) <= now) &&
                    (!d.end_date   || now <= new Date(d.end_date))
    const notExpired = (!d.expiry_at || now <= new Date(d.expiry_at))
    const underUsageLimit = (d.usage_limit > 0 ? d.used < d.usage_limit : true)
    return inRange && notExpired && underUsageLimit
  }

  // ====== API: system discounts ======
  const getAllDiscount = async (query = {}) => {
    try {
      const params = new URLSearchParams(query).toString()
      const res = await axios.get(`${API_URL}/discounts?${params}`)

      const raw = query.source
        ? res.data.filter(d => d.source === query.source)
        : res.data

      discounts.value = raw.map(normalizeSystem).filter(isActive)
    } catch (error) {
      console.error('Lỗi khi lấy mã giảm giá:', error)
    }
  }

  // ====== API: user vouchers (your /user-vouchers => getUserDiscounts) ======
  const fetchUserDiscounts = async () => {
    try {
      const res = await axios.get(`${API_URL}/user-vouchers`, {
        headers: {
          Authorization: `Bearer ${userStore.token}`,
        },
      })
      console.log('🎁 Vouchers người dùng hiện có:', res.data)
      userDiscounts.value = res.data.map(normalizeUser).filter(isActive)
    } catch (error) {
      console.error(error)
    }
  }

  // ====== merge (prefer user codes) ======
  const allDiscounts = computed(() => {
    const userCodes = new Set(userDiscounts.value.map(d => d.code))
    const systemPart = discounts.value.filter(d => !userCodes.has(d.code))
    return [...userDiscounts.value, ...systemPart]
  })

  // ====== UI helpers ======
  const showToast = (message, icon = 'success') => {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon,
      title: message,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    })
  }

  const applyDiscountCode = (code) => {
    const selected = allDiscounts.value.find((d) => d.code === code)
    if (!selected) {
      showToast('Không tìm thấy mã giảm giá', 'warning')
      return
    }
    selectedDiscount.value = code

    // Ưu tiên discount_user_id nếu có
    const idToUse = selected.discount_user_id ?? selected.discount_id
    discountId.value = idToUse
    discountInputId.value = idToUse

    showMoreDiscounts.value = false
  }

  const handleDiscountInput = () => {
    const code = discountInput.value.trim().toUpperCase()
    const discount = allDiscounts.value.find((d) => d.code === code)

    if (!discount) {
      showToast('Mã giảm giá không hợp lệ', 'error')
      return
    }

    if (totalPrice.value < discount.min_order_value) {
      showToast(`Đơn hàng cần tối thiểu ${discount.min_order_value.toLocaleString()}đ để dùng mã ${code}`, 'warning')
      return
    }

    applyDiscountCode(code)
    discountInput.value = ''
    showToast(`Mã ${code} đã được áp dụng!`, 'success')
  }

  // Đồng bộ id khi selectedDiscount thay đổi
  watchEffect(() => {
    const selected = allDiscounts.value.find((d) => d.code === selectedDiscount.value)
    if (selected) {
      const idToUse = selected.discount_user_id ?? selected.discount_id
      discountId.value = idToUse
      discountInputId.value = idToUse
    }
  })

  // ====== compute discount amounts ======
  const discountFoodAmount = computed(() => {
    const d = allDiscounts.value.find(x => x.code === selectedDiscount.value)
    if (!d || d.discount_type !== 'salefood') return 0

    const value = Number(d.discount_value)
    const max = Number(d.max_discount_amount ?? Number.POSITIVE_INFINITY)
    let amount = 0

    if (d.category_id) {
      const ids = getAllChildCategoryIds(d.category_id)
      const subtotal = cartItems.value
        .filter(item => ids.includes(Number(item.category_id)))
        .reduce((s, item) => s + Number(item.price) * Number(item.quantity), 0)

      if (subtotal < d.min_order_value) return 0
      amount = d.discount_method === 'percent' ? (subtotal * value) / 100 : value
    } else {
      if (totalPrice.value < d.min_order_value) return 0
      amount = d.discount_method === 'percent' ? (totalPrice.value * value) / 100 : value
    }

    return Math.min(amount, max)
  })

  const discountShipAmount = computed(() => {
    const d = allDiscounts.value.find(x => x.code === selectedDiscount.value)
    if (!d || d.discount_type !== 'freeship') return 0

    const value = Number(d.discount_value)
    const max = Number(d.max_discount_amount ?? Number.POSITIVE_INFINITY)
    const amount = d.discount_method === 'percent'
      ? (shippingFee.value * value) / 100
      : value

    return Math.min(amount, max)
  })

  // ====== points ======
  const POINT_TO_VND = 1

  const totalBeforePoints = computed(() =>
    Math.max(
      totalPrice.value - discountFoodAmount.value + shippingFee.value - discountShipAmount.value,
      0
    )
  )

  const pointsDiscountAmount = computed(() => {
    if (!form.use_points) return 0
    const usableMoney = Number(form.usable_points) * POINT_TO_VND
    return Math.min(usableMoney, totalBeforePoints.value)
  })

  const finalTotal = computed(() =>
    Math.max(totalBeforePoints.value - pointsDiscountAmount.value, 0)
  )

  const getImageByType = (type) =>
    type === 'freeship' ? '/img/freeship-icon.png' : '/img/discount-icon.png'

  const formatCurrency = (num) => {
    if (!num) return '0'
    return Number(num).toLocaleString('vi-VN')
  }

  // ====== debug watches ======
  watch(
    () => form.use_points,
    (newValue) => {
      if (newValue) {
        console.log('Đã bật sử dụng Tpoints, giảm:', pointsDiscountAmount.value, 'VND')
      } else {
        console.log('Đã tắt sử dụng Tpoints')
      }
    }
  )

  watchEffect(() => {
    console.log('USE_POINTS:', form.use_points)
    console.log('USABLE_POINTS:', form.usable_points)
    console.log('Tpoints giảm:', pointsDiscountAmount.value)
  })

  // ====== lifecycle ======
  onMounted(async () => {
    await getAllCategory()
    // tuỳ trang mà bạn gọi:
    // await getAllDiscount()
    // if (userStore.token) await fetchUserDiscounts()
  })

  // ====== exposed ======
  return {
    // data
    discounts,
    userDiscounts,
    allDiscounts,

    // fetch
    getAllDiscount,
    fetchUserDiscounts,

    // selection
    discountInput,
    selectedDiscount,
    discountId,
    discountInputId,
    showMoreDiscounts,
    applyDiscountCode,
    handleDiscountInput,

    // cart refs
    cartItems,
    totalPrice,
    totalQuantity,
    totalPriceItem,
    loadCart,

    // computed totals
    discountFoodAmount,
    discountShipAmount,
    finalTotal,
    pointsDiscountAmount,

    // helpers
    getAllChildCategoryIds,
    getImageByType,
    formatCurrency,
  }
}
