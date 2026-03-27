<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import BaseCard from '../../components/ui/BaseCard.vue'
import BaseInput from '../../components/ui/BaseInput.vue'
import BaseButton from '../../components/ui/BaseButton.vue'
import AppShell from '../../components/layout/AppShell.vue'
import { useAuthStore } from '../../stores/auth'
import branding from '../../config/branding'

const phone = ref('')
const authStore = useAuthStore()
const router = useRouter()
const error = ref(null)
async function submit() {
  authStore.isLoading = true
  try {
    if(!phone.value.trim()) {
      toast.error('Phone number is required')
      return
    }
    const response = await authStore.sendOtp(phone.value.trim())
    toast.success(response.message)
    router.push('/auth/verify')
  } catch (error) {
    const message = error?.response?.data?.message || 'Unable to send OTP. Please retry.'
    toast.error(message)
  } finally {
    authStore.isLoading = false
  }
}
</script>

<template>
  <AppShell>
    <div class="auth-page">
      <BaseCard>
        <p class="overline">Welcome to {{ branding.appName }}</p>
        <h2 class="card-title">Sign in with phone</h2>
        <p class="card-text">Login via OTP code.</p>
        <form class="auth-form" @submit.prevent="submit">
          <BaseInput v-model="phone" label="Phone Number" placeholder="9876543210" type="tel" :maxlength="15" />
          <BaseButton type="submit" :loading="authStore.isLoading">Send OTP</BaseButton>
        </form>
      </BaseCard>
    </div>
  </AppShell>
</template>
