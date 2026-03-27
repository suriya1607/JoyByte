<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AppLogo from '../branding/AppLogo.vue'

const route = useRoute()
const showNav = computed(() => route.path.startsWith('/app'))
</script>

<template>
  <div class="app-shell">
    <header class="sticky-header">
      <AppLogo />
    </header>

    <main class="page-content">
      <slot />
    </main>

    <nav v-if="showNav" class="bottom-nav">
      <RouterLink to="/app/home" class="nav-btn" :class="{ active: route.path === '/app/home' }">Home</RouterLink>
      <button type="button" class="nav-btn" disabled>Orders</button>
      <RouterLink to="/app/profile" class="nav-btn" :class="{ active: route.path === '/app/profile' }">Profile</RouterLink>
    </nav>
  </div>
</template>
