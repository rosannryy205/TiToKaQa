import Sidebar from '@/components/Admin/SideBarPlugin/Sidebar.vue'
import SideBarItem from '@/components/Admin/SideBarPlugin/SideBarItem.vue'
import { reactive } from 'vue'

const SidebarStore = reactive({
  showSidebar: false,
  sidebarLinks: [],
  isMinimized: false,
  displaySidebar(value) {
    this.showSidebar = value
  },
  toggleMinimize() {
    document.body.classList.toggle('sidebar-mini')
    const simulateWindowResize = setInterval(() => {
      window.dispatchEvent(new Event('resize'))
    }, 180)

    setTimeout(() => {
      clearInterval(simulateWindowResize)
    }, 1000)

    this.isMinimized = !this.isMinimized
  }
})

export default {
  install(app, options) {
    if (options && options.sidebarLinks) {
      SidebarStore.sidebarLinks = options.sidebarLinks
    }

    // Gắn vào toàn cục (globalProperties)
    app.config.globalProperties.$sidebar = SidebarStore

    // Đăng ký component toàn cục
    app.component('side-bar', Sidebar)
    app.component('sidebar-item', SideBarItem)
  }
}
