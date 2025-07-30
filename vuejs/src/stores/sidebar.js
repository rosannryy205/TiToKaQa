import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSidebarStore = defineStore('sidebar', () => {
  const showSidebar = ref(false)

  function toggleSidebar() {
    showSidebar.value = !showSidebar.value
    // console.log('[SidebarStore] showSidebar:', showSidebar.value)
  }

  function hideSidebar() {
    showSidebar.value = false
  }

  return {
    showSidebar,
    toggleSidebar,
    hideSidebar
  }
})
