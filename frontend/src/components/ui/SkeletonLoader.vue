<template>
  <div class="skeleton-wrapper">
    <div v-for="i in count" :key="i" class="skeleton-item" :class="`variant-${variant}`">
      <div v-if="variant === 'shop'" class="skeleton skeleton-image"></div>
      <div class="skeleton skeleton-text"></div>
      <div class="skeleton skeleton-text short"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 3
  },
  variant: {
    type: String,
    default: 'default',
    validator: (val) => ['default', 'shop', 'product'].includes(val)
  }
})
</script>

<style scoped>
.skeleton-wrapper {
  display: grid;
  gap: 12px;
}

.skeleton-item {
  background: var(--surface);
  border-radius: var(--radius-md);
  padding: 12px;
  box-shadow: var(--shadow);
}

.skeleton-image {
  width: 100%;
  height: 140px;
  margin-bottom: 12px;
}

.skeleton {
  background: linear-gradient(
    90deg,
    #e5e7eb 25%,
    #f3f4f6 50%,
    #e5e7eb 75%
  );
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 4px;
}

.skeleton-text {
  height: 12px;
  margin-bottom: 8px;
}

.skeleton-text.short {
  width: 60%;
  margin-bottom: 0;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
