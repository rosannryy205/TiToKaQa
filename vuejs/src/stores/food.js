import axios from 'axios'
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
import { Modal } from 'bootstrap'
import { computed } from 'vue'

export const FoodList = {
  setup() {
    const foods = ref([])
    const combos = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const toppings = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})

    const isLoading = ref(false)
    const isDropdownOpen = ref(false)
    const selectedCategoryName = ref('Món Ăn')
    const quantity = ref(1)

    const foodOrderAdmin = ref([])

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }

    const formatNumber = (value) => {
      return numeral(value).format('0,0')
    }

    const getImageUrl = (image) => {
      return `/img/food/${image}`
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
        if (!categoryId) {
          await getFood()
          return
        }
        const res = await axios.get(`http://127.0.0.1:8000/api/home/category/${categoryId}/food`)
        foods.value = res.data
        const selectedCategory = categories.value.find((c) => c.id == categoryId)
        if (categoryId.value == '' || selectedCategory.value == '') {
          getFood()
          return
        }
        if (selectedCategory) {
          selectedCategoryName.value = selectedCategory.name
          if (selectedCategory.children && selectedCategory.children.length) {
            const childRequests = selectedCategory.children.map((child) =>
              axios.get(`http://127.0.0.1:8000/api/home/category/${child.id}/food`),
            )
            const childResults = await Promise.all(childRequests)
            childResults.forEach((childRes) => {
              foods.value = [...foods.value, ...childRes.data]
            })
          }
        }
      } catch (error) {
        console.error('Lỗi khi lấy món ăn theo danh mục:', error)
      }
    }

    const getAllCombos = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/home/combos')
        combos.value = res.data
      } catch (error) {
        console.error(error)
      }
    }

    const openModal = async (item) => {
      isLoading.value = true

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
          isLoading.value = false
        } else if (item.type === 'combo') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`)
          foodDetail.value = res.data
          isLoading.value = false
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

    const getAllFoodWithTopping = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/foods');
        // console.log(res.data); // Log dữ liệu nhận về từ API
        foodOrderAdmin.value = res.data;

        // Khởi tạo lại topping theo từng món ăn
        foodOrderAdmin.value.forEach(food => {
          food.spicyLevelNull = [];
          food.spicyLevelNotNull = [];

          food.toppings.forEach(topping => {
            // console.log(topping.price); // Log giá trị price của mỗi topping
            if (topping.price == null) {
              food.spicyLevelNull.push(topping);
            } else {
              food.spicyLevelNotNull.push(topping);
            }
          });
        });
      } catch (error) {
        console.error('Error fetching data:', error);
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
        quantity: quantity.value,
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

    const increaseQuantity = () => {
      quantity.value++
    }

    const decreaseQuantity = () => {
      if (quantity.value > 1) quantity.value--
    }

    const flatCategoryList = computed(() => {
      const result = []
      categories.value.forEach((parent) => {
        result.push({ id: parent.id, name: parent.name, indent: '' })
        if (parent.children && parent.children.length) {
          parent.children.forEach((child) => {
            result.push({ id: child.id, name: child.name, indent: '   -- ' })
          })
        }
      })
      return result
    })

    onMounted(async () => {
      await getCategory()
      await getFood()
      await getAllCombos()
      await getAllFoodWithTopping()
      flatCategoryList
    })
    // console.log(foodOrderAdmin);

    return {
      foods,
      combos,
      categories,
      foodDetail,
      toppings,
      spicyLevel,
      toppingList,
      isLoading,
      isDropdownOpen,
      selectedCategoryName,
      getFoodByCategory,
      openModal,
      addToCart,
      toggleDropdown,
      formatNumber,
      getImageUrl,
      flatCategoryList,
      increaseQuantity,
      decreaseQuantity,
      quantity,
      getAllFoodWithTopping,
      foodOrderAdmin
    }
  },
}
