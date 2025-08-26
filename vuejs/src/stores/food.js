import axios from 'axios'
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
import { Modal } from 'bootstrap'
import { computed } from 'vue'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
export const FoodList = {
  setup() {
    const foods = ref([])
    const combos = ref([])
    const categories = ref([])
    const categorys = ref([])
    const allCates = ref([])
    const foodDetail = ref([])
    const toppings = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})
    const isLoading = ref(false)
    const isDropdownOpen = ref(false)
    const selectedCategoryName = ref('Món Ăn')
    const quantity = ref(1)
    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }

    const formatNumber = (value) => {
      return numeral(value).format('0,0')
    }

    const getImageUrl = (image) => {
      return `${STORAGE_URL}/img/food/${image}`
    }
    const getImageMenuUrl = (image) => {
      return `${STORAGE_URL}/img/food/imgmenu/${image}`
    }
    const getCategory = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/categories`)
        categories.value = res.data  .filter(item => item.type === 'food')
        categories.value.shift()
      } catch (error) {
        console.error(error)
      }
    }
    const getCategoryForAdmin = async () => {
      try {
        const res = await axios.get(`${API_URL}/admin-categories`)
        categorys.value = res.data  .filter(item => item.type === 'food')
        categorys.value.shift()
      } catch (error) {
        console.error(error)
      }
    }

    const getAllCategory = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/all-categories`)
        allCates.value = res.data
        allCates.value.shift()
      } catch (error) {
        console.error(error)
      }
    }

    const getFood = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/foods`)
        foods.value = res.data.map((item) => ({ ...item, type: 'food' }))
      } catch (error) {
        console.error(error)
      }
    }

    const getFoodByCategory = async (categoryId) => {
      try {
        isLoading.value = true

        let tempFoods = []

        if (!categoryId) {
          await getFood()
          isLoading.value = false
          return
        }

        if (categoryId == 14) {
          const comboRes = await axios.get(`${API_URL}/home/combos`)
          tempFoods = comboRes.data.map((item) => ({ ...item, type: 'combo' }))
        } else {
          const res = await axios.get(`${API_URL}/home/category/${categoryId}/food`)
          tempFoods = res.data.map((item) => ({ ...item, type: 'food' }))

          const selectedCategory = categories.value.find((c) => c.id == categoryId)
          if (selectedCategory && selectedCategory.children && selectedCategory.children.length) {
            const childRequests = selectedCategory.children.map((child) =>
              axios.get(`${API_URL}/home/category/${child.id}/food`),
            )
            const childResults = await Promise.all(childRequests)
            childResults.forEach((childRes) => {
              const childFoods = childRes.data.map((item) => ({ ...item, type: 'food' }))
              tempFoods = [...tempFoods, ...childFoods]
            })
          }
        }
        console.log('ssss' + categoryId)

        foods.value = tempFoods
      } catch (error) {
        console.error('Lỗi khi lấy món ăn theo danh mục:', error)
      } finally {
        isLoading.value = false
      }
    }

    const getAllCombos = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/combos`)
        combos.value = res.data
      } catch (error) {
        console.error(error)
      }
    }

    const openModal = async (item) => {
      foodDetail.value = {}
      toppings.value = []
      spicyLevel.value = []
      toppingList.value = []
      quantity.value = 1
      try {
        if (item.type === 'food') {
          const res = await axios.get(`${API_URL}/home/food/${item.id}`)
          foodDetail.value = { ...res.data, type: 'food' }

          const res1 = await axios.get(`${API_URL}/home/topping/${item.id}`)
          toppings.value = res1.data

          spicyLevel.value = toppings.value.filter((item) => item.category_id == 15)
          toppingList.value = toppings.value.filter((item) => item.category_id == 16)
          toppingList.value.forEach((item) => {
            item.price = item.price || 0
          })
        } else if (item.type === 'combo') {
          const res = await axios.get(`${API_URL}/home/combo/${item.id}`)
          foodDetail.value = { ...res.data, type: 'combo' }
        }

        console.log('topping' + toppingList.value);
        console.log('topping1' + spicyLevel.value);
        const modalElement = document.getElementById('productModal')
        if (modalElement) {
          const modal = new Modal(modalElement)
          modal.show()
        }
      } catch (error) {
        console.error(error)
      }
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
      await getCategoryForAdmin()
      await getFood()
      await getAllCombos()
      flatCategoryList
    })

    return {
      foods,
      combos,
      categories,
      categorys,
      foodDetail,
      toppings,
      spicyLevel,
      toppingList,
      isLoading,
      isDropdownOpen,
      selectedCategoryName,
      getFoodByCategory,
      openModal,
      toggleDropdown,
      formatNumber,
      getImageUrl,
      flatCategoryList,
      getImageMenuUrl,
      getCategory,
      getCategoryForAdmin,
      getAllCategory,
      allCates,
      quantity
    }
  },
}
