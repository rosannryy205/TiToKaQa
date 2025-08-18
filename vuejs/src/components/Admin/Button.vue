<template>
  <component :is="tag" :type="nativeType" class="btn" :class="classes">
    <span class="btn-label" v-if="$slots.label">
      <slot name="label"></slot>
    </span>

    <slot></slot>

    <span class="btn-label btn-label-right" v-if="$slots.labelRight">
      <slot name="labelRight"></slot>
    </span>
  </component>
</template>

<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
  tag: {
    type: String,
    default: 'button'
  },
  type: {
    type: String,
    default: 'default'
  },
  nativeType: {
    type: String,
    default: 'button'
  },
  round: Boolean,
  simple: Boolean,
  block: Boolean,
  social: Boolean,
  link: Boolean,
  icon: Boolean,
  wide: Boolean,
  size: String
})

const slots = useSlots()

const classes = computed(() => {
  let btnClasses = [
    { 'btn-simple': props.simple },
    { 'btn-icon': props.icon },
    { 'btn-round': props.round },
    { 'btn-block': props.block },
    { 'btn-social': props.social },
    { 'btn-link': props.link },
    { 'btn-wd': props.wide },
    `btn-${props.type}`
  ]
  if (props.size) {
    btnClasses.push(`btn-${props.size}`)
  }
  return btnClasses
})
</script>
