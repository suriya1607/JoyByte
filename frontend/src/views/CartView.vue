<template>
  <AppShell>
    <div class="cart-page">
      <h1 class="cart-title">Shopping Cart</h1>

      <div v-if="cartStore.isEmpty" class="empty-cart">
        <p>Your cart is empty</p>
        <p class="empty-text">Start shopping to add items to your cart</p>
        <BaseButton @click="handleContinueShopping" class="continue-btn"> Continue Shopping </BaseButton>
      </div>

      <div v-else class="page-content cart-content">
        <!-- Cart Items -->
        <div class="cart-items">
          <div v-for="item in cartStore.items" :key="item.product_id" class="cart-item">
            <div class="item-image">
              <img :src="item.product.image_url" :alt="item.product.name" />
            </div>

            <div class="item-details">
              <h3>{{ item.product.name }}</h3>
              <p class="price">{{ formatPrice(item.product.price) }}</p>
            </div>

            <div class="item-controls">
              <button
                @click="handleDecrement(item.product_id)"
                class="control-btn"
                :disabled="cartStore.isLoading"
              >
                −
              </button>
              <span class="quantity">{{ item.quantity }}</span>
              <button
                @click="handleIncrement(item.product_id)"
                class="control-btn"
                :disabled="cartStore.isLoading"
              >
                +
              </button>
              <button
                @click="handleRemove(item.product_id)"
                class="remove-btn"
                :disabled="cartStore.isLoading"
              >
                🗑️
              </button>
            </div>

            <div class="item-subtotal">{{ formatPrice(item.product.price * item.quantity) }}</div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary">
          <div class="summary-row">
            <span>Subtotal:</span>
            <span>{{ formatPrice(cartStore.cartTotal) }}</span>
          </div>
          <div class="summary-row">
            <span>Tax (10%):</span>
            <span>{{ formatPrice(cartStore.cartTotal * 0.1) }}</span>
          </div>
          <div class="summary-row total">
            <span>Total:</span>
            <span>{{ formatPrice(cartStore.cartTotal * 1.1) }}</span>
          </div>
        </div>

        <!-- Checkout Button -->
        <BaseButton class="checkout-btn" @click="handleCheckout"> Proceed to Checkout </BaseButton>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import AppShell from '../components/layout/AppShell.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import formatPrice from '../lib/formatPrice'

const router = useRouter()
const cartStore = useCartStore()

const handleIncrement = async (productId) => {
  const item = cartStore.items.find((i) => i.product_id === productId)
  if (item) {
    await cartStore.updateCartItem(productId, item.quantity + 1)
  }
}

const handleDecrement = async (productId) => {
  const item = cartStore.items.find((i) => i.product_id === productId)
  if (item && item.quantity > 1) {
    await cartStore.updateCartItem(productId, item.quantity - 1)
  }
}

const handleRemove = async (productId) => {
  await cartStore.removeFromCart(productId)
}

const handleCheckout = () => {
  alert('Checkout feature coming soon!')
}

const handleContinueShopping = () => {
  router.push({ name: 'home' })
}
</script>

<style scoped>
.cart-page {
  padding-bottom: 80px;
}

.cart-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text);
  margin: 16px 12px 0;
}

.empty-cart {
  text-align: center;
  padding: 60px 20px;
}

.empty-cart p {
  margin: 0;
  font-size: 14px;
  color: var(--text);
}

.empty-text {
  color: var(--text-muted);
  font-size: 12px;
  margin-top: 8px !important;
}

.continue-btn {
  margin-top: 16px;
}

.cart-content {
  padding-bottom: 100px;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.cart-item {
  display: grid;
  grid-template-columns: 60px 1fr auto auto;
  gap: 12px;
  align-items: center;
  background: var(--surface);
  padding: 12px;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow);
}

.item-image {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-sm);
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details h3 {
  margin: 0 0 4px;
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
}

.price {
  margin: 0;
  font-size: 12px;
  color: var(--primary);
  font-weight: 600;
}

.item-controls {
  display: flex;
  align-items: center;
  gap: 4px;
}

.control-btn {
  width: 28px;
  height: 28px;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  color: var(--text);
  transition: all 0.2s;
}

.control-btn:hover:not(:disabled) {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

.control-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  width: 24px;
  text-align: center;
  font-size: 12px;
  font-weight: 600;
}

.remove-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

.item-subtotal {
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
  min-width: 50px;
  text-align: right;
}

.cart-summary {
  background: var(--surface);
  padding: 16px 12px;
  border-radius: var(--radius-md);
  margin-bottom: 16px;
  box-shadow: var(--shadow);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 8px;
  color: var(--text);
}

.summary-row:last-child {
  margin-bottom: 0;
}

.summary-row.total {
  border-top: 1px solid #e5e7eb;
  padding-top: 8px;
  font-weight: 700;
  color: var(--primary);
}

.checkout-btn {
  width: 100%;
  padding: 12px;
}

@media (max-width: 640px) {
  .cart-item {
    grid-template-columns: 50px 1fr;
  }

  .item-controls,
  .item-subtotal {
    grid-column: 1 / -1;
    margin-top: 8px;
  }

  .item-subtotal {
    justify-self: flex-end;
  }
}
</style>
