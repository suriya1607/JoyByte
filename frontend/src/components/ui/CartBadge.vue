<template>
  <div v-if="count > 0" class="cart-badge">{{ count }}</div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    required: true
  }
})
</script>

<style scoped>
.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--primary);
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
}
</style>
