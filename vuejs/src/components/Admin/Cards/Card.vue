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

<style lang="scss" scoped>
@use "../../../assets/sass/now-ui-dashboard/variables" as *;

h1, .h1 {
    font-size: $font-size-h1;
    line-height: 1.15;
    margin-bottom: $margin-base-vertical * 2;

    small{
        font-weight: $font-weight-bold;
        text-transform: uppercase;
        opacity: .8;
    }
}
h3, .h3{
    font-size: $font-size-h3;
    margin-bottom: $margin-base-vertical * 2;
    line-height: 1.4em;
}
h4, .h4{
    font-size: $font-size-h4;
    line-height: 1.45em;
    margin-top: $margin-base-vertical * 2;
    margin-bottom: $margin-base-vertical;
}
h5, .h5 {
    font-size: $font-size-h5;
    line-height: 1.4em;
    margin-bottom: 15px;
}
h6, .h6{
    font-size: $font-size-h6;
    font-weight: $font-weight-bold;
    text-transform: uppercase;
}
p{
    &.description{
        font-size: 1.14em;
    }
}

.title{
    font-weight: $font-weight-bold;

    &.title-up{
        text-transform: uppercase;

        a{
            color: $black-color;
            text-decoration: none;
        }
    }
    & + .category{
        margin-top: -10px;
    }
}

.description,
.card-description,
.footer-big p,
.card .footer .stats{
    color: $dark-gray;
    font-weight: $font-weight-light;
}
.category,
.card-category{
    text-transform: capitalize;
    font-weight: $font-weight-normal;
    color: $dark-gray;
    font-size: $font-size-mini;
}
</style>
