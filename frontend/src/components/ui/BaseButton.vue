<script setup>
import loaderGif from '../../assets/loader.gif'

defineProps({
  type: { type: String, default: 'button' },
  loading: { type: Boolean, default: false },
  block: { type: Boolean, default: true },
  variant: { type: String, default: 'primary' }
})
</script>

<template>
  <button
    :type="type"
    :disabled="loading"
    class="base-btn"
    :class="[variant, { block, 'is-loading': loading }]"
  >
    <img v-if="loading" :src="loaderGif" class="btn-loader" alt="Loading" />
    <slot />
  </button>
</template>
