<script setup>
import { ref } from 'vue'
import PopupBottomRight from '@/components/PopupBottomRight.vue'

const user = ref(JSON.parse(localStorage.getItem('user')) || null)
const isLoggedIn = ref(!!user.value)

function setUser(newUser) {
  user.value = newUser
  isLoggedIn.value = !!newUser
  if (newUser) {
    localStorage.setItem('user', JSON.stringify(newUser))
  } else {
    localStorage.removeItem('user')
  }
}
</script>
<template>
  <router-view></router-view>
  <PopupBottomRight />
</template>

<style>
</style>
