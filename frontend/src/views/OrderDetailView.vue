<template>
  <div class="order-detail-view">
    <!-- Header -->
    <div class="detail-header">
      <router-link to="/app/orders" class="back-btn">← Back</router-link>
      <h1>Order {{ orderId }}</h1>
      <div class="header-actions">
        <button @click="refreshTracking" class="refresh-btn" :disabled="isLoadingTracking">
          🔄 Refresh
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="center-content">
      <div class="spinner"></div>
      <p>Loading order details...</p>
    </div>

    <!-- Order not found -->
    <div v-else-if="!order" class="center-content">
      <h2>Order Not Found</h2>
      <p>The order you're looking for doesn't exist.</p>
      <router-link to="/app/orders" class="link-btn">View All Orders</router-link>
    </div>

    <!-- Order details -->
    <div v-else class="detail-content">
      <!-- Tracking section - only if confirmed or later -->
      <div v-if="isTrackingAvailable" class="section">
        <OrderTrackingMap :tracking-info="trackingInfo" :is-loading="isLoadingTracking" />
      </div>

      <!-- Status timeline -->
      <div class="section">
        <div class="section-title">Order Status</div>
        <OrderTimeline :milestones="order.milestones || []" :current-status-id="order.status" />
      </div>

      <!-- Order items -->
      <div class="section">
        <div class="section-title">
          <span>Order Items</span>
          <span class="item-count">({{ order.items?.length || 0 }} items)</span>
        </div>
        <div v-if="order.items?.length" class="items-list">
          <div v-for="item in order.items" :key="item.id" class="item-card">
            <div class="item-image">
              <img src="https://via.placeholder.com/60" :alt="item.product?.name || 'Product'" />
            </div>
            <div class="item-details">
              <div class="item-name">{{ item.product?.name || 'Product' }}</div>
              <div class="item-shop" v-if="item.shop">{{ item.shop.name }}</div>
              <div class="item-price">₹{{ ((item.price || 0) * (item.quantity || 0)).toLocaleString() }}</div>
            </div>
            <div class="item-qty">
              <span class="qty-label">Qty:</span>
              <span class="qty-value">{{ item.quantity || 0 }}</span>
            </div>
          </div>
        </div>
        <div v-else class="empty-state">
          <p>No items in this order</p>
        </div>
      </div>

      <!-- Order summary -->
      <div class="section">
        <div class="section-title">Order Summary</div>
        <div class="summary-grid">
          <div class="summary-item">
            <span class="summary-label">Order ID</span>
            <span class="summary-value">#{{ order.id }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Status</span>
            <StatusBadge :status-id="order.status" />
          </div>
          <div class="summary-item">
            <span class="summary-label">Payment Method</span>
            <span class="summary-value">{{ getPaymentMethodLabel(order.payment_method) }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Order Date</span>
            <span class="summary-value">{{ formatDate(order.created_at) }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Subtotal</span>
            <span class="summary-value">₹{{ (order.subtotal || 0).toLocaleString() }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Delivery Fee</span>
            <span class="summary-value">₹{{ (order.delivery_fee || 0).toLocaleString() }}</span>
          </div>
          <div v-if="order.tax" class="summary-item">
            <span class="summary-label">Tax</span>
            <span class="summary-value">₹{{ (order.tax || 0).toLocaleString() }}</span>
          </div>
          <div class="summary-item highlight">
            <span class="summary-label">Total Amount</span>
            <span class="summary-value">₹{{ (order.total_amount || 0).toLocaleString() }}</span>
          </div>
        </div>
      </div>

      <!-- Delivery address -->
      <div class="section">
        <div class="section-title">Delivery Address</div>
        <div v-if="order.delivery_address" class="address-card">
          <div class="address-header">
            <span class="address-type">📍 Home</span>
            <span class="address-default">Default</span>
          </div>
          <div class="address-content">
            <p class="address-line">{{ order.delivery_address.street || order.delivery_address.line1 || '-' }}</p>
            <p class="address-line">{{ order.delivery_address.city || '-' }}, {{ order.delivery_address.state || '' }}</p>
            <p class="address-line">{{ order.delivery_address.postal_code || order.delivery_address.pincode || '-' }}</p>
            <p class="address-phone">📞 {{ order.delivery_address.phone || '-' }}</p>
            <p v-if="order.delivery_address.notes" class="address-notes">
              <strong>Notes:</strong> {{ order.delivery_address.notes }}
            </p>
          </div>
        </div>
        <div v-else class="empty-state">
          <p>No delivery address found</p>
        </div>
      </div>

      <!-- Payment status -->
      <div class="section">
        <div class="section-title">Payment Status</div>
        <div class="payment-card" :class="`payment-${order.payment_method}`">
          <div class="payment-icon">💳</div>
          <div class="payment-info">
            <div class="payment-status">{{ getPaymentMethodLabel(order.payment_method) }}</div>
            <div class="payment-amount">₹{{ (order.total_amount || 0).toLocaleString() }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useOrdersStore } from '../stores/orders'
import OrderTimeline from '../components/OrderTimeline.vue'
import OrderTrackingMap from '../components/OrderTrackingMap.vue'
import StatusBadge from '../components/StatusBadge.vue'
import { ORDER_CONFIG } from '../config/order'

const route = useRoute()
const ordersStore = useOrdersStore()

const orderId = route.params.id
const isLoading = ref(false)
const isLoadingTracking = ref(false)
const order = ref(null)
const trackingInfo = ref(null)

const isTrackingAvailable = computed(() => {
  // Tracking available for confirmed and beyond
  return order.value && order.value.status >= 2
})

onMounted(async () => {
  await fetchOrderDetail()
  if (isTrackingAvailable.value) {
    await fetchTracking()
  }
})

async function fetchOrderDetail() {
  isLoading.value = true
  try {
    await ordersStore.fetchOrderDetail(orderId)
    order.value = ordersStore.currentOrder
  } catch (error) {
    console.error('Failed to fetch order:', error)
  } finally {
    isLoading.value = false
  }
}

async function fetchTracking() {
  isLoadingTracking.value = true
  try {
    await ordersStore.fetchOrderTracking(orderId)
    trackingInfo.value = ordersStore.currentTracking
  } catch (error) {
    console.error('Failed to fetch tracking:', error)
  } finally {
    isLoadingTracking.value = false
  }
}

async function refreshTracking() {
  if (isTrackingAvailable.value) {
    await fetchTracking()
  }
}

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function getPaymentMethodLabel(methodId) {
  const config = ORDER_CONFIG.paymentMethods.find((m) => m.id === methodId)
  return config?.label || 'Unknown'
}
</script>

<style scoped>
.order-detail-view {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px 16px;
}

.detail-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
  gap: 16px;
}

.back-btn {
  background: none;
  border: none;
  color: #ff6b00;
  font-size: 1.1rem;
  cursor: pointer;
  text-decoration: none;
  font-weight: 600;
  padding: 8px 12px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: #f3f4f6;
}

.detail-header h1 {
  margin: 0;
  font-size: 1.8rem;
  color: #1a1a1a;
  flex: 1;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.refresh-btn {
  background: #ff6b00;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.refresh-btn:hover:not(:disabled) {
  background: #e55a00;
  transform: translateY(-2px);
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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

.link-btn {
  display: inline-block;
  margin-top: 16px;
  padding: 10px 20px;
  background: #ff6b00;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.link-btn:hover {
  background: #e55a00;
}

.detail-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.section {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.item-count {
  font-size: 0.9rem;
  color: #666;
  font-weight: 400;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-card {
  display: grid;
  grid-template-columns: 60px 1fr auto;
  gap: 12px;
  align-items: center;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.item-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.item-image {
  width: 60px;
  height: 60px;
  border-radius: 6px;
  overflow: hidden;
  background: white;
  border: 1px solid #e5e7eb;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-name {
  font-weight: 600;
  color: #1a1a1a;
}

.item-shop {
  font-size: 0.85rem;
  color: #ff6b00;
  font-weight: 500;
}

.item-price {
  font-weight: 600;
  color: #10b981;
}

.item-qty {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.qty-label {
  font-size: 0.8rem;
  color: #666;
}

.qty-value {
  font-weight: 700;
  color: #1a1a1a;
}

.empty-state {
  text-align: center;
  padding: 32px 16px;
  color: #999;
  background: #f9fafb;
  border-radius: 8px;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.summary-item {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.summary-item.highlight {
  background: #fff8f0;
  border-color: #ff6b00;
}

.summary-label {
  font-size: 0.8rem;
  color: #666;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.summary-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a1a;
}

.summary-item.highlight .summary-value {
  color: #ff6b00;
}

.address-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
}

.address-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.address-type {
  font-weight: 600;
  color: #1a1a1a;
}

.address-default {
  background: #10b981;
  color: white;
  font-size: 0.75rem;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: 600;
}

.address-content {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.address-line {
  margin: 0;
  color: #1a1a1a;
  font-weight: 500;
}

.address-phone {
  margin: 8px 0 0 0;
  padding-top: 8px;
  border-top: 1px solid #e5e7eb;
  color: #ff6b00;
  font-weight: 600;
}

.address-notes {
  margin: 8px 0 0 0;
  padding-top: 8px;
  border-top: 1px solid #e5e7eb;
  color: #666;
  font-size: 0.95rem;
}

.payment-card {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
}

.payment-icon {
  font-size: 2rem;
}

.payment-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.payment-status {
  font-size: 0.9rem;
  color: #666;
  font-weight: 500;
}

.payment-amount {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1a1a1a;
}

@media (max-width: 640px) {
  .detail-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .detail-header h1 {
    font-size: 1.4rem;
  }

  .summary-grid {
    grid-template-columns: 1fr;
  }

  .item-card {
    grid-template-columns: 50px 1fr;
  }

  .item-qty {
    grid-column: 1 / -1;
  }
}
</style>
