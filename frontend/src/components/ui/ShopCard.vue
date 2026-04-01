<template>
  <div class="shop-card" @click="$emit('click')">
    <div class="shop-card-image">
      <img :src="shop.image_url" :alt="shop.name" />
    </div>
    <div class="shop-card-content">
      <h3 class="shop-card-title">{{ shop.name }}</h3>
      <p class="shop-card-location">📍 {{ shop.location }}</p>
      <div class="shop-card-footer">
        <div class="rating">⭐ {{ shop.rating }}</div>
        <div class="orders">{{ shop.total_orders }} orders</div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  shop: {
    type: Object,
    required: true
  }
})

defineEmits(['click'])
</script>

<style scoped>
.shop-card {
  background: var(--surface);
  border-radius: var(--radius-md);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  box-shadow: var(--shadow);
}

.shop-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 35px rgba(26, 41, 64, 0.15);
}

.shop-card-image {
  width: 100%;
  height: 160px;
  overflow: hidden;
}

.shop-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.shop-card-content {
  padding: 12px;
}

.shop-card-title {
  margin: 0 0 4px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
}

.shop-card-location {
  margin: 0 0 8px;
  font-size: 12px;
  color: var(--text-muted);
}

.shop-card-footer {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: var(--text-muted);
}

.rating,
.orders {
  display: flex;
  align-items: center;
  gap: 2px;
}
</style>
