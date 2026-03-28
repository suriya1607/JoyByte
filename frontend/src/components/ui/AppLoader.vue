<script setup>
defineProps({
  show: { type: Boolean, default: false }
})
</script>

<template>
  <Transition name="loader-fade">
    <div v-if="show" class="loader-overlay" role="status" aria-label="Loading">
      <div class="loader-ring"></div>
    </div>
  </Transition>
</template>

<style scoped>
.loader-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(26, 41, 64, 0.6);
  backdrop-filter: blur(3px);
}

.loader-ring {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  border: 4px solid rgba(231, 201, 28, 0.2);
  border-top-color: #e7c91c;
  border-right-color: #e7c91c;
  animation: spin 0.75s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loader-fade-enter-active,
.loader-fade-leave-active {
  transition: opacity 0.25s ease;
}
.loader-fade-enter-from,
.loader-fade-leave-to {
  opacity: 0;
}
</style>
