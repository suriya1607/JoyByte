<template>
  <div class="category-filter-wrapper">
    <div class="category-filter">
      <button
        class="category-chip"
        :class="{ active: !modelValue }"
        @click="$emit('update:modelValue', null)"
      >
        All
      </button>
      <button
        v-for="category in categories"
        :key="category.id"
        class="category-chip"
        :class="{ active: modelValue === category.id }"
        @click="$emit('update:modelValue', category.id)"
      >
        {{ category.name }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  categories: {
    type: Array,
    required: true
  },
  modelValue: {
    type: [Number, null],
    default: null
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.category-filter-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  padding: 8px 0;
}

.category-filter {
  display: flex;
  gap: 8px;
  padding: 0 12px;
  min-width: min-content;
}

.category-chip {
  padding: 6px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 20px;
  background: white;
  color: var(--text-muted);
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.category-chip:hover {
  border-color: var(--primary);
  color: var(--primary);
}

.category-chip.active {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}
</style>
