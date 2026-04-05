<template>
  <AppShell>
    <div class="success-page">
      <!-- Animated checkmark -->
      <div class="success-hero">
        <div class="success-ring ring-3"></div>
        <div class="success-ring ring-2"></div>
        <div class="success-ring ring-1"></div>
        <div class="success-circle">
          <svg class="checkmark" viewBox="0 0 52 52" width="52" height="52">
            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark-check" fill="none" d="M14 27l7 7 17-17"/>
          </svg>
        </div>
      </div>

      <div class="success-content">
        <h1 class="success-heading">Order Placed! 🎉</h1>
        <p class="success-subtext">Your order has been confirmed and is being prepared.</p>

        <!-- Order ID card -->
        <div v-if="order" class="order-id-card">
          <span class="order-id-label">Order ID</span>
          <span class="order-id-value">#{{ String(order.id).padStart(5, '0') }}</span>
        </div>

        <!-- Delivery estimate -->
        <div class="delivery-estimate">
          <div class="estimate-icon">🛵</div>
          <div class="estimate-info">
            <span class="estimate-label">Estimated Delivery</span>
            <span class="estimate-time">30–45 minutes</span>
          </div>
        </div>

        <!-- Order details -->
        <div v-if="order" class="order-details-card">
          <h2 class="details-title">Order Details</h2>

          <div class="details-meta">
            <div class="meta-row">
              <span class="meta-label">Status</span>
              <span class="status-chip confirmed">{{ order.status_label || 'Confirmed' }}</span>
            </div>
            <div class="meta-row">
              <span class="meta-label">Payment</span>
              <span class="meta-value">Cash on Delivery</span>
            </div>
            <div class="meta-row">
              <span class="meta-label">Shop</span>
              <span class="meta-value">{{ order.shop?.name || '—' }}</span>
            </div>
          </div>

          <div class="details-divider"></div>

          <!-- Items -->
          <div class="details-items">
            <div v-for="item in order.items" :key="item.id" class="detail-item">
              <div class="detail-item-img">
                <img v-if="item.product_image_url" :src="item.product_image_url" :alt="item.product_name" />
                <div v-else class="img-placeholder">📦</div>
              </div>
              <div class="detail-item-info">
                <span class="detail-item-name">{{ item.product_name }}</span>
                <span class="detail-item-qty">× {{ item.quantity }}</span>
              </div>
              <span class="detail-item-price">{{ formatPrice(item.subtotal) }}</span>
            </div>
          </div>

          <div class="details-divider"></div>

          <!-- Totals -->
          <div class="totals">
            <div class="total-row">
              <span>Subtotal</span>
              <span>{{ formatPrice(order.subtotal) }}</span>
            </div>
            <div class="total-row">
              <span>Delivery Fee</span>
              <span>{{ formatPrice(order.delivery_fee) }}</span>
            </div>
            <div class="total-row grand">
              <span>Total Paid</span>
              <span>{{ formatPrice(order.total) }}</span>
            </div>
          </div>

          <!-- Delivery address -->
          <div class="details-divider"></div>
          <div class="delivery-address">
            <h3 class="address-label">📍 Delivering to</h3>
            <p class="address-text">
              {{ order.delivery_address?.name }}<br/>
              {{ order.delivery_address?.line1 }},
              {{ order.delivery_address?.city }} – {{ order.delivery_address?.pincode }}<br/>
              <span class="address-phone">📞 {{ order.delivery_address?.phone }}</span>
            </p>
          </div>
        </div>

        <!-- Loading state -->
        <div v-else-if="orderStore.isLoading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading order details…</p>
        </div>

        <!-- CTA buttons -->
        <div class="cta-buttons">
          <button class="cta-secondary" @click="router.push({ name: 'home' })">
            Continue Shopping
          </button>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useOrderStore } from '../stores/order'
import AppShell from '../components/layout/AppShell.vue'
import formatPrice from '../lib/formatPrice'

const route = useRoute()
const router = useRouter()
const orderStore = useOrderStore()

const order = ref(null)

onMounted(async () => {
  const orderId = route.params.id

  // Use the store's current order if it matches (just placed)
  if (orderStore.currentOrder && orderStore.currentOrder.id == orderId) {
    order.value = orderStore.currentOrder
  } else {
    try {
      order.value = await orderStore.fetchOrder(orderId)
    } catch {
      // fallback to home if not found
      router.push({ name: 'home' })
    }
  }
})
</script>

<style scoped>
.success-page {
  min-height: 100svh;
  background: var(--bg);
  padding-bottom: 40px;
}

/* Hero animation */
.success-hero {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 0 32px;
  position: relative;
}

.success-circle {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), #ff8e60);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 3;
  animation: popIn 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) forwards;
  box-shadow: 0 12px 36px rgba(255, 107, 53, 0.4);
}

.success-ring {
  position: absolute;
  border-radius: 50%;
  border: 2px solid var(--primary);
  animation: ripple 2s ease-out infinite;
}

.ring-1 {
  width: 100px;
  height: 100px;
  opacity: 0.5;
  animation-delay: 0s;
}

.ring-2 {
  width: 130px;
  height: 130px;
  opacity: 0.3;
  animation-delay: 0.3s;
}

.ring-3 {
  width: 160px;
  height: 160px;
  opacity: 0.15;
  animation-delay: 0.6s;
}

@keyframes ripple {
  0% { transform: scale(0.95); opacity: 0.6; }
  100% { transform: scale(1.2); opacity: 0; }
}

@keyframes popIn {
  0% { transform: scale(0); opacity: 0; }
  60% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}

/* Checkmark SVG */
.checkmark {
  stroke: white;
}

.checkmark-circle {
  stroke-dasharray: 166;
  stroke-dashoffset: 166;
  stroke-width: 2;
  stroke: rgba(255,255,255,0.4);
  animation: drawCircle 0.4s ease forwards 0.2s;
}

.checkmark-check {
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  stroke-width: 3.5;
  stroke-linecap: round;
  stroke-linejoin: round;
  animation: drawCheck 0.3s ease forwards 0.5s;
}

@keyframes drawCircle {
  to { stroke-dashoffset: 0; }
}

@keyframes drawCheck {
  to { stroke-dashoffset: 0; }
}

/* Content */
.success-content {
  width: min(600px, 100%);
  margin: 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  animation: fadeUp 0.5s ease 0.3s both;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}

.success-heading {
  font-size: 26px;
  font-weight: 900;
  color: var(--text);
  margin: 0;
  text-align: center;
}

.success-subtext {
  margin: -8px 0 0;
  text-align: center;
  color: var(--text-muted);
  font-size: 14px;
  font-weight: 500;
  line-height: 1.5;
}

/* Order ID */
.order-id-card {
  background: linear-gradient(135deg, var(--primary), #ff8e60);
  border-radius: var(--radius-md);
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: white;
}

.order-id-label {
  font-size: 13px;
  font-weight: 600;
  opacity: 0.85;
}

.order-id-value {
  font-size: 22px;
  font-weight: 900;
  letter-spacing: 0.04em;
}

/* Delivery estimate */
.delivery-estimate {
  display: flex;
  align-items: center;
  gap: 14px;
  background: var(--surface);
  border-radius: var(--radius-md);
  padding: 16px;
  box-shadow: var(--shadow);
}

.estimate-icon {
  font-size: 36px;
  animation: scooter 1.5s ease-in-out infinite alternate;
}

@keyframes scooter {
  from { transform: translateX(-4px); }
  to { transform: translateX(4px); }
}

.estimate-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.estimate-label {
  font-size: 12px;
  color: var(--text-muted);
  font-weight: 600;
}

.estimate-time {
  font-size: 18px;
  font-weight: 800;
  color: var(--text);
}

/* Order details */
.order-details-card {
  background: var(--surface);
  border-radius: var(--radius-md);
  padding: 20px;
  box-shadow: var(--shadow);
}

.details-title {
  font-size: 16px;
  font-weight: 800;
  color: var(--text);
  margin: 0 0 14px;
}

.details-meta {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.meta-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.meta-label {
  color: var(--text-muted);
  font-weight: 600;
}

.meta-value {
  color: var(--text);
  font-weight: 700;
}

.status-chip {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.status-chip.confirmed {
  background: #d1fae5;
  color: #065f46;
}

.details-divider {
  height: 1px;
  background: rgba(26, 41, 64, 0.07);
  margin: 14px 0;
}

/* Detail items */
.details-items {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.detail-item-img {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  overflow: hidden;
  background: var(--secondary);
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.detail-item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.img-placeholder {
  font-size: 20px;
}

.detail-item-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.detail-item-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
}

.detail-item-qty {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 600;
}

.detail-item-price {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
}

/* Totals */
.totals {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--text-muted);
  font-weight: 600;
}

.total-row.grand {
  font-size: 16px;
  font-weight: 800;
  color: var(--primary);
  padding-top: 10px;
  border-top: 1px solid rgba(26, 41, 64, 0.08);
}

/* Address */
.delivery-address {}

.address-label {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
  margin: 0 0 6px;
}

.address-text {
  font-size: 13px;
  color: var(--text-muted);
  margin: 0;
  line-height: 1.6;
  font-weight: 500;
}

.address-phone {
  font-weight: 600;
  color: var(--text);
}

/* Loading */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px;
  color: var(--text-muted);
}

.loading-spinner {
  width: 28px;
  height: 28px;
  border: 3px solid rgba(26, 41, 64, 0.1);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* CTA buttons */
.cta-buttons {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.cta-secondary {
  height: 50px;
  border: 2px solid var(--primary);
  background: transparent;
  color: var(--primary);
  border-radius: var(--radius-sm);
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, transform 0.2s;
  font-family: inherit;
}

.cta-secondary:hover {
  background: var(--primary);
  color: white;
  transform: translateY(-1px);
}
</style>
