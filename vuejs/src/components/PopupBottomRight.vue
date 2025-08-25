<template>
  <div class="popup-bottom-left" v-if="visible">
    <div class="popup-inner">
      <button class="popup-close" @click="closePopup">×</button>
      <router-link  to="/flashsale" ><img src="/public/img/flsale.png" alt="" class="wiggle-image"></router-link>
      <router-link to="/flashsale" class="popup-button">XEM NGAY ▶</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

const visible = ref(false)
const route = useRoute()

function isHiddenRoute(path) {
  return path.startsWith('/admin') || path.startsWith('/login') || path.startsWith('/register')
}

function checkAndTogglePopup(path) {
  const today = new Date().toDateString()
  const dismissedDate = localStorage.getItem('popupDismissed')

  if (!isHiddenRoute(path) && dismissedDate !== today) {
    visible.value = true
  } else {
    visible.value = false
  }
}

onMounted(() => {
  checkAndTogglePopup(route.path)
})
watch(() => route.path, (newPath) => {
  checkAndTogglePopup(newPath)
})

function closePopup() {
  visible.value = false
  localStorage.setItem('popupDismissed', new Date().toDateString())
}
</script>




<style scoped>
.popup-bottom-left {
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 9999;
}

.popup-inner {
  position: relative;
  text-align: center;
  width: 120px;
  background: transparent;
}

.popup-close {
  position: absolute;
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
  background: white;
  border: 1px solid #ccc;
  color: #c92c3c;
  font-size: 14px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  line-height: 1;
}

.popup-button {
  margin-top: 6px;
  background-color: #c92c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
  font-size: 12px;
  text-decoration: none;
}

@keyframes wiggle {
  0%, 100% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(3deg);
  }
  50% {
    transform: rotate(-3deg);
  }
  75% {
    transform: rotate(2deg);
  }
}

.wiggle-image {
  animation: wiggle 1.5s infinite ease-in-out;
  transform-origin: bottom center;
  display: inline-block;
}
</style>
