import axios from 'axios'
import { ref, onMounted, computed, watchEffect } from 'vue'
import { useRoute } from 'vue-router'
import Swal from 'sweetalert2'
import { API_URL } from '@/config'
export function Cart() {
  const cartItems = ref([])
  const user = JSON.parse(localStorage.getItem('user')) || {}
  const userId = user?.id || 'guest'
  const route = useRoute()
  const orderId = computed(() => route.params.orderId)
  const isAdmin = computed(() => {
    return route.name && String(route.name).includes('admin')
  })
  const quantity = ref(1)
  const isChatbotActive = ref(false)
  // const loadCart = async () => {
  //   const cartKey = isAdmin.value
  //     ? orderId.value
  //       ? `cart_admin_reservation_${orderId.value}`
  //       : `cart_admin_reservation`
  //     : orderId.value
  //       ? `cart_${userId}_reservation_${orderId.value}`
  //       : `cart_${userId}`
  //   const storedCart = localStorage.getItem(cartKey)
  //   cartItems.value = storedCart ? JSON.parse(storedCart) : []
  // }

  const cartKey = computed(() => {
    let key = ''
    if (isAdmin.value) {
      key = orderId.value
        ? `cart_admin_reservation_${orderId.value}`
        : `cart_admin_reservation`
    } else {
      key = orderId.value
        ? `cart_${userId}_reservation_${orderId.value}`
        : `cart_${userId}`
    }

    if (isChatbotActive.value) {
      return `${key}_chatbot`
    }
    return key

  })

  const loadCart = async () => {
    const storedCart = localStorage.getItem(cartKey.value)
    cartItems.value = storedCart ? JSON.parse(storedCart) : []
  }

  const saveCart = () => {
    localStorage.setItem(cartKey.value, JSON.stringify(cartItems.value))
  }

  const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      const basePrice = Number(item.price) * item.quantity
      const toppingPrice =
        item.type === 'food'
          ? item.toppings.reduce((tsum, topping) => {
            return tsum + Number(topping.price) * item.quantity
          }, 0)
          : 0
      return sum + basePrice + toppingPrice
    }, 0)
  })

  const addToCart = (foodDetail, quantity, toppings, isDeal = false) => {
    console.log(isAdmin.value);

    const newCartItem = {
      id: foodDetail.id,
      name: foodDetail.name,
      image: foodDetail.image,
      price: foodDetail.price,
      toppings: toppings,
      quantity: quantity,
      type: foodDetail.type,
      category_id: foodDetail.category_id,
      is_deal: isDeal

    }
    const existingItemIndex = cartItems.value.findIndex(
      (item) =>
        item.id === newCartItem.id &&
        JSON.stringify(item.toppings.sort((a, b) => a.id - b.id)) ===
        JSON.stringify(newCartItem.toppings.sort((a, b) => a.id - b.id)) &&
        item.is_deal === newCartItem.is_deal
    )

    if (existingItemIndex !== -1) {
      cartItems.value[existingItemIndex].quantity += newCartItem.quantity
    } else {
      cartItems.value.push(newCartItem)
    }

    saveCart()
    // toast.success('Đã thêm vào giỏ hàng!')
  }

  const increaseQuantity = () => {
    quantity.value++
  }

  const decreaseQuantity = () => {
    if (quantity.value > 1) quantity.value--
  }
  const decreaseQuantity1 = (index) => {
    if (cartItems.value[index].quantity > 1) {
      cartItems.value[index].quantity--
      saveCart()
    }
  }

  const increaseQuantity2 = (index) => {
    cartItems.value[index].quantity++
    saveCart()
  }

  const removeItem = (index) => {
    Swal.fire({
      title: 'Bạn có chắc chắn?',
      text: 'Xoá món này khỏi giỏ hàng?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Xoá',
      cancelButtonText: 'Huỷ'
    }).then((result) => {
      if (result.isConfirmed) {
        cartItems.value.splice(index, 1)
        saveCart()
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Đã xoá món khỏi giỏ hàng',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        })
      }
    })
  }

  const totalQuantity = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const totalPriceItem = (item) => {
    const itemPrice = Number(item.price) * item.quantity
    const toppingPrice =
      item.type === 'food'
        ? item.toppings.reduce((sum, topping) => {
          return sum + Number(topping.price) * item.quantity
        }, 0)
        : 0
    return itemPrice + toppingPrice
  }
  const clearCart = () => {
    cartItems.value = []
    saveCart()
  }
  // const saveCart = () => {
  //   const cartKey = isAdmin.value
  //     ? orderId.value
  //       ? `cart_admin_reservation_${orderId.value}`
  //       : `cart_admin_reservation`
  //     : orderId.value
  //       ? `cart_${userId}_reservation_${orderId.value}`
  //       : `cart_${userId}`
  //   localStorage.setItem(cartKey, JSON.stringify(cartItems.value))
  // }
  onMounted(() => {
    loadCart()
  })

  return {
    quantity,
    cartItems,
    totalPrice,
    addToCart,
    totalQuantity,
    totalPriceItem,
    loadCart,
    isAdmin,
    saveCart,
    decreaseQuantity,
    increaseQuantity,
    removeItem,
    increaseQuantity2,
    decreaseQuantity1,
    clearCart,
    cartKey,
    isChatbotActive
  }
}
