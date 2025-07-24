<template>
  <li :class="{ active: isActive }">
    <!-- Menu item có con -->
    <a v-if="isMenu" href="#" class="nav-link-admin sidebar-menu-item" :aria-expanded="!collapsed" data-toggle="collapse"
      @click.prevent="collapseMenu">
      <component v-if="isComponentIcon" :is="link.icon" />
      <i v-else :class="link.icon" />
      <p>{{ link.label }} <b class="caret"></b></p>
    </a>

    <Transition name="collapse">
      <div v-if="$slots.default || isMenu" v-show="!collapsed">
        <ul class="nav links__nav">
          <slot />
        </ul>
      </div>
    </Transition>

    <component v-if="!isMenu && link.to" :is="elementType(link, false)" :to="link.to" :href="link.to" class="nav-link nav-link-admin"
      :target="link.target" :class="{ active: isActive }" @click="linkClick">
      <template v-if="isComponentIcon">
        <component :is="link.icon" class="sidebar-mini-icon" />
      </template>
      <template v-else>
        <i :class="link.icon" />
      </template>
      <p class="sidebar-normal">{{ link.label }}</p>
    </component>

  </li>
</template>


<script setup>
import { computed, ref, onMounted, onBeforeUnmount, inject, provide } from 'vue'
import { useRoute, RouterLink } from 'vue-router'

const props = defineProps({
  link: {
    type: Object,
    default: () => ({ label: '', to: '', children: [], icon: '' }),
  },
  menu: Boolean,
  opened: Boolean
})

const children = ref([])
const collapsed = ref(!props.opened)

const addLink = inject('addLink', null)
const removeLink = inject('removeLink', null)
const autoClose = inject('autoClose', true)

provide('addLink', addChild)
provide('removeLink', removeChild)

const route = useRoute()

const isComponentIcon = computed(() => typeof props.link.icon === 'function' || typeof props.link.icon === 'object')

const isMenu = computed(() => Array.isArray(props.link.children) && props.link.children.length > 0)

// const baseComponent = computed(() => (isMenu.value || props.link.isRoute ? 'li' : RouterLink))

const isActive = computed(() => {
  if (props.link.children && props.link.children.length > 0) {
    return props.link.children.some(child => route.path == child.to)
  }
  if (!props.link.to) return false
  return route.path.startsWith(props.link.to)
})

function addChild(item) {
  children.value.push(item)
}

function removeChild(item) {
  const index = children.value.indexOf(item)
  if (index !== -1) children.value.splice(index, 1)
}

function collapseMenu() {
  collapsed.value = !collapsed.value
}

function elementType(link, isParent = true) {
  if (link.isRoute === false) {
    return isParent ? 'li' : 'a'
  } else {
    return RouterLink
  }
}

function linkClick() {
  if (autoClose && window.innerWidth < 992) {
    const sidebar = document.querySelector('.sidebar')
    if (sidebar) sidebar.classList.remove('nav-open')
  }
}

onMounted(() => {
  if (addLink) addLink({ link: props.link })
  if (props.link.collapsed !== undefined) {
    collapsed.value = props.link.collapsed
  }
  if (props.link.path && window.location.pathname.startsWith(props.link.path)) {
    collapsed.value = false
  }
})

onBeforeUnmount(() => {
  if (removeLink) removeLink({ link: props.link })
})
</script>

<style scoped>
.sidebar-menu-item {
  cursor: pointer;
}

.sidebar ul.links__nav {
  margin-top: 0;
  padding-top: 10px;
}

.collapse-enter-active,
.collapse-leave-active {
  transition: all 0.3s ease;
}

.collapse-enter-from,
.collapse-leave-to {
  opacity: 0;
  height: 0;
  overflow: hidden;
}
</style>
