import { defineStore } from 'pinia'
import api from '../lib/api'
import { toast } from 'vue-sonner'

const CART_STORAGE_KEY = 'pytrip_cart'

function loadCartFromStorage() {
  try {
    const raw = localStorage.getItem(CART_STORAGE_KEY)
    return raw ? JSON.parse(raw) : []
  } catch {
    return []
  }
}

function saveCartToStorage(items) {
  try {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items))
  } catch {
    // silently fail
  }
}

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: loadCartFromStorage(),
    isLoading: false
  }),

  getters: {
    cartCount: (state) => state.items.reduce((total, item) => total + item.quantity, 0),
    cartTotal: (state) =>
      state.items.reduce((total, item) => total + item.product.price * item.quantity, 0),
    isEmpty: (state) => state.items.length === 0
  },

  actions: {
    async fetchCart() {
      this.isLoading = true
      try {
        const { data } = await api.get('/cart')
        this.items = data.data
        saveCartToStorage(this.items)
        return data
      } catch (err) {
        console.error('Failed to fetch cart:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async addToCart(productId, quantity = 1) {
      this.isLoading = true
      try {
        const { data } = await api.post('/cart/items', {
          product_id: productId,
          quantity
        })
        this.items = data.data
        saveCartToStorage(this.items)
        toast.success('Item added to cart')
        return data
      } catch (err) {
        const message = err?.response?.data?.message || 'Failed to add item to cart'
        toast.error(message)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async updateCartItem(productId, quantity) {
      this.isLoading = true
      try {
        const { data } = await api.put(`/cart/items/${productId}`, { quantity })
        this.items = data.data
        saveCartToStorage(this.items)
        return data
      } catch (err) {
        const message = err?.response?.data?.message || 'Failed to update cart'
        toast.error(message)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async removeFromCart(productId) {
      this.isLoading = true
      try {
        const { data } = await api.delete(`/cart/items/${productId}`)
        this.items = data.data
        saveCartToStorage(this.items)
        toast.success('Item removed from cart')
        return data
      } catch (err) {
        const message = err?.response?.data?.message || 'Failed to remove item'
        toast.error(message)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    clearCart() {
      this.items = []
      saveCartToStorage([])
    }
  }
})
