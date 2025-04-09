import axios from 'axios'
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
import { Modal } from 'bootstrap'
import { computed } from 'vue'

export const FoodList = {
  setup() {
    const foods = ref([])
    const toppings = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})
    const isLoading = ref(false)
    const isDropdownOpen = ref(false)
    const modalElement = ref('')

    // Hàm định dạng số
    const formatNumber = (value) => {
      return numeral(value).format('0,0')
    }

    // Hàm lấy URL ảnh
    const getImageUrl = (image) => {
      return `/img/food/${image}`
    }

    // Hàm lấy tất cả món ăn
    const getFood = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/foods`)
        foods.value = res.data
        foods.value = foods.value.map(food => ({
          ...food,
          quantity: food.quantity || 1
        }))
      } catch (error) {
        console.error("Lỗi khi lấy món ăn:", error)
      }
    }

    const openModal = async (foodId, modalElementId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${foodId}`);
        foodDetail.value = res.data;

        const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${foodId}`);
        toppings.value = res1.data;

        spicyLevel.value = toppings.value.filter(item => item.category_id == 1);
        toppingList.value = toppings.value.filter(item => item.category_id == 2);

        const modalElement = document.getElementById(modalElementId);
        if (modalElement) {
          const modal = new Modal(modalElement);
          modal.show();
        }
        console.log("Opening modal for food ID:", foodId, "with element ID:", modalElementId);

      } catch (error) {
        console.error("Lỗi khi mở modal:", error);
      }
    };


    // Hàm lấy tất cả danh mục
    const getCategory = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/categories`)
        categories.value = res.data
        categories.value.shift() // Loại bỏ mục mặc định nếu cần
      } catch (error) {
        console.error("Lỗi khi lấy danh mục:", error)
      }
    }

    // Hàm lọc món ăn theo danh mục
    const selectedCategoryName = ref('Món Ăn')

    const getFoodByCategory = async (categoryId) => {
      try {
        if (!categoryId) {
          await getFood()
          return;
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
              axios.get(`http://127.0.0.1:8000/api/home/category/${child.id}/food`)
            )
            const childResults = await Promise.all(childRequests)
            childResults.forEach((childRes) => {
              foods.value = [...foods.value, ...childRes.data]
            })
          }
        }
      } catch (error) {
        console.error("Lỗi khi lấy món ăn theo danh mục:", error)
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

    // Hàm toggle dropdown
    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }


    const addToCart = () => {
      const quantityInput = parseInt(document.getElementById('quantityInput')?.value || 1)
      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)

      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)
      const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không rõ'
      const selectedToppingId = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked')).map((el)=>parseInt(el.value))

      const selectedToppingName= toppingList.value
      .filter((topping)=>selectedToppingId.includes(topping.id))
      .map((topping)=>topping.name)

      const selectedToppingprice= toppingList.value
      .filter((topping)=>selectedToppingId.includes(topping.id))
      .map((topping)=>topping.price)

      const cartItem = {
        id: food.value.id,
        name: food.value.name,
        image: food.value.image,
        price: food.value.price,
        spicyLevel: selectedSpicyName,
        toppings: selectedToppingName,
        toppings_price: selectedToppingprice,
        quantity: quantityInput,
      }

      //lấy giỏ hàng từ localStorage
      let cart=JSON.parse(localStorage.getItem('cart1')) || []

      //Tìm xem item có trong giỏ hàng chưa
      const existingItem = cart.findIndex(
        (item) =>
        item.id === cartItem.id &&
        item.spicyLevel === cartItem.spicyLevel &&
        JSON.stringify(item.toppings.sort()) ===  JSON.stringify(cartItem.toppings.sort())
      )

      if(existingItem !== -1){
        cart[existingItem].quantity += 1
      } else {
        cart.push(cartItem)
      }

      localStorage.setItem('cart1', JSON.stringify(cart))
      alert('Đã thêm vào giỏ hàng!')
    }


    // Lấy dữ liệu khi component được mount
    onMounted(() => {
      getFood()
      getCategory()
    })

    return {
      foods,
      toppings,
      categories,
      getFoodByCategory,
      selectedCategoryName,
      foodDetail,
      openModal,
      isLoading,
      isDropdownOpen,
      toggleDropdown,
      spicyLevel,
      toppingList,
      formatNumber,
      getImageUrl,
      flatCategoryList,
      modalElement,
      addToCart,

    }
  },
}
