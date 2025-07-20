// src/stores/userForm.js
import { reactive } from 'vue'

export const form = reactive({
  fullname: '',
  email: '',
  phone: '',
  address: '',
  avatar: null,
  username: '',
  rank_points: 0,
  usable_points: 0,
  rank: '',
  use_points: false
})
