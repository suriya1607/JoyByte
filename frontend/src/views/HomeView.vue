<template>
  <AppShell>
    <div class="home">
      <!-- Header -->
      <div class="home-header">
        <h1 class="page-title">Explore Shops</h1>
      </div>

      <!-- Category Filter -->
      <CategoryFilter
        v-if="shopStore.categories.length"
        :categories="shopStore.categories"
        :model-value="shopStore.selectedCategory"
        @update:model-value="handleCategorySelect"
      />

      <!-- Shops List -->
      <div class="page-content">
        <div v-if="shopStore.isLoading && !shopStore.shops.length" class="shops-grid">
          <SkeletonLoader count="6" variant="shop" />
        </div>

        <div v-else-if="shopStore.shops.length" class="shops-grid">
          <ShopCard
            v-for="shop in shopStore.shops"
            :key="shop.id"
            :shop="shop"
            @click="handleShopClick(shop.id)"
          />
        </div>

        <div v-else class="empty-state">
          <p>No shops available</p>
          <p class="empty-text">Check back later for new shops!</p>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useShopStore } from '../stores/shop'
import AppShell from '../components/layout/AppShell.vue'
import ShopCard from '../components/ui/ShopCard.vue'
import CategoryFilter from '../components/ui/CategoryFilter.vue'
import SkeletonLoader from '../components/ui/SkeletonLoader.vue'

const router = useRouter()
const shopStore = useShopStore()

onMounted(async () => {
  try {
    await Promise.all([
      shopStore.fetchCategories(),
      shopStore.fetchShops()
    ])
  } catch (err) {
    console.error('Failed to load home data:', err)
  }
})

const handleCategorySelect = (categoryId) => {
  shopStore.setSelectedCategory(categoryId)
}

const handleShopClick = (shopId) => {
  router.push({ name: 'shop-detail', params: { id: shopId } })
}
</script>

<style scoped>
.home {
  padding-bottom: 80px;
}

.home-header {
  padding: 16px 12px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
}

.page-title {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
}

.shops-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 12px;
  padding: 12px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-state p {
  margin: 0;
  font-size: 14px;
  color: var(--text);
}

.empty-text {
  color: var(--text-muted);
  font-size: 12px;
  margin-top: 8px !important;
}

@media (max-width: 640px) {
  .shops-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
