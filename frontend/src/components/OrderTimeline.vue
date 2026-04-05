<template>
  
  <div class="order-timeline">
    <div class="timeline-header">
      <h3 class="timeline-title">Order Progress</h3>
      <span class="timeline-badge">{{ completedSteps }} of {{ totalSteps }}</span>
    </div>

    <div class="timeline-container">
      <div
        v-for="(milestone, index) in milestones"
        :key="milestone.id"
        class="timeline-step"
        :class="{ completed: milestone.completed, active: isActive(index) }"
      >
        <!-- Connector line -->
        <div v-if="index < milestones.length - 1" class="timeline-connector"></div>

        <!-- Step circle -->
        <div class="timeline-circle">
          <div v-if="milestone.completed" class="check-mark">✓</div>
          <div v-else class="step-number">{{ index + 1 }}</div>
        </div>

        <!-- Step content -->
        <div class="timeline-content">
          <div class="step-label">{{ milestone.label }}</div>
          <div v-if="milestone.timestamp" class="step-time">
            {{ formatTime(milestone.timestamp) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Current status -->
    <div class="timeline-footer">
      <div class="current-status">
        <span class="status-icon">📍</span>
        <span class="status-text">{{ currentStatusText }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ORDER_STATUS_LABELS } from '../config/order'

const props = defineProps({
  milestones: {
    type: Array,
    required: true,
    default: () => [],
  },
  currentStatusId: {
    type: Number,
    required: true,
  },
})

const totalSteps = computed(() => props.milestones.length)

const completedSteps = computed(() => {
  return props.milestones.filter(m => m.completed).length
})

const currentStatusText = computed(() => {
  return ORDER_STATUS_LABELS[props.currentStatusId] || 'Unknown'
})

function isActive(index) {
  if (index === 0) return true
  const currentMilestone = props.milestones[index]
  const previousMilestone = props.milestones[index - 1]
  return currentMilestone.completed && !previousMilestone.completed
}

function formatTime(timestamp) {
  if (!timestamp) return ''
  const date = new Date(timestamp)
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}
</script>

<style scoped>
.order-timeline {
  background: white;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e5e7eb;
}

.timeline-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.timeline-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a1a;
}

.timeline-badge {
  background: #ff6b00;
  color: white;
  font-size: 0.8rem;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
}

.timeline-container {
  position: relative;
}

.timeline-step {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  position: relative;
}

.timeline-step:last-child {
  margin-bottom: 0;
}

.timeline-connector {
  position: absolute;
  left: 19px;
  top: 40px;
  width: 2px;
  height: 48px;
  background: linear-gradient(to bottom, #ff6b00, #e5e7eb);
}

.timeline-step.completed .timeline-connector {
  background: #10b981;
}

.timeline-circle {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: white;
  border: 2px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-weight: 700;
  color: #666;
  transition: all 0.3s ease;
}

.timeline-step.completed .timeline-circle {
  background: #10b981;
  border-color: #10b981;
  color: white;
}

.timeline-step.active .timeline-circle {
  background: #ff6b00;
  border-color: #ff6b00;
  color: white;
  box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.1);
}

.check-mark,
.step-number {
  font-size: 1rem;
}

.timeline-content {
  padding-top: 4px;
}

.step-label {
  font-weight: 600;
  color: #1a1a1a;
  font-size: 0.95rem;
}

.step-time {
  font-size: 0.8rem;
  color: #999;
  margin-top: 2px;
}

.timeline-footer {
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.current-status {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #1a1a1a;
}

.status-icon {
  font-size: 1.2rem;
}

.status-text {
  font-weight: 600;
  font-size: 0.95rem;
}
</style>
