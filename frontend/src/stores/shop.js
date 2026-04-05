import { defineStore } from 'pinia'
import api from '../lib/api'

export const useShopStore = defineStore('shop', {
  state: () => ({
    categories: [],
    shops: [],
    products: [],
    selectedShop: null,
    selectedCategory: null,
    isLoading: false,
    error: null
  }),

  getters: {
    shopCount: (state) => state.shops.length,
    categoryCount: (state) => state.categories.length,
    productsByCategory: (state) => (categoryId) => {
      if (!categoryId) return state.products
      return state.products.filter((p) => p.category_id === categoryId)
    }
  },

  actions: {
    async fetchCategories() {
      this.isLoading = true
      this.error = null
      try {
        const { data } = await api.get('/categories')
        this.categories = data.data
        localStorage.setItem('jb_categories_cache', JSON.stringify(this.categories))
        return data
      } catch (err) {
        this.error = err?.response?.data?.message || 'Failed to fetch categories'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchShops(location = null) {
      this.isLoading = true
      this.error = null
      try {
        const url = location ? `/shops?location=${location}` : '/shops'
        const { data } = await api.get(url)
        this.shops = data.data
        localStorage.setItem('jb_shops_cache', JSON.stringify(this.shops))
        return data
      } catch (err) {
        this.error = err?.response?.data?.message || 'Failed to fetch shops'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchShopDetail(shopId) {
      this.isLoading = true
      this.error = null
      try {
        const { data } = await api.get(`/shops/${shopId}`)
        this.selectedShop = data.data
        return data
      } catch (err) {
        this.error = err?.response?.data?.message || 'Failed to fetch shop'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchProducts(shopId, categoryId = null) {
      this.isLoading = true
      this.error = null
      try {
        const url = categoryId
          ? `/shops/${shopId}/products?category_id=${categoryId}`
          : `/shops/${shopId}/products`
        const { data } = await api.get(url)
        this.products = data.data
        return data
      } catch (err) {
        this.error = err?.response?.data?.message || 'Failed to fetch products'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    setSelectedCategory(categoryId) {
      this.selectedCategory = categoryId
    },

    clearSelectedShop() {
      this.selectedShop = null
      this.products = []
      this.selectedCategory = null
    }
  }
})
