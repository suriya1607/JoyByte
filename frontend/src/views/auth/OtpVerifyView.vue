<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import BaseCard from '../../components/ui/BaseCard.vue'
import BaseInput from '../../components/ui/BaseInput.vue'
import BaseButton from '../../components/ui/BaseButton.vue'
import AppShell from '../../components/layout/AppShell.vue'
import { useAuthStore } from '../../stores/auth'

const otp = ref('')
const authStore = useAuthStore()
const router = useRouter()

const RESEND_COOLDOWN_SECONDS = 60
const resendCooldownLeft = ref(RESEND_COOLDOWN_SECONDS)
let resendIntervalId = null

const canResend = computed(() => resendCooldownLeft.value <= 0 && !authStore.isLoading)

function startResendCooldown() {
  if (resendIntervalId) clearInterval(resendIntervalId)
  resendIntervalId = setInterval(() => {
    resendCooldownLeft.value = Math.max(0, resendCooldownLeft.value - 1)
    if (resendCooldownLeft.value === 0) {
      clearInterval(resendIntervalId)
      resendIntervalId = null
    }
  }, 1000)
}

function initResendCooldownFromStorage() {
  const sentAtRaw = localStorage.getItem('jb_otp_sent_at')
  const sentAt = sentAtRaw ? Number(sentAtRaw) : 0
  if (!sentAt) {
    resendCooldownLeft.value = RESEND_COOLDOWN_SECONDS
    startResendCooldown()
    return
  }
  const elapsed = Math.floor((Date.now() - sentAt) / 1000)
  resendCooldownLeft.value = Math.max(0, RESEND_COOLDOWN_SECONDS - elapsed)
  if (resendCooldownLeft.value > 0) startResendCooldown()
}

async function resendOtp() {
  if (!authStore.phone) {
    toast.error('Missing phone number. Please go back and try again.')
    return
  }

  try {
    const response = await authStore.sendOtp(authStore.phone)
    toast.success(response.message || 'OTP resent')
    resendCooldownLeft.value = RESEND_COOLDOWN_SECONDS
    startResendCooldown()
  } catch (error) {
    const message = error?.response?.data?.message || 'Unable to resend OTP. Please retry.'
    toast.error(message)
  }
}

async function submit() {
  try {
    const response = await authStore.verifyOtp(otp.value.trim())
    toast.success(response.message)
    if (response?.data?.user?.is_profile_completed) {
      router.push('/app/home')
    } else {
      router.push('/app/profile')
    }
  } catch (error) {
    const message = error?.response?.data?.message || 'OTP verification failed.'
    toast.error(message)
  }
}

onMounted(() => initResendCooldownFromStorage())

onBeforeUnmount(() => {
  if (resendIntervalId) clearInterval(resendIntervalId)
})
</script>

<template>
  <AppShell>
    <div class="auth-page">
      <BaseCard>
        <p class="overline">Step 2 of 2</p>
        <h2 class="card-title">Verify OTP</h2>
        <p class="card-text">Enter the 6-digit OTP sent to {{ authStore.phone || 'your number' }}.</p>
        <form class="auth-form" @submit.prevent="submit">
          <BaseInput v-model="otp" label="OTP Code" placeholder="------" type="tel" :maxlength="6" />
          <BaseButton type="submit" :loading="authStore.isLoading">Verify and continue</BaseButton>
        </form>

        <div style="margin-top: 12px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap">
          <button
            type="button"
            class="nav-btn"
            :disabled="!canResend"
            @click="resendOtp"
            style="padding: 10px 12px"
          >
            Resend OTP
            <span v-if="resendCooldownLeft > 0">({{ resendCooldownLeft }}s)</span>
          </button>
        </div>
      </BaseCard>
    </div>
  </AppShell>
</template>
