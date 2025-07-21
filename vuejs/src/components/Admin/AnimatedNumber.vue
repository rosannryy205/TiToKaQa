<template>
  <span>{{ animatedNumber }}</span>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import TWEEN from 'tween.js'

const props = defineProps({
  value: {
    type: Number,
    default: 0
  },
  duration: {
    type: Number,
    default: 500
  }
})

const animatedNumber = ref(0)

function animateNumber(newValue, oldValue) {
  const tweenObj = { tweeningNumber: oldValue }

  function animate() {
    if (TWEEN.update()) {
      requestAnimationFrame(animate)
    }
  }

  new TWEEN.Tween(tweenObj)
    .easing(TWEEN.Easing.Quadratic.Out)
    .to({ tweeningNumber: newValue }, props.duration)
    .onUpdate(() => {
      animatedNumber.value = Math.round(tweenObj.tweeningNumber)
    })
    .start()

  animate()
}

onMounted(() => {
  animateNumber(props.value, 0)
})

watch(() => props.value, (newVal, oldVal) => {
  animateNumber(newVal, oldVal)
})
</script>

<style scoped>
/* Optional: styling */
</style>
