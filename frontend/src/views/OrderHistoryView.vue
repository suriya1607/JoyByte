<template>
     <AppShell>
  <div class="order-history-view">
    <!-- Header -->
    <div class="history-header">
      <h1>My Orders</h1>
      <div class="header-actions">
        <div class="search-box">
          <input v-model="searchQuery" type="text" placeholder="Search orders..." @input="resetPagination" />
          <span class="search-icon">🔍</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
      <div class="filter-group">
        <label>Status:</label>
        <select v-model="selectedStatus" @change="resetPagination">
          <option value="">All Orders</option>
          <option v-for="status in ORDER_CONFIG.statuses" :key="status.id" :value="status.id">
            {{ status.label }}
          </option>
        </select>
      </div>

      <div class="filter-group">
        <label>Sort by:</label>
        <select v-model="sortBy" @change="resetPagination">
          <option value="created_at">Date (Newest)</option>
          <option value="created_at_asc">Date (Oldest)</option>
          <option value="total_amount">Amount (High to Low)</option>
          <option value="total_amount_asc">Amount (Low to High)</option>
        </select>
      </div>

      <button v-if="hasActiveFilters" @click="clearFilters" class="clear-btn">✕ Clear Filters</button>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="center-content">
      <div class="spinner"></div>
      <p>Loading your orders...</p>
    </div>

    <!-- Empty state -->
    <div v-else-if="!orders || orders.length === 0" class="empty-state">
      <div class="empty-content">
        <div class="empty-icon">📦</div>
        <h2>No Orders Yet</h2>
        <p v-if="hasActiveFilters">No orders match your filters. Try adjusting them.</p>
        <p v-else>Start by placing your first order!</p>
        <router-link to="/app/shop" class="shop-btn">Go to Shop</router-link>
      </div>
    </div>

    <!-- Orders list -->
    <div v-else class="orders-container">
      <div v-for="order in orders" :key="order.id" class="order-card">
        <!-- Card header -->
        <div class="card-header">
          <div class="order-id">Order #{{ order.id }}</div>
          <StatusBadge :status-id="order.status" />
          <div class="order-date">{{ formatDate(order.created_at) }}</div>
        </div>

        <!-- Card body -->
        <div class="card-body">
          <!-- Shop info -->
          <div class="order-section">
            <span class="section-label">Shop:</span>
            <span class="section-value">{{ order.shop?.name || 'Unknown Shop' }}</span>
          </div>

          <!-- Items summary -->
          <div class="order-section">
            <span class="section-label">Items:</span>
            <span class="section-value">{{ order.items_count }} item{{ order.items_count !== 1 ? 's' : '' }}</span>
          </div>

          <!-- Amount -->
          <div class="order-section highlight">
            <span class="section-label">Total:</span>
            <span class="section-value">₹{{ (order.total_amount || 0).toLocaleString() }}</span>
          </div>

          <!-- Payment method -->
          <div class="order-section">
            <span class="section-label">Payment:</span>
            <span class="section-value">{{ getPaymentMethodLabel(order.payment_method) }}</span>
          </div>
        </div>

        <!-- Card footer (actions) -->
        <div class="card-footer">
          <router-link :to="`/app/orders/${order.id}`" class="view-btn">View Details →</router-link>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination-container">
      <button @click="previousPage" :disabled="currentPage === 1" class="pagination-btn">
        ← Previous
      </button>

      <div class="pagination-info">
        Page {{ currentPage }} of {{ totalPages }}
      </div>

      <button @click="nextPage" :disabled="currentPage === totalPages" class="pagination-btn">
        Next →
      </button>
    </div>
  </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useOrdersStore } from '../stores/orders'
import StatusBadge from '../components/StatusBadge.vue'
import { ORDER_CONFIG } from '../config/order'
import AppShell from '../components/layout/AppShell.vue'


const ordersStore = useOrdersStore()

const isLoading = ref(false)
const searchQuery = ref('')
const selectedStatus = ref('')
const sortBy = ref('created_at')
const currentPage = ref(1)
const perPage = 5

const orders = computed(() => ordersStore.orders)
const totalOrders = computed(() => ordersStore.totalOrders)
const totalPages = computed(() => Math.ceil(totalOrders.value / perPage) || 1)

const hasActiveFilters = computed(() => {
  return selectedStatus.value !== '' || searchQuery.value !== ''
})

onMounted(() => {
  fetchOrders()
})

async function fetchOrders() {
  isLoading.value = true
  try {
    const [sortByField, sortOrder] = getSortParams()

    let statusId = selectedStatus.value ? parseInt(selectedStatus.value) : null

    await ordersStore.fetchOrders({
      status: statusId,
      sortBy: sortByField,
      sortOrder: sortOrder,
      page: currentPage.value,
      perPage: perPage,
      search: searchQuery.value,
    })
  } catch (error) {
    console.error('Failed to fetch orders:', error)
  } finally {
    isLoading.value = false
  }
}

function getSortParams() {
  const sortMap = {
    created_at: ['created_at', 'desc'],
    created_at_asc: ['created_at', 'asc'],
    total_amount: ['total_amount', 'desc'],
    total_amount_asc: ['total_amount', 'asc'],
  }
  return sortMap[sortBy.value] || ['created_at', 'desc']
}

function resetPagination() {
  currentPage.value = 1
  fetchOrders()
}

async function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchOrders()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

async function previousPage() {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchOrders()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

function clearFilters() {
  searchQuery.value = ''
  selectedStatus.value = ''
  sortBy.value = 'created_at'
  currentPage.value = 1
  fetchOrders()
}

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

function getPaymentMethodLabel(methodId) {
  const config = ORDER_CONFIG.paymentMethods.find((m) => m.id === methodId)
  return config?.label || 'Unknown'
}
</script>

<style scoped>
.order-history-view {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px 16px;
}

.history-header {
  margin-bottom: 32px;
}

.history-header h1 {
  margin: 0 0 20px 0;
  font-size: 2rem;
  color: #1a1a1a;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.search-box {
  flex: 1;
  position: relative;
  max-width: 400px;
}

.search-box input {
  width: 100%;
  padding: 10px 12px 10px 36px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #ff6b00;
  box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
}

.filters-bar {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 24px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 8px;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-group label {
  font-weight: 600;
  color: #666;
  font-size: 0.9rem;
}

.filter-group select {
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 0.9rem;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-group select:focus {
  outline: none;
  border-color: #ff6b00;
  box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
}

.clear-btn {
  margin-left: auto;
  background: #e5e7eb;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  font-weight: 600;
  color: #666;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-btn:hover {
  background: #d1d5db;
}

.center-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top-color: #ff6b00;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.empty-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.empty-content {
  text-align: center;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
}

.empty-state h2 {
  margin: 0 0 8px 0;
  font-size: 1.5rem;
  color: #1a1a1a;
}

.empty-state p {
  margin: 0 0 16px 0;
  color: #666;
  font-size: 0.95rem;
}

.shop-btn {
  display: inline-block;
  padding: 10px 20px;
  background: #ff6b00;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.shop-btn:hover {
  background: #e55a00;
  transform: translateY(-2px);
}

.orders-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
}

.order-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.2s ease;
}

.order-card:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  border-color: #ff6b00;
}

.card-header {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 16px;
  align-items: center;
  padding: 16px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.order-id {
  font-weight: 700;
  color: #1a1a1a;
}

.order-date {
  text-align: right;
  font-size: 0.9rem;
  color: #666;
}

.card-body {
  padding: 16px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
}

.order-section {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.section-label {
  font-size: 0.8rem;
  color: #666;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.section-value {
  font-size: 1rem;
  font-weight: 600;
  color: #1a1a1a;
}

.order-section.highlight .section-value {
  color: #ff6b00;
  font-size: 1.1rem;
}

.card-footer {
  padding: 12px 16px;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.view-btn {
  display: inline-block;
  padding: 10px 16px;
  background: #ff6b00;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.view-btn:hover {
  background: #e55a00;
  transform: translateX(2px);
}

.pagination-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e5e7eb;
}

.pagination-btn {
  padding: 10px 16px;
  background: #ff6b00;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: #e55a00;
  transform: translateY(-2px);
}

.pagination-btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

.pagination-info {
  font-weight: 600;
  color: #666;
  min-width: 160px;
  text-align: center;
}

@media (max-width: 768px) {
  .filters-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-group {
    width: 100%;
    flex-direction: column;
  }

  .filter-group select {
    width: 100%;
  }

  .clear-btn {
    margin-left: 0;
    width: 100%;
  }

  .card-header {
    grid-template-columns: 1fr;
    text-align: left;
  }

  .order-date {
    text-align: left;
  }

  .card-body {
    grid-template-columns: 1fr;
  }
}
</style>
