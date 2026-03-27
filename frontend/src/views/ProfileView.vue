<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import AppShell from '../components/layout/AppShell.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import DefaultAvatar from '../components/ui/DefaultAvatar.vue'
import ToggleSwitch from '../components/ui/ToggleSwitch.vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const isLoggedIn = ref(authStore.isAuthenticated)
const name = ref('')
const email = ref('')
const selectedAvatar = ref(null)
const avatarPreview = ref('')
const fileInputRef = ref(null)
const isSaving = ref(false)

watch(
  () => authStore.isAuthenticated,
  (v) => {
    isLoggedIn.value = v
  }
)

const displayName = computed(() => authStore.user?.name || '-')
const displayPhone = computed(() => authStore.user?.phone || authStore.phone || '')
const displayAvatar = computed(() => avatarPreview.value || authStore.user?.avatar_url || '')
console.log(displayAvatar,'displayAvatar')
async function onToggle(next) {
  if (next) {
    toast.message('You are already signed in.')
    isLoggedIn.value = true
    return
  }

  authStore.logout()
  toast.success('Logged out successfully.')
  router.replace('/auth/login')
}

onMounted(async () => {
  try {
    if (authStore.isAuthenticated && !authStore.user) {
      await authStore.fetchMe()
    }

    name.value = authStore.user?.name || ''
    email.value = authStore.user?.email || ''
  } catch {
    // silent: token could be invalid; store handles logout
  }
})

function openFilePicker() {
  fileInputRef.value?.click()
}

function onFileChange(event) {
  const file = event.target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    toast.error('Please select a valid image file.')
    return
  }

  selectedAvatar.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

async function saveProfile() {
  isSaving.value = true
  try {
    const response = await authStore.updateProfile({
      name: name.value.trim(),
      email: email.value.trim(),
      avatarFile: selectedAvatar.value
    })
    toast.success(response.message || 'Profile updated')
    avatarPreview.value = ''
    selectedAvatar.value = null
    router.replace('/app/home')
  } catch (error) {
    const message = error?.response?.data?.message || 'Failed to update profile.'
    toast.error(message)
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <AppShell>
    <BaseCard>
      <div class="profile-header">
        <DefaultAvatar :name="displayName" :phone="displayPhone" :src="displayAvatar" :size="72" />
        <div class="profile-meta">
          <div class="profile-name">{{ displayName }}</div>
          <div class="profile-sub">
            <span v-if="displayPhone">{{ displayPhone }}</span>
            <span v-if="displayPhone" class="dot" />
          </div>
        </div>
      </div>

      <div class="avatar-actions">
        <input
          ref="fileInputRef"
          type="file"
          accept="image/*"
          class="hidden-file-input"
          @change="onFileChange"
        />
        <button type="button" class="nav-btn avatar-action-btn" @click="openFilePicker">Upload photo</button>
      </div>

      <div class="divider" />

      <form class="profile-form" @submit.prevent="saveProfile">
        <BaseInput v-model="name" label="Full Name" placeholder="Enter your name" :maxlength="120" />
        <BaseInput v-model="email" label="Email (optional)" placeholder="you@example.com" type="email" :maxlength="190" />
        <BaseButton type="submit" :loading="isSaving">Save profile</BaseButton>
      </form>

      <div class="divider" />

      <ToggleSwitch
        v-model="isLoggedIn"
        label="Signed in"
        description="Turn off to securely log out of this device."
        @update:modelValue="onToggle"
      />
    </BaseCard>
  </AppShell>
</template>

