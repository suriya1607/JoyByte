<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  label: { type: String, default: '' },
  description: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="toggle-row" role="group">
    <div class="toggle-text">
      <div class="toggle-label">{{ label }}</div>
      <div v-if="description" class="toggle-desc">{{ description }}</div>
    </div>

    <button
      type="button"
      class="toggle"
      :class="{ on: modelValue }"
      :aria-pressed="modelValue ? 'true' : 'false'"
      @click="emit('update:modelValue', !modelValue)"
    >
      <span class="toggle-knob" />
    </button>
  </div>
</template>
