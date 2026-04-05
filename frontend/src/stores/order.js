import { defineStore } from 'pinia'
import api from '../lib/api'
import { toast } from 'vue-sonner'
import { DEFAULT_PAYMENT_METHOD, PAYMENT_METHOD_IDS } from '../config/order'

export const useOrderStore = defineStore('order', {
  state: () => ({
    orders: [],
    currentOrder: null,
    isLoading: false
  }),

  actions: {
    async placeOrder(addressId, paymentMethod = DEFAULT_PAYMENT_METHOD) {
      this.isLoading = true
      try {
        const { data } = await api.post('/orders', {
          address_id: addressId,
          payment_method: paymentMethod
        })
        this.currentOrder = data.data
        toast.success('Order placed successfully! 🎉')
        return data.data
      } catch (err) {
        const errors = err?.response?.data?.errors
        if (errors) {
          const firstError = Object.values(errors)[0]
          toast.error(Array.isArray(firstError) ? firstError[0] : firstError)
        } else {
          toast.error(err?.response?.data?.message || 'Failed to place order')
        }
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchOrders() {
      this.isLoading = true
      try {
        const { data } = await api.get('/orders')
        this.orders = data.data
        return data.data
      } catch (err) {
        console.error('Failed to fetch orders:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchOrder(id) {
      this.isLoading = true
      try {
        const { data } = await api.get(`/orders/${id}`)
        this.currentOrder = data.data
        return data.data
      } catch (err) {
        console.error('Failed to fetch order:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    }
  }
})
