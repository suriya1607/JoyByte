<script setup>
import { computed } from 'vue'
import defaultAvatar from '../../assets/default-avatar.svg'

const props = defineProps({
  name: { type: String, default: '' },
  phone: { type: String, default: '' },
  size: { type: Number, default: 64 },
  src: { type: String, default: '' }
})

const style = computed(() => ({
  width: `${props.size}px`,
  height: `${props.size}px`
}))

const imageSrc = computed(() => props.src || defaultAvatar)
</script>

<template>
  <div class="avatar" :style="style" :aria-label="`Avatar for ${name || phone || 'user'}`">
    <img :src="imageSrc" :alt="`${name || phone || 'User'} avatar`" class="avatar-image" />
  </div>
</template>
