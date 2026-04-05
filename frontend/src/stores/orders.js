import { defineStore } from 'pinia'
import api from '../lib/api'
import { toast } from 'vue-sonner'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [],
    currentOrder: null,
    trackingInfo: null,
    isLoading: false,
    pagination: {
      current_page: 1,
      total: 0,
      per_page: 10,
      last_page: 1,
    },
  }),

  getters: {
    orderById: (state) => (id) => state.orders.find(o => o.id === id),
    activeOrders: (state) => state.orders.filter(o => o.status < 5),
    completedOrders: (state) => state.orders.filter(o => o.status >= 5),
    totalOrders: (state) => state.pagination.total,
  },

  actions: {
    async fetchOrders(options = {}) {
      this.isLoading = true
      try {
        const {
          status = null,
          sortBy = 'created_at',
          sortOrder = 'desc',
          page = 1,
          perPage = 10,
          search = '',
        } = options

        const params = {
          sort_by: sortBy,
          sort_order: sortOrder,
          per_page: perPage,
          page,
        }

        if (status !== null && status !== '') {
          params.status = status
        }

        if (search) {
          params.search = search
        }

        const { data } = await api.get('/orders', { params })
        this.orders = data.data.data
        this.pagination = {
          current_page: data.data.current_page,
          total: data.data.total,
          per_page: data.data.per_page,
          last_page: data.data.last_page,
        }
        return data.data
      } catch (err) {
        toast.error('Failed to fetch orders')
        console.error('Error fetching orders:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchOrderDetail(orderId) {
      this.isLoading = true
      try {
        const { data } = await api.get(`/orders/${orderId}`)
        this.currentOrder = data.data
        return data.data
      } catch (err) {
        toast.error('Failed to fetch order details')
        console.error('Error fetching order:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async fetchOrderTracking(orderId) {
      this.isLoading = true
      try {
        const { data } = await api.get(`/orders/${orderId}/tracking`)
        this.trackingInfo = data.data
        return data.data
      } catch (err) {
        toast.error('Failed to fetch tracking information')
        console.error('Error fetching tracking:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    async updateOrderStatus(orderId, statusId) {
      this.isLoading = true
      try {
        const { data } = await api.put(`/orders/${orderId}/status`, {
          status: statusId,
        })
        this.currentOrder = data.data
        // Update in list
        const orderIndex = this.orders.findIndex(o => o.id === orderId)
        if (orderIndex !== -1) {
          this.orders[orderIndex] = data.data
        }
        toast.success('Order status updated')
        return data.data
      } catch (err) {
        toast.error('Failed to update order status')
        console.error('Error updating status:', err)
        throw err
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Simulate real-time tracking by polling for updates
     */
    async startTrackingPoll(orderId, intervalMs = 5000) {
      return setInterval(async () => {
        try {
          await this.fetchOrderTracking(orderId)
        } catch (err) {
          console.error('Polling error:', err)
        }
      }, intervalMs)
    },

    stopTrackingPoll(pollId) {
      clearInterval(pollId)
    },
  },
})
