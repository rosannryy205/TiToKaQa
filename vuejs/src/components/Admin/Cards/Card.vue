<template>
  <div class="card" :class="{ 'card-plain': plain }">
    <h6 class="card-category" v-if="$slots.category || category">{{ category }}</h6>

    <div class="card-image" v-if="$slots.image">
      <slot name="image" />
    </div>

    <div class="card-header" :class="headerClasses" v-if="$slots.header || title">
      <slot name="header">
        <h1 v-if="title" class="card-title">{{ title }}</h1>
        <h3 v-if="subTitle" class="card-category">{{ subTitle }}</h3>
        <p v-if="description" class="card-description">{{ description }}</p>
      </slot>
    </div>

    <slot name="raw-content" />

    <div v-if="$slots.default" class="card-body" :class="cardBodyClasses">
      <slot />
    </div>

    <hr v-if="$slots.footer && !noFooterLine" />

    <div class="card-footer" v-if="$slots.footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subTitle: String,
  category: String,
  description: String,
  noFooterLine: Boolean,
  plain: Boolean,
  cardBodyClasses: [String, Object, Array],
  headerClasses: [String, Object, Array]
})
</script>

<style scoped>
/* Tuấn có thể thêm style riêng tại đây nếu cần */
</style>
