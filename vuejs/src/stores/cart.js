// src/stores/discount.js
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'
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
  return isAdmin.value
    ? orderId.value
      ? `cart_admin_reservation_${orderId.value}`
      : `cart_admin_reservation`
    : orderId.value
      ? `cart_${userId}_reservation_${orderId.value}`
      : `cart_${userId}`
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

  const addToCart = (foodDetail, quantity, selectedSpicyName, selectedToppings) => {
    const newCartItem = {
      id: foodDetail.id,
      name: foodDetail.name,
      image: foodDetail.image,
      price: foodDetail.price,
      spicyLevel: selectedSpicyName,
      toppings: selectedToppings,
      quantity: quantity, // Sử dụng tham số quantity
      type: foodDetail.type,
    }

    // Trong addToCart
      const existingItemIndex = cartItems.value.findIndex(
        (item) =>
          item.id === newCartItem.id &&
          item.spicyLevel === newCartItem.spicyLevel &&
          // Đảm bảo sắp xếp theo một thuộc tính cố định, ví dụ 'id'
          JSON.stringify(item.toppings.sort((a,b) => a.id - b.id)) === JSON.stringify(newCartItem.toppings.sort((a,b) => a.id - b.id)),
      )

    if (existingItemIndex !== -1) {
      cartItems.value[existingItemIndex].quantity += newCartItem.quantity
    } else {
      cartItems.value.push(newCartItem)
    }

    saveCart()
    toast.success('Đã thêm vào giỏ hàng!')
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
    const confirmed = window.confirm('Bạn có chắc chắn xóa món này khỏi giỏ hàng ?')
    if (confirmed) {
      cartItems.value.splice(index, 1)
      saveCart()
    }
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
    cartKey
  }
}
