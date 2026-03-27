<script setup>
defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  type: { type: String, default: 'text' },
  maxlength: { type: Number, default: 255 }
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <label class="input-wrap">
    <span class="input-label">{{ label }}</span>
    <input
      :value="modelValue"
      :type="type"
      :placeholder="placeholder"
      :maxlength="maxlength"
      class="base-input"
      @input="emit('update:modelValue', $event.target.value)"
    />
  </label>
</template>
