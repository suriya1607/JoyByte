<script setup>
import branding from '../../config/branding'
import logoSvg from '../../assets/logo.svg'
import logoPng from '../../assets/logo.png'
</script>

<template>
  <div class="brand-block">
    <img v-if="branding.logoType === 'svg'" :src="logoSvg" :alt="`${branding.appName} logo`" class="brand-logo" />
    <img v-else-if="branding.logoType === 'png'" :src="logoPng" :alt="`${branding.appName} logo`" class="brand-logo" />
    <div class="brand-text">
      <h1 class="brand">{{ branding.appName }}</h1>
      <span class="tagline">{{ branding.tagline }}</span>
    </div>
  </div>
</template>
