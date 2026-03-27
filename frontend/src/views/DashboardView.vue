<script setup>
import AppShell from '../components/layout/AppShell.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import branding from '../config/branding'
</script>

<template>
  <AppShell>
    <BaseCard>
      <p class="overline">Sprint 1 Ready</p>
      <h2 class="card-title">You are logged in</h2>
      <p class="card-text">
        {{ branding.appName }} foundation is active with OTP auth, design system, sticky header, and mobile bottom navigation.
      </p>
    </BaseCard>
  </AppShell>
</template>
