<template>
  <div class="status-badge" :class="`status-${statusValue}`">
    <span class="badge-icon">{{ statusIcon }}</span>
    <span class="badge-text">{{ statusLabel }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ORDER_STATUS_LABELS } from '../config/order'

const props = defineProps({
  statusId: {
    type: Number,
    required: true,
  },
})

const statusLabel = computed(() => {
  return ORDER_STATUS_LABELS[props.statusId] || 'Unknown'
})

const statusValue = computed(() => {
  const mapping = {
    1: 'pending',
    2: 'confirmed',
    3: 'preparing',
    4: 'out-for-delivery',
    5: 'delivered',
    6: 'cancelled',
  }
  return mapping[props.statusId] || 'unknown'
})

const statusIcon = computed(() => {
  const icons = {
    1: '📝',
    2: '✅',
    3: '👨‍🍳',
    4: '🚚',
    5: '📦',
    6: '❌',
  }
  return icons[props.statusId] || '❓'
})
</script>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  white-space: nowrap;
}

.badge-icon {
  font-size: 1.1rem;
}

.badge-text {
  letter-spacing: 0.3px;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fcd34d;
}

.status-confirmed {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.status-preparing {
  background: #fed7aa;
  color: #9a3412;
  border: 1px solid #fdba74;
}

.status-out-for-delivery {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #6ee7b7;
}

.status-delivered {
  background: #bbf7d0;
  color: #065f46;
  border: 1px solid #6ee7b7;
}

.status-cancelled {
  background: #fee2e2;
  color: #7f1d1d;
  border: 1px solid #fca5a5;
}

.status-unknown {
  background: #e5e7eb;
  color: #374151;
  border: 1px solid #d1d5db;
}
</style>
