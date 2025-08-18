<template>
  <component
    :is="tag"
    class="dropdown"
    :class="[
      { show: isOpen },
      { dropdown: direction === 'down' },
      { dropup: direction === 'up' }
    ]"
    aria-haspopup="true"
    :aria-expanded="isOpen"
    @click="toggleDropDown"
    ref="dropdownRef"
  >
    <slot name="title">
      <a
        class="dropdown-toggle nav-link"
        :class="{ 'no-caret': hideArrow }"
        data-toggle="dropdown"
      >
        <i :class="icon"></i>
        <span class="no-icon">{{ title }}</span>
      </a>
    </slot>

    <ul
      class="dropdown-menu"
      :class="[{ 'dropdown-menu-right': position === 'right' }, { show: isOpen }]"
    >
      <slot></slot>
    </ul>
  </component>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  direction: { type: String, default: 'down' },
  title: String,
  icon: String,
  position: String,
  hideArrow: Boolean,
  tag: { type: String, default: 'li' }
})

const emit = defineEmits(['change'])

const isOpen = ref(false)
const dropdownRef = ref(null)

const toggleDropDown = () => {
  isOpen.value = !isOpen.value
  emit('change', isOpen.value)
}

const closeDropDown = () => {
  isOpen.value = false
  emit('change', isOpen.value)
}

// Click outside to close
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropDown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.dropdown {
  list-style-type: none;
}

.dropdown .dropdown-toggle {
  cursor: pointer;
}
</style>
