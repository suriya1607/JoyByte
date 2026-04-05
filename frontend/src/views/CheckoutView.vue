<template>
  <AppShell>
    <div class="checkout-page">
      <!-- Header -->
      <div class="checkout-header">
        <button class="back-btn" @click="router.back()">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
          </svg>
        </button>
        <h1 class="checkout-title">Checkout</h1>
      </div>

      <div class="page-content checkout-content">

        <!-- Order Summary (read-only) -->
        <section class="section">
          <h2 class="section-title">
            <span class="section-icon">🛍️</span>
            Order Summary
          </h2>
          <div class="summary-card">
            <div v-for="item in cartStore.items" :key="item.product_id" class="summary-item">
              <div class="summary-item-img">
                <img :src="item.product.image_url" :alt="item.product.name" />
              </div>
              <div class="summary-item-info">
                <span class="summary-item-name">{{ item.product.name }}</span>
                <span class="summary-item-qty">× {{ item.quantity }}</span>
              </div>
              <span class="summary-item-price">{{ formatPrice(item.product.price * item.quantity) }}</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row">
              <span>Subtotal</span>
              <span>{{ formatPrice(cartStore.cartTotal) }}</span>
            </div>
            <div class="summary-row">
              <span>Delivery Fee</span>
              <span>{{ formatPrice(DELIVERY_FEE) }}</span>
            </div>
            <div class="summary-row grand-total">
              <span>Total</span>
              <span>{{ formatPrice(cartStore.cartTotal + DELIVERY_FEE) }}</span>
            </div>
          </div>
        </section>

        <!-- Delivery Address -->
        <section class="section">
          <h2 class="section-title">
            <span class="section-icon">📍</span>
            Delivery Address
          </h2>
          <div class="form-card">
            <AddressSelector v-model="selectedAddressId" />
          </div>
        </section>

        <!-- Payment Method -->
        <section class="section">
          <h2 class="section-title">
            <span class="section-icon">💳</span>
            Payment Method
          </h2>
          <div class="payment-card selected">
            <div class="payment-icon">💵</div>
            <div class="payment-info">
              <h3>Cash on Delivery</h3>
              <p>Pay when your order arrives</p>
            </div>
            <div class="payment-check">✓</div>
          </div>
        </section>

      </div>

      <!-- Sticky Bottom Bar -->
      <div class="checkout-bar">
        <div class="checkout-bar-total">
          <span class="checkout-bar-label">Total</span>
          <span class="checkout-bar-amount">{{ formatPrice(cartStore.cartTotal + DELIVERY_FEE) }}</span>
        </div>
        <button
          id="place-order-btn"
          class="place-order-btn"
          :disabled="orderStore.isLoading || !selectedAddressId"
          @click="handlePlaceOrder"
        >
          <span v-if="orderStore.isLoading" class="btn-spinner"></span>
          <span v-else>Place Order 🚀</span>
        </button>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { useOrderStore } from '../stores/order'
import { useAddressStore } from '../stores/address'
import AppShell from '../components/layout/AppShell.vue'
import AddressSelector from '../components/AddressSelector.vue'
import formatPrice from '../lib/formatPrice'
import { DEFAULT_DELIVERY_FEE as DELIVERY_FEE, DEFAULT_PAYMENT_METHOD } from '../config/order'

const router = useRouter()
const cartStore = useCartStore()
const orderStore = useOrderStore()
const addressStore = useAddressStore()

const selectedAddressId = ref(null)

onMounted(async () => {
  await addressStore.fetchAddresses()
})

async function handlePlaceOrder() {
  if (!selectedAddressId.value) {
    alert('Please select a delivery address')
    return
  }

  try {
    const order = await orderStore.placeOrder(selectedAddressId.value, DEFAULT_PAYMENT_METHOD)
    cartStore.clearCart()
    router.push({ name: 'order-success', params: { id: order.id } })
  } catch {
    // toast shown in store
  }
}
</script>

<style scoped>
.checkout-page {
  padding-bottom: 90px;
}

.checkout-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(26, 41, 64, 0.08);
  position: sticky;
  top: 0;
  z-index: 10;
}

.back-btn {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  border: 1px solid rgba(26, 41, 64, 0.1);
  background: var(--surface);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text);
  transition: background 0.2s, transform 0.2s;
}

.back-btn:hover {
  background: var(--secondary);
  transform: translateX(-2px);
}

.checkout-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--text);
  margin: 0;
}

.checkout-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding-top: 20px !important;
}

.section {}

.section-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 15px;
  font-weight: 800;
  color: var(--text);
  margin: 0 0 12px;
}

.section-icon {
  font-size: 18px;
}

/* Summary */
.summary-card {
  background: var(--surface);
  border-radius: var(--radius-md);
  padding: 16px;
  box-shadow: var(--shadow);
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 0;
}

.summary-item-img {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  background: var(--secondary);
}

.summary-item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.summary-item-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.summary-item-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
}

.summary-item-qty {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 600;
}

.summary-item-price {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
}

.summary-divider {
  height: 1px;
  background: rgba(26, 41, 64, 0.08);
  margin: 12px 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--text-muted);
  font-weight: 600;
  margin-bottom: 8px;
}

.summary-row:last-child {
  margin-bottom: 0;
}

.grand-total {
  border-top: 1px solid rgba(26, 41, 64, 0.08);
  padding-top: 10px;
  font-size: 15px;
  font-weight: 800;
  color: var(--primary);
}

/* Address form */
.form-card {
  background: var(--surface);
  border-radius: var(--radius-md);
  padding: 16px;
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.input-label {
  font-size: 12px;
  font-weight: 700;
  color: var(--text);
}

.optional {
  font-weight: 500;
  color: var(--text-muted);
}

.base-input {
  width: 100%;
  border-radius: var(--radius-sm);
  border: 1.5px solid #e2e8f0;
  padding: 10px 14px;
  font-size: 14px;
  font-weight: 500;
  font-family: inherit;
  color: var(--text);
  background: var(--bg);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.base-input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.12);
}

.base-input.input-error {
  border-color: #ef4444;
}

.textarea {
  resize: none;
  height: auto;
}

.error-msg {
  font-size: 11px;
  color: #ef4444;
  font-weight: 600;
}

/* Payment */
.payment-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: var(--surface);
  border: 2px solid transparent;
  border-radius: var(--radius-md);
  padding: 16px;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-shadow: var(--shadow);
}

.payment-card.selected {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.payment-icon {
  font-size: 28px;
  width: 48px;
  height: 48px;
  background: #fff3ef;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.payment-info {
  flex: 1;
}

.payment-info h3 {
  margin: 0 0 2px;
  font-size: 14px;
  font-weight: 700;
  color: var(--text);
}

.payment-info p {
  margin: 0;
  font-size: 12px;
  color: var(--text-muted);
  font-weight: 500;
}

.payment-check {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  font-size: 14px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Bottom bar */
.checkout-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--surface);
  border-top: 1px solid rgba(26, 41, 64, 0.08);
  padding: 12px 16px;
  padding-bottom: max(12px, env(safe-area-inset-bottom));
  display: flex;
  align-items: center;
  gap: 16px;
  z-index: 30;
  box-shadow: 0 -6px 24px rgba(26, 41, 64, 0.08);
}

.checkout-bar-total {
  display: flex;
  flex-direction: column;
}

.checkout-bar-label {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 600;
}

.checkout-bar-amount {
  font-size: 20px;
  font-weight: 800;
  color: var(--text);
}

.place-order-btn {
  flex: 1;
  height: 50px;
  background: var(--primary);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
  letter-spacing: 0.02em;
}

.place-order-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(255, 107, 53, 0.35);
}

.place-order-btn:active {
  transform: translateY(0);
}

.place-order-btn:disabled {
  opacity: 0.7;
  cursor: wait;
}

.btn-spinner {
  width: 20px;
  height: 20px;
  border: 2.5px solid rgba(255, 255, 255, 0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 400px) {
  .input-row {
    grid-template-columns: 1fr;
  }
}
</style>
