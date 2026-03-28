<script setup>
import { useAuthStore } from './stores/auth'
import AppLoader from './components/ui/AppLoader.vue'

const authStore = useAuthStore()
</script>

<template>
  <router-view />
  <AppToaster rich-colors position="top-center" />
  <AppLoader :show="authStore.isLoading" />
</template>
