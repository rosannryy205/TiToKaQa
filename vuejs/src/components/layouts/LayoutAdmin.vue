<template>
  <div class="wrapper" :class="{ 'nav-open': sidebar.showSidebar }">

    <side-bar :style="{ background: sidebarBackground }" />
    <div class="main-panel">
      <TopNavbar />
      <div :class="['panel-header', panelHeaderSize]">
        <router-view name="header" />
      </div>
      <div :class="{ content: !$route.meta.hideContent }" >
        <router-view v-slot="{ Component }">
          <transition name="zoomInOut" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </div>
      <TheFooter />
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { ref, onMounted, computed } from 'vue'
import PerfectScrollbar from 'perfect-scrollbar'
import 'perfect-scrollbar/css/perfect-scrollbar.css'
import TopNavbar from '@/components/TopNavbar.vue'
import SideBar from '@/components/Admin/SideBarPlugin/Sidebar.vue'
import TheFooter from '../TheFooter.vue'
import { useSidebarStore } from '@/stores/sidebar'

const sidebar = useSidebarStore()
const sidebarBackground = ref("grey")
const route = useRoute()
const panelHeaderSize = computed(() => {
  return route.meta.headerSize === 'lg' ? 'panel-header-lg' : 'panel-header-sm'
})

function initScrollbar(className) {
  if (document.getElementsByClassName(className).length > 0) {
    new PerfectScrollbar(`.${className}`)
  } else {
    setTimeout(() => {
      initScrollbar(className)
    }, 100)
  }
}

onMounted(() => {
  if (navigator.platform.startsWith('Win')) {
    initScrollbar('sidebar')
    initScrollbar('sidebar-wrapper')
    document.body.classList.add('perfect-scrollbar-on')
  } else {
    document.body.classList.add('perfect-scrollbar-off')
  }
})
</script>


<style lang="scss">
$scaleSize: 0.95;

@keyframes zoomIn95 {
  from {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }

  to {
    opacity: 1;
  }
}

.main-panel .zoomIn {
  animation-name: zoomIn95;
}

@keyframes zoomOut95 {
  from {
    opacity: 1;
  }

  to {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
}

.main-panel .zoomOut {
  animation-name: zoomOut95;
}

/* Hiệu ứng zoom in/out */
.zoomInOut-enter-active,
.zoomInOut-leave-active {
  transition: all 0.3s ease;
}

.zoomInOut-enter-from,
.zoomInOut-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.zoomInOut-enter-to,
.zoomInOut-leave-from {
  opacity: 1;
  transform: scale(1);
}
</style>
