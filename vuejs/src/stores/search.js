
import axios from 'axios'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import numeral from 'numeral'
import { Modal } from 'bootstrap'

export const FoodListSearch= {
  name: 'HomePage',
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
    getImageMenuUrl(image) {
      return `/img/food/imgmenu/${image}`
    },
  },
  setup() {
    const foods = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const toppings = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})

    const isLoading = ref(false)
    const isDropdownOpen = ref(false)
    const selectedCategoryName = ref('Món Ăn')
    const selectedCategoryImage = ref('')

    const currentIndex = ref(0)
    const images = [
      '/img/banner/Banner (1).webp',
      '/img/banner/Banner (2).png',
      '/img/banner/Banner.png',
    ]
    let intervalId = null

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }

    const changeSlide = (direction) => {
      const total = images.length
      currentIndex.value = (currentIndex.value + direction + total) % total
    }

    const getCategory = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/categories`)
        categories.value = res.data
        categories.value.shift()
      } catch (error) {
        console.error(error)
      }
    }

    const getFood = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/foods`)
        foods.value = res.data.map((item) => ({ ...item, type: 'food' }))
      } catch (error) {
        console.error(error)
      }
    }

    const getFoodByCategory = async (categoryId) => {
      try {
        if (!categories.value.length) {
          console.warn('Categories chưa được load.')
          return
        }

        const res = await axios.get(`http://127.0.0.1:8000/api/home/category/${categoryId}/food`)
        let allFoods = res.data.map((item) => ({ ...item, type: 'food' }))

        let parentName = ''
        let childName = ''
        let parentImage = ''

        for (const parent of categories.value) {
          if (parent.id === categoryId) {
            parentName = parent.name
            parentImage = parent.images || ''
            break
          }
          if (parent.children && parent.children.length) {
            const child = parent.children.find((c) => c.id === categoryId)
            if (child) {
              parentName = parent.name
              childName = child.name
              parentImage = child.images || ''
              if(!child.images){
                parentImage = parent.images
              }
              break
            }
          }
        }

        selectedCategoryName.value = childName
          ? `${parentName} > ${childName}`
          : parentName || 'Món Ăn'
        selectedCategoryImage.value = parentImage

        const selectedCategory = categories.value.find((c) => c.id === categoryId)
        if (selectedCategory?.children?.length) {
          const childRequests = selectedCategory.children.map((child) =>
            axios.get(`http://127.0.0.1:8000/api/home/category/${child.id}/food`),
          )
          const childResults = await Promise.all(childRequests)
          childResults.forEach((childRes) => {
            const childFoods = childRes.data.map((item) => ({ ...item, type: 'food' }))
            allFoods = [...allFoods, ...childFoods]
          })
        }

        if (categoryId === 14) {
          const comboRes = await axios.get(`http://127.0.0.1:8000/api/home/combos`)
          const combosWithType = comboRes.data.map((item) => ({ ...item, type: 'combo' }))
          allFoods = [...allFoods, ...combosWithType]
        }

        foods.value = allFoods
      } catch (error) {
        console.error(error)
      }
    }

    const openModal = async (item) => {
      foodDetail.value = {}
      toppings.value = []
      spicyLevel.value = []
      toppingList.value = []
      try {
        if (item.type === 'food') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`)
          foodDetail.value = res.data

          const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`)
          toppings.value = res1.data

          spicyLevel.value = toppings.value.filter((item) => item.category_id == 1)
          toppingList.value = toppings.value.filter((item) => item.category_id == 2)
          toppingList.value.forEach((item) => {
            item.price = item.price || 0
          })
        } else if (item.type === 'combo') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`)
          foodDetail.value = res.data
        }

        const modalElement = document.getElementById('productModal')
        if (modalElement) {
          const modal = new Modal(modalElement)
          modal.show()
        }
      } catch (error) {
        console.error(error)
      }
    }

    const addToCart = () => {
      const user = JSON.parse(localStorage.getItem('user'))
      const userId = user?.id || 'guest'
      const cartKey = `cart_${userId}`

      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)
      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)
      const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không cay'

      const selectedToppingId = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked'),
      ).map((el) => parseInt(el.value))

      const selectedToppings = toppingList.value
        .filter((topping) => selectedToppingId.includes(topping.id))
        .map((topping) => ({
          id: topping.id,
          name: topping.name,
          price: topping.price,
          food_toppings_id: topping.pivot?.id || null,
        }))

      const cartItem = {
        id: foodDetail.value.id,
        name: foodDetail.value.name,
        image: foodDetail.value.image,
        price: foodDetail.value.price,
        spicyLevel: selectedSpicyName,
        toppings: selectedToppings,
        quantity: 1,
      }

      let cart = JSON.parse(localStorage.getItem(cartKey)) || []

      const existingItem = cart.findIndex(
        (item) =>
          item.id === cartItem.id &&
          item.spicyLevel === cartItem.spicyLevel &&
          JSON.stringify(item.toppings.sort()) === JSON.stringify(cartItem.toppings.sort()),
      )

      if (existingItem !== -1) {
        cart[existingItem].quantity += 1
      } else {
        cart.push(cartItem)
      }

      localStorage.setItem(cartKey, JSON.stringify(cart))
      alert('Đã thêm vào giỏ hàng!')
    }

    onMounted(async () => {
      await getCategory()
      await getFood()
      intervalId = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % images.length
      }, 3000)
    })

    onBeforeUnmount(() => {
      clearInterval(intervalId)
    })

    return {
      foods,
      categories,
      foodDetail,
      toppings,
      spicyLevel,
      toppingList,
      isLoading,
      isDropdownOpen,
      selectedCategoryName,
      selectedCategoryImage,
      currentIndex,
      images,
      getFoodByCategory,
      openModal,
      addToCart,
      toggleDropdown,
      changeSlide,
    }
  },
}
