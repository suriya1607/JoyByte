import { defineStore } from 'pinia'
import api from '../lib/api'
import { toast } from 'vue-sonner'

export const useAddressStore = defineStore('address', {
  state: () => ({
    addresses: [],
    isLoading: false,
    selectedAddressId: null,
  }),

  getters: {
    selectedAddress: (state) => {
      return state.addresses.find(addr => addr.id === state.selectedAddressId)
    },
    defaultAddress: (state) => {
      return state.addresses.find(addr => addr.is_default) || state.addresses[0]
    },
  },

  actions: {
    async fetchAddresses() {
      this.isLoading = true
      try {
        const { data } = await api.get('/addresses')
        this.addresses = data.data
        // Auto-select default address if not selected
        if (!this.selectedAddressId && this.defaultAddress) {
          this.selectedAddressId = this.defaultAddress.id
        }
        return data.data
      } catch (err) {
        console.error('Failed to fetch addresses:', err)
        toast.error('Failed to fetch addresses')
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async createAddress(addressData) {
      this.isLoading = true
      try {
        const { data } = await api.post('/addresses', addressData)
        this.addresses.push(data.data)
        // Auto-select newly created address
        this.selectedAddressId = data.data.id
        toast.success('Address created successfully!')
        return data.data
      } catch (err) {
        const errors = err?.response?.data?.errors
        if (errors) {
          const firstError = Object.values(errors)[0]
          toast.error(Array.isArray(firstError) ? firstError[0] : firstError)
        } else {
          toast.error(err?.response?.data?.message || 'Failed to create address')
        }
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async updateAddress(addressId, addressData) {
      this.isLoading = true
      try {
        const { data } = await api.put(`/addresses/${addressId}`, addressData)
        const index = this.addresses.findIndex(addr => addr.id === addressId)
        if (index !== -1) {
          this.addresses[index] = data.data
        }
        toast.success('Address updated successfully!')
        return data.data
      } catch (err) {
        const errors = err?.response?.data?.errors
        if (errors) {
          const firstError = Object.values(errors)[0]
          toast.error(Array.isArray(firstError) ? firstError[0] : firstError)
        } else {
          toast.error(err?.response?.data?.message || 'Failed to update address')
        }
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async deleteAddress(addressId) {
      this.isLoading = true
      try {
        await api.delete(`/addresses/${addressId}`)
        this.addresses = this.addresses.filter(addr => addr.id !== addressId)
        if (this.selectedAddressId === addressId) {
          this.selectedAddressId = this.defaultAddress?.id || null
        }
        toast.success('Address deleted successfully!')
      } catch (err) {
        toast.error(err?.response?.data?.message || 'Failed to delete address')
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async setDefaultAddress(addressId) {
      this.isLoading = true
      try {
        const { data } = await api.post(`/addresses/${addressId}/set-default`)
        // Update all addresses' is_default flag
        this.addresses.forEach(addr => {
          addr.is_default = addr.id === addressId
        })
        toast.success('Address set as default!')
        return data.data
      } catch (err) {
        toast.error(err?.response?.data?.message || 'Failed to set default address')
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async getLocationAddress(latitude, longitude) {
      this.isLoading = true
      try {
        const { data } = await api.post('/addresses/from-coordinates', {
          latitude,
          longitude,
        })
        return data.data
      } catch (err) {
        toast.error('Failed to fetch location details')
        throw err
      } finally {
        this.isLoading = false
      }
    },

    selectAddress(addressId) {
      if (this.addresses.find(addr => addr.id === addressId)) {
        this.selectedAddressId = addressId
      }
    },

    clearSelection() {
      this.selectedAddressId = null
    },
  },
})
