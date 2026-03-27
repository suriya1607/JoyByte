import { defineStore } from 'pinia'
import api from '../lib/api'

function getStoredUser() {
  const raw = localStorage.getItem('jb_user')
  if (!raw) return null
  try {
    return JSON.parse(raw)
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    phone: localStorage.getItem('jb_phone') || '',
    user: getStoredUser(),
    role: localStorage.getItem('role') || '',
    accessToken: localStorage.getItem('jb_access_token') || '',
    isLoading: false
  }),
  getters: {
    isAuthenticated: (state) => Boolean(state.accessToken),
    isProfileCompleted: (state) => Boolean(state.user?.is_profile_completed)
  },
  actions: {
    setPhone(phone) {
      this.phone = phone
      localStorage.setItem('jb_phone', phone)
    },
    async sendOtp(phone) {
      this.isLoading = true
      try {
        const { data } = await api.post('/auth/send-otp', { phone })
        this.phone = phone
        localStorage.setItem('jb_phone', phone)
        localStorage.setItem('jb_otp_sent_at', String(Date.now()))
        return data
      } finally {
        this.isLoading = false
      }
    },
    async verifyOtp(otp) {
      this.isLoading = true
      try {
        const { data } = await api.post('/auth/verify-otp', {
          phone: this.phone,
          otp
        })

        this.accessToken = data.data.access_token
        this.user = data.data.user
        localStorage.setItem('jb_access_token', this.accessToken)
        localStorage.setItem('jb_user', JSON.stringify(this.user))
        return data
      } finally {
        this.isLoading = false
      }
    },
    async fetchMe() {
      const { data } = await api.get('/auth/me')
      this.user = data.data
      localStorage.setItem('jb_user', JSON.stringify(this.user))
      return data
    },
    async updateProfile(payload) {
      const formData = new FormData()
      formData.append('name', payload.name)
      formData.append('email', payload.email || '')
      if (payload.avatarFile) {
        formData.append('avatar', payload.avatarFile)
      }

      const { data } = await api.post('/auth/profile', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      this.user = data.data
      localStorage.setItem('jb_user', JSON.stringify(this.user))
      return data
    },
    logout() {
      this.accessToken = ''
      this.user = null
      this.phone = ''
      localStorage.removeItem('jb_access_token')
      localStorage.removeItem('jb_user')
      localStorage.removeItem('jb_phone')
      localStorage.removeItem('jb_otp_sent_at')
    }
  }
})
