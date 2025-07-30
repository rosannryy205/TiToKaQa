<template>
  <nav :class="classes" class="navbar navbar-admin navbar-expand-lg">
    <div class="container-fluid">
      <slot></slot>

      <Transition name="collapse">
        <div
          v-show="showNavbar"
          class="collapse navbar-collapse justify-content-end show"
          id="navigation"
        >
          <ul class="navbar-nav">
            <slot name="navbar-menu"></slot>
          </ul>
        </div>
      </Transition>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  showNavbar: Boolean,
  transparent: {
    type: Boolean,
    default: true
  },
  position: {
    type: String,
    default: 'absolute'
  },
  type: {
    type: String,
    default: 'white',
    validator: (value) =>
      ['white', 'default', 'primary', 'danger', 'success', 'warning', 'info'].includes(value)
  }
})

const classes = computed(() => {
  const colorClass = `bg-${props.type}`
  const navPosition = `navbar-${props.position}`

  return [
    {
      'navbar-transparent': !props.showNavbar && props.transparent,
      [colorClass]: props.showNavbar || !props.transparent
    },
    navPosition
  ]
})
</script>

<style scoped>
/* Collapse transition (nếu muốn mượt) */
.collapse-enter-active,
.collapse-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.collapse-enter-from,
.collapse-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
