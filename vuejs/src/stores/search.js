
// import axios from 'axios'
// import { ref, onMounted, onBeforeUnmount } from 'vue'
// import numeral from 'numeral'
// import { Modal } from 'bootstrap'

// export const FoodListSearch= {
//   name: 'HomePage',
//   methods: {
//     formatNumber(value) {
//       return numeral(value).format('0,0')
//     },
//     getImageUrl(image) {
//       return `/img/food/${image}`
//     },
//     getImageMenuUrl(image) {
//       return `/img/food/imgmenu/${image}`
//     },
//   },
//   setup() {
//     const foods = ref([])
//     const categories = ref([])
//     const foodDetail = ref([])
//     const toppings = ref([])
//     const spicyLevel = ref([])
//     const toppingList = ref({})

//     const isLoading = ref(false)
//     const isDropdownOpen = ref(false)
//     const selectedCategoryName = ref('Món Ăn')
//     const selectedCategoryImage = ref('')

//     const currentIndex = ref(0)
//     const images = [
//       '/img/banner/Banner (1).webp',
//       '/img/banner/Banner (2).png',
//       '/img/banner/Banner.png',
//     ]
//     let intervalId = null

//     const toggleDropdown = () => {
//       isDropdownOpen.value = !isDropdownOpen.value
//     }

//     const changeSlide = (direction) => {
//       const total = images.length
//       currentIndex.value = (currentIndex.value + direction + total) % total
//     }

//     const getCategory = async () => {
//       try {
//         const res = await axios.get(`http://127.0.0.1:8000/api/home/categories`)
//         categories.value = res.data
//         categories.value.shift()
//       } catch (error) {
//         console.error(error)
//       }
//     }

//     const getFood = async () => {
//       try {
//         const res = await axios.get(`http://127.0.0.1:8000/api/home/foods`)
//         foods.value = res.data.map((item) => ({ ...item, type: 'food' }))
//       } catch (error) {
//         console.error(error)
//       }
//     }

//     const getFoodByCategory = async (categoryId) => {
//       try {
//         if (!categories.value.length) {
//           console.warn('Categories chưa được load.')
//           return
//         }

//         const res = await axios.get(`http://127.0.0.1:8000/api/home/category/${categoryId}/food`)
//         let allFoods = res.data.map((item) => ({ ...item, type: 'food' }))

//         let parentName = ''
//         let childName = ''
//         let parentImage = ''

//         for (const parent of categories.value) {
//           if (parent.id === categoryId) {
//             parentName = parent.name
//             parentImage = parent.images || ''
//             break
//           }
//           if (parent.children && parent.children.length) {
//             const child = parent.children.find((c) => c.id === categoryId)
//             if (child) {
//               parentName = parent.name
//               childName = child.name
//               parentImage = child.images || ''
//               if(!child.images){
//                 parentImage = parent.images
//               }
//               break
//             }
//           }
//         }

//         selectedCategoryName.value = childName
//           ? `${parentName} > ${childName}`
//           : parentName || 'Món Ăn'
//         selectedCategoryImage.value = parentImage

//         const selectedCategory = categories.value.find((c) => c.id === categoryId)
//         if (selectedCategory?.children?.length) {
//           const childRequests = selectedCategory.children.map((child) =>
//             axios.get(`http://127.0.0.1:8000/api/home/category/${child.id}/food`),
//           )
//           const childResults = await Promise.all(childRequests)
//           childResults.forEach((childRes) => {
//             const childFoods = childRes.data.map((item) => ({ ...item, type: 'food' }))
//             allFoods = [...allFoods, ...childFoods]
//           })
//         }

//         if (categoryId === 14) {
//           const comboRes = await axios.get(`http://127.0.0.1:8000/api/home/combos`)
//           const combosWithType = comboRes.data.map((item) => ({ ...item, type: 'combo' }))
//           allFoods = [...allFoods, ...combosWithType]
//         }

//         foods.value = allFoods
//       } catch (error) {
//         console.error(error)
//       }
//     }

//     const openModal = async (item) => {
//       foodDetail.value = {}
//       toppings.value = []
//       spicyLevel.value = []
//       toppingList.value = []
//       try {
//         if (item.type === 'food') {
//           const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`)
//           foodDetail.value = res.data

//           const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`)
//           toppings.value = res1.data

//           spicyLevel.value = toppings.value.filter((item) => item.category_id == 1)
//           toppingList.value = toppings.value.filter((item) => item.category_id == 2)
//           toppingList.value.forEach((item) => {
//             item.price = item.price || 0
//           })
//         } else if (item.type === 'combo') {
//           const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`)
//           foodDetail.value = res.data
//         }

//         const modalElement = document.getElementById('productModal')
//         if (modalElement) {
//           const modal = new Modal(modalElement)
//           modal.show()
//         }
//       } catch (error) {
//         console.error(error)
//       }
//     }

//     const addToCart = () => {
//       const user = JSON.parse(localStorage.getItem('user'))
//       const userId = user?.id || 'guest'
//       const cartKey = `cart_${userId}`

//       const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)
//       const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)
//       const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không cay'

//       const selectedToppingId = Array.from(
//         document.querySelectorAll('input[name="topping[]"]:checked'),
//       ).map((el) => parseInt(el.value))

//       const selectedToppings = toppingList.value
//         .filter((topping) => selectedToppingId.includes(topping.id))
//         .map((topping) => ({
//           id: topping.id,
//           name: topping.name,
//           price: topping.price,
//           food_toppings_id: topping.pivot?.id || null,
//         }))

//       const cartItem = {
//         id: foodDetail.value.id,
//         name: foodDetail.value.name,
//         image: foodDetail.value.image,
//         price: foodDetail.value.price,
//         spicyLevel: selectedSpicyName,
//         toppings: selectedToppings,
//         quantity: 1,
//       }

//       let cart = JSON.parse(localStorage.getItem(cartKey)) || []

//       const existingItem = cart.findIndex(
//         (item) =>
//           item.id === cartItem.id &&
//           item.spicyLevel === cartItem.spicyLevel &&
//           JSON.stringify(item.toppings.sort()) === JSON.stringify(cartItem.toppings.sort()),
//       )

//       if (existingItem !== -1) {
//         cart[existingItem].quantity += 1
//       } else {
//         cart.push(cartItem)
//       }

//       localStorage.setItem(cartKey, JSON.stringify(cart))
//       alert('Đã thêm vào giỏ hàng!')
//     }

//     onMounted(async () => {
//       await getCategory()
//       await getFood()
//       intervalId = setInterval(() => {
//         currentIndex.value = (currentIndex.value + 1) % images.length
//       }, 3000)
//     })

//     onBeforeUnmount(() => {
//       clearInterval(intervalId)
//     })

//     return {
//       foods,
//       categories,
//       foodDetail,
//       toppings,
//       spicyLevel,
//       toppingList,
//       isLoading,
//       isDropdownOpen,
//       selectedCategoryName,
//       selectedCategoryImage,
//       currentIndex,
//       images,
//       getFoodByCategory,
//       openModal,
//       addToCart,
//       toggleDropdown,
//       changeSlide,
//     }
//   },
// }
// }const searchQuery = ref('')
const searchQuery = ref(''); // Từ khóa tìm kiếm
const suggestions = ref([]); // Danh sách kết quả
const offset = ref(0); // Vị trí bắt đầu
const limit = 5; // Số kết quả mỗi lần
const hasMore = ref(true); // Kiểm tra có còn dữ liệu để tải thêm không
const showSuggestions = ref(false); // Biến để điều khiển dropdown
const wrapperRef = ref(null); // Ref để gắn vào input-wrapper

// Hàm debounce để tránh gọi API quá nhanh
function debounce(fn, delay = 300) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn.apply(this, args), delay);
  };
}

// Hàm xử lý khi người dùng nhập từ khóa tìm kiếm
const handleInput = debounce(() => {
  if (searchQuery.value.trim()) {
    offset.value = 0;
    suggestions.value = [];
    hasMore.value = true;
    showSuggestions.value = true;
    fetchSuggestions();
  } else {
    suggestions.value = [];
    showSuggestions.value = false;
  }
}, 300);
// 300ms debounce

// Hàm lấy dữ liệu từ API
const fetchSuggestions = async () => {
  if (loading.value || !searchQuery.value.trim() || !hasMore.value) return;

  loading.value = true;
  try {
    const res = await axios.get('http://localhost:8000/api/search', {
      params: {
        search: searchQuery.value,
        offset: offset.value,
        limit: limit,
      },
    });

    const results = res.data.results || [];
    const total = res.data.total || 0;

    console.log("Load thêm:", results.length, "offset:", offset.value, "total:", total);

    suggestions.value.push(...results);

    offset.value += results.length;
    hasMore.value = offset.value < total;
  } catch (error) {
    console.error('Lỗi khi fetch gợi ý:', error);
  } finally {
    loading.value = false;
  }
};



// Hàm xử lý cuộn để tải thêm dữ liệu
const handleScroll = (e) => {
  console.log("Đang scroll suggestion dropdown...");
  const el = e.target;
  if (
    el.scrollTop + el.clientHeight >= el.scrollHeight - 10 &&
    hasMore.value &&
    !loading.value
  ) {
    console.log("Gần cuối dropdown, tải thêm...");
    fetchSuggestions();
  }
};





// Hàm xử lý khi người dùng chọn một item trong danh sách gợi ý
const selectItem = (item) => {
  console.log("Selected item:", item);
  searchQuery.value = item.name;
  suggestions.value = [];
  showSuggestions.value = false; // Ẩn dropdown khi chọn item
};

// Hàm tìm kiếm sản phẩm khi người dùng nhấn Enter hoặc submit
const searchProduct = () => {
  if (searchQuery.value.trim()) {
    showSuggestions.value = false;
    router.push({
      path: '/search', // đường dẫn của route
      query: { search: searchQuery.value }
    });
  }
};

// Hàm xử lý khi người dùng click ngoài để ẩn dropdown
const handleClickOutside = (e) => {
  if (wrapperRef.value && !wrapperRef.value.contains(e.target)) {
    showSuggestions.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
