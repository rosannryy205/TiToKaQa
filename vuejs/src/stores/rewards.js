// src/stores/discount.js
import axios from 'axios'
import { onMounted, ref} from 'vue'
import { useUserStore } from '@/stores/userAuth'
import { API_URL } from '@/config'
export function Rewards() {
const userStore = useUserStore()


const userRewards = ref([])
const getUserRewards = async (query = {}) => {
  try {
    const params = new URLSearchParams(query).toString()
    const res = await axios.get(`${API_URL}/user-rewards?${params}`, {
      headers: {
        Authorization: `Bearer ${userStore.token}`,
      },
    })

    if (query.prize_type) {
      userRewards.value = res.data.filter(d => d.prize_type === query.prize_type)
    } else {
      userRewards.value = res.data
    }
  } catch (error) {
    console.error('Lỗi khi lấy phần thưởng:', error)
  }
}


  onMounted(async () => {
  })
  return {
    userRewards,
    getUserRewards,
  }
}