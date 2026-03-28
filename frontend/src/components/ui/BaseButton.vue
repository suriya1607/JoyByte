<script setup>
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
    <span v-if="loading" class="btn-spinner" aria-hidden="true"></span>
    <span :style="loading ? 'opacity: 0.6' : ''"><slot /></span>
  </button>
</template>

<style scoped>
.btn-spinner {
  display: inline-block;
  width: 18px;
  height: 18px;
  border: 2.5px solid rgba(255, 255, 255, 0.35);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: btn-spin 0.7s linear infinite;
  flex-shrink: 0;
}

@keyframes btn-spin {
  to { transform: rotate(360deg); }
}

.base-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
</style>
