<template>
  <div
    class="swipe-container"
    @mousedown="startDrag"
    @touchstart="startDrag"
    @mousemove="onDrag"
    @touchmove="onDrag"
    @mouseup="endDrag"
    @mouseleave="endDrag"
    @touchend="endDrag"
    :class="{ 'confirmed': confirmed }"
  >
    <div class="swipe-track" :style="{ backgroundColor: color }">
      <div class="swipe-label" v-if="!confirmed" :style="{ color: confirmed ? '#fff' : '#fff' }">{{ label }}</div>
      <div class="swipe-label success" v-else>
        <i class="bi bi-check2-circle me-1"></i> Xác nhận!
      </div>
      <div
        class="swipe-button"
        ref="buttonRef"
        :style="{ transform: `translateX(${position}px)`, backgroundColor: color, opacity: confirmed ? 0.4 : 1 }"
      >
        <i v-if="!confirmed" class="bi bi-arrow-right-circle-fill swipe-icon"></i>
        <i v-else class="bi bi-check-circle-fill swipe-icon success-icon"></i>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  label: { type: String, default: 'Vuốt để xác nhận' },
  color: { type: String, default: '#28a745' }, // Màu nút vuốt
  successColor: { type: String, default: '#198754' }, // Màu sau khi xác nhận
})

const emit = defineEmits(['confirm'])

const dragging = ref(false)
const position = ref(0)
const confirmed = ref(false)
const buttonRef = ref(null)

let startX = 0
let maxWidth = 0

const startDrag = (e) => {
  if (confirmed.value) return
  dragging.value = true
  startX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX
  maxWidth = e.currentTarget.offsetWidth - buttonRef.value.offsetWidth
}

const onDrag = (e) => {
  if (!dragging.value || confirmed.value) return
  const currentX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX
  const diff = currentX - startX
  position.value = Math.max(0, Math.min(diff, maxWidth))
}

const endDrag = () => {
  if (!dragging.value) return
  dragging.value = false
  if (position.value >= maxWidth - 10) {
    confirmed.value = true
    position.value = maxWidth
    setTimeout(() => emit('confirm'), 300) // Delay nhỏ để hiển thị hiệu ứng
  } else {
    position.value = 0
  }
}
</script>

<style scoped>
.swipe-container {
  width: 100%;
  max-width: 480px;
  margin: 0 auto 20px;
  user-select: none;
  transition: background-color 0.3s ease;
}

.swipe-track {
  background: #f0f0f0;
  border-radius: 50px;
  height: 56px;
  position: relative;
  display: flex;
  align-items: center;
  overflow: hidden;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.05);
  transition: background-color 0.4s ease;
}

.swipe-label {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  color: #495057;
  font-weight: 600;
  font-size: 16px;
  pointer-events: none;
  z-index: 1;
  transition: opacity 0.2s ease;
}

.swipe-label.success {
  color: white;
  display: flex;
  align-items: center;
  font-weight: 600;
}

.swipe-button {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  position: relative;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transition: all 0.25s ease;
}

.swipe-icon {
  font-size: 24px;
  color: white;
  transition: transform 0.3s ease;
}

.success-icon {
  color: white;
}

.confirmed .swipe-button {
  pointer-events: none;
}
</style>
