<template>
  <div class="product-card">
    <div class="product-card-image">
      <img :src="product.image_url" :alt="product.name" />
      <div v-if="product.stock_quantity === 0" class="stock-badge">Out of Stock</div>
    </div>
    <div class="product-card-content">
      <h4 class="product-card-title">{{ product.name }}</h4>
      <p class="product-card-description">{{ product.description }}</p>
      <div class="product-card-footer">
        <span class="price">{{ formatPrice(product.price) }}</span>
        <BaseButton
          v-if="product.stock_quantity > 0"
          @click="handleAddToCart"
          :loading="isLoading"
          size="sm"
          variant="primary"
        >
          Add
        </BaseButton>
        <span v-else class="out-of-stock">Out</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import BaseButton from './BaseButton.vue'
import formatPrice from '../../lib/formatPrice'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['add-to-cart'])
const isLoading = ref(false)

const handleAddToCart = async () => {
  isLoading.value = true
  try {
    emit('add-to-cart', 1)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.product-card {
  background: var(--surface);
  border-radius: var(--radius-md);
  overflow: hidden;
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card-image {
  position: relative;
  width: 100%;
  height: 140px;
  overflow: hidden;
}

.product-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.stock-badge {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
}

.product-card-content {
  padding: 10px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-card-title {
  margin: 0 0 4px;
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
  line-height: 1.3;
}

.product-card-description {
  margin: 0 0 8px;
  font-size: 11px;
  color: var(--text-muted);
  line-height: 1.2;
  flex: 1;
}

.product-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.price {
  font-size: 12px;
  font-weight: 600;
  color: var(--primary);
}

.out-of-stock {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 500;
}
</style>
