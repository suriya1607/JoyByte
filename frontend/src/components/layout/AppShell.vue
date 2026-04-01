<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLogo from '../branding/AppLogo.vue'
import CartBadge from '../ui/CartBadge.vue'
import { useCartStore } from '../../stores/cart'

const route = useRoute()
const cartStore = useCartStore()

const showNav = computed(() => route.path.startsWith('/app'))

onMounted(async () => {
  try {
    await cartStore.fetchCart()
  } catch (err) {
    console.error('Failed to load cart:', err)
  }
})
</script>

<template>
  <div class="app-shell">
    <header class="sticky-header">
      <AppLogo />
    </header>

    <main class="page-content">
      <slot />
    </main>

    <nav v-if="showNav" class="bottom-nav">
      <RouterLink to="/app/home" class="nav-btn" :class="{ active: route.path === '/app/home' }">
        🏪 Home
      </RouterLink>
      <RouterLink to="/app/cart" class="nav-btn cart-btn" :class="{ active: route.path === '/app/cart' }">
        🛒 Cart
        <CartBadge :count="cartStore.cartCount" />
      </RouterLink>
      <RouterLink to="/app/profile" class="nav-btn" :class="{ active: route.path === '/app/profile' }">
        👤 Profile
      </RouterLink>
    </nav>
  </div>
</template>

<style scoped>
.app-shell {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.sticky-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--surface);
  box-shadow: var(--shadow);
  padding: 12px;
}

main {
  flex: 1;
  overflow-y: auto;
}

.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  gap: 0;
  background: var(--surface);
  box-shadow: 0 -2px 10px rgba(26, 41, 64, 0.05);
  border-top: 1px solid #e5e7eb;
  z-index: 99;
}

.nav-btn {
  flex: 1;
  padding: 12px 8px;
  border: none;
  background: none;
  color: var(--text-muted);
  font-size: 12px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
  position: relative;
}

.nav-btn:active {
  background: #f3f4f6;
}

.nav-btn.active {
  color: var(--primary);
}

.cart-btn {
  position: relative;
}
</style>
