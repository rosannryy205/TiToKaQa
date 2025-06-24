import { defineStore } from 'pinia'
import router from '@/router'

export const useUserStore = defineStore('userAuth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isLoggedIn: (state) => !!state.token,
  },
  actions: {
    setUser(user, token) {
      this.user = {
        ...user,
        isGuest: user.isGuest ?? false,
      }
      this.token = token
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('token', token)
    },
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')
      router.push('/login')
    },
  },
})
