<template>
  <div class="order-tracking-map">
    <!-- Map container -->
    <div class="map-container" ref="mapContainer"></div>

    <!-- Tracking info overlay -->
    <div class="tracking-info">
      <div class="info-header">
        <h4 class="info-title">Live Tracking</h4>
        <div class="refresh-badge" :class="{ refreshing: isRefreshing }">
          🔄 {{ isRefreshing ? 'Updating...' : 'Real-time' }}
        </div>
      </div>

      <!-- Delivery agent info -->
      <div v-if="trackingInfo?.delivery_agent" class="agent-card">
        <div class="agent-header">
          <div class="agent-avatar">👨‍💼</div>
          <div class="agent-details">
            <div class="agent-name">{{ trackingInfo.delivery_agent.name }}</div>
            <div class="agent-meta">
              <span class="rating">⭐ {{ trackingInfo.delivery_agent.rating }}</span>
              <span class="vehicle">{{ trackingInfo.delivery_agent.vehicle }}</span>
            </div>
          </div>
          <a :href="`tel:${trackingInfo.delivery_agent.phone}`" class="call-btn">☎️</a>
        </div>
      </div>

      <!-- Distance and time info -->
      <div v-if="trackingInfo" class="tracking-stats">
        <div class="stat-item">
          <span class="stat-label">Distance</span>
          <span class="stat-value">{{ trackingInfo.distance_remaining_km }} km</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Est. Delivery</span>
          <span class="stat-value">{{ formatTime(trackingInfo.estimated_delivery) }}</span>
        </div>
      </div>

      <!-- Location info -->
      <div v-if="trackingInfo?.current_location" class="location-info">
        <div class="location-item">
          <span class="location-icon">📍</span>
          <div>
            <div class="location-label">Current Location</div>
            <div class="location-address">{{ trackingInfo.current_location.address }}</div>
            <div class="location-time">{{ formatUpdateTime(trackingInfo.current_location.updated_at) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="map-loading">
      <div class="spinner"></div>
      <p>Loading tracking information...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  trackingInfo: {
    type: Object,
    default: null,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
})

const mapContainer = ref(null)
const isRefreshing = computed(() => props.isLoading)

onMounted(() => {
  // In this demo, we're showing a placeholder
  // In production, you'd integrate with Leaflet or Mapbox
  if (mapContainer.value) {
    mapContainer.value.innerHTML = `
      <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 18px; font-weight: 600;">
        🗺️ Map View (Leaflet/Mapbox Integration Required)
      </div>
    `
  }
})

function formatTime(timestamp) {
  if (!timestamp) return '-'
  const date = new Date(timestamp)
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}

function formatUpdateTime(timestamp) {
  if (!timestamp) return '-'
  const date = new Date(timestamp)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)

  if (diff < 60) return `${diff} seconds ago`
  if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}
</script>

<style scoped>
.order-tracking-map {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: white;
  border: 1px solid #e5e7eb;
  margin-bottom: 24px;
}

.map-container {
  width: 100%;
  height: 300px;
  background: #f3f4f6;
  position: relative;
}

.tracking-info {
  padding: 16px;
  background: white;
}

.info-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.info-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
}

.refresh-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #10b981;
  color: white;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.refresh-badge.refreshing {
  background: #ff6b00;
  animation: pulse 1s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.agent-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 16px;
}

.agent-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.agent-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: white;
  border: 2px solid #ff6b00;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.agent-details {
  flex: 1;
}

.agent-name {
  font-weight: 600;
  color: #1a1a1a;
  font-size: 0.95rem;
}

.agent-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 2px;
  font-size: 0.8rem;
  color: #666;
}

.rating {
  display: flex;
  align-items: center;
  gap: 2px;
}

.vehicle {
  background: white;
  padding: 2px 8px;
  border-radius: 4px;
  border: 1px solid #e5e7eb;
}

.call-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ff6b00;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-size: 1.2rem;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.call-btn:hover {
  background: #e55a00;
  transform: scale(1.05);
}

.tracking-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 16px;
}

.stat-item {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px;
  text-align: center;
}

.stat-label {
  display: block;
  font-size: 0.8rem;
  color: #666;
  font-weight: 500;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  display: block;
  font-size: 1.1rem;
  font-weight: 700;
  color: #ff6b00;
}

.location-info {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px;
}

.location-item {
  display: flex;
  gap: 12px;
}

.location-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.location-label {
  font-size: 0.8rem;
  color: #666;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.location-address {
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 2px;
}

.location-time {
  font-size: 0.8rem;
  color: #999;
}

.map-loading {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.9);
  z-index: 10;
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

.map-loading p {
  margin-top: 12px;
  color: #666;
  font-size: 0.95rem;
}
</style>
