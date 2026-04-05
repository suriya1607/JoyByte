<template>
  <AppShell>
    <div v-if="shopStore.selectedShop" class="shop-detail">
      <!-- Shop Header -->
      <div class="shop-header">
        <button class="back-button" @click="handleBack">← Back</button>
        <div class="shop-header-image">
          <img :src="shopStore.selectedShop.image_url" :alt="shopStore.selectedShop.name" />
        </div>
        <div class="shop-info">
          <h1>{{ shopStore.selectedShop.name }}</h1>
          <p class="location">📍 {{ shopStore.selectedShop.location }}</p>
          <div class="rating-info">
            <span>⭐ {{ shopStore.selectedShop.rating }}</span>
            <span>{{ shopStore.selectedShop.total_orders }} orders</span>
          </div>
        </div>
      </div>

      <!-- Category Filter -->
      <CategoryFilter
        v-if="shopStore.categories.length"
        :categories="shopStore.categories"
        :model-value="shopStore.selectedCategory"
        @update:model-value="handleCategorySelect"
      />

      <!-- Products Grid -->
      <div class="page-content">
        <div v-if="shopStore.isLoading && !shopStore.products.length" class="products-grid">
          <SkeletonLoader count="6" variant="product" />
        </div>

        <div v-else-if="shopStore.products.length" class="products-grid">
          <ProductCard
            v-for="product in shopStore.products"
            :key="product.id"
            :product="product"
            @add-to-cart="handleAddToCart(product)"
          />
        </div>

        <div v-else class="empty-state">
          <p>No products available</p>
        </div>
      </div>
    </div>

    <div v-else class="loading-state">
      <AppLoader />
    </div>
  </AppShell>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useShopStore } from '../stores/shop'
import { useCartStore } from '../stores/cart'
import AppShell from '../components/layout/AppShell.vue'
import AppLoader from '../components/ui/AppLoader.vue'
import ProductCard from '../components/ui/ProductCard.vue'
import CategoryFilter from '../components/ui/CategoryFilter.vue'
import SkeletonLoader from '../components/ui/SkeletonLoader.vue'

const router = useRouter()
const route = useRoute()
const shopStore = useShopStore()
const cartStore = useCartStore()

onMounted(async () => {
  try {
    const shopId = parseInt(route.params.id)
    await shopStore.fetchCategories()
    await shopStore.fetchShopDetail(shopId)
    await shopStore.fetchProducts(shopId)
  } catch (err) {
    console.error('Failed to load shop details:', err)
    router.push({ name: 'home' })
  }
})

const handleCategorySelect = async (categoryId) => {
  shopStore.setSelectedCategory(categoryId)
  await shopStore.fetchProducts(shopStore.selectedShop.id, categoryId)
}

const handleAddToCart = async (product) => {
  try {
    await cartStore.addToCart(product.id, 1)
  } catch (err) {
    console.error('Failed to add to cart:', err)
  }
}

const handleBack = () => {
  shopStore.clearSelectedShop()
  router.push({ name: 'home' })
}
</script>

<style scoped>
.shop-detail {
  padding-bottom: 80px;
}

.shop-header {
  background: var(--surface);
  padding-bottom: 16px;
}

.back-button {
  background: none;
  border: none;
  color: var(--primary);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  padding: 12px;
}

.shop-header-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.shop-header-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.shop-info {
  padding: 16px 12px;
}

.shop-info h1 {
  margin: 0 0 4px;
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
}

.location {
  margin: 0 0 8px;
  font-size: 12px;
  color: var(--text-muted);
}

.rating-info {
  display: flex;
  gap: 12px;
  font-size: 12px;
  color: var(--text-muted);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 12px;
  padding: 12px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}

.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 400px;
}

@media (max-width: 640px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .shop-header-image {
    height: 160px;
  }
}
</style>
