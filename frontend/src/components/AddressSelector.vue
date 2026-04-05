<template>
  <div class="address-selector">
    <!-- Option Selection View -->
    <div v-if="!showAddressForm && !showLocationForm" class="options-container">
      <h3 class="section-title">Choose Address Option</h3>
      
      <div class="options-grid">
        <!-- Option 1: Use Geolocation -->
        <button class="option-card" @click="startGeolocation">
          <div class="option-icon">📍</div>
          <div class="option-content">
            <h4>Use Current Location</h4>
            <p>Fetch address using GPS</p>
          </div>
          <div class="option-arrow">→</div>
        </button>

        <!-- Option 2: Select Existing or Create -->
        <button class="option-card" @click="showAddressForm = true">
          <div class="option-icon">📋</div>
          <div class="option-content">
            <h4>{{ hasAddresses ? 'Select Address' : 'Create Address' }}</h4>
            <p>{{ hasAddresses ? `${addresses.length} saved address(es)` : 'Add new address' }}</p>
          </div>
          <div class="option-arrow">→</div>
        </button>
      </div>
    </div>

    <!-- Address List / Form View -->
    <div v-if="showAddressForm && !showLocationForm" class="address-form-container">
      <div class="form-header">
        <button class="back-btn" @click="goBack">← Back</button>
        <h3>{{ selectedAddressIdLocal ? 'Edit Address' : 'Add Delivery Address' }}</h3>
      </div>

      <!-- Existing Addresses List -->
      <div v-if="hasAddresses && !selectedAddressIdLocal" class="addresses-list">
        <h4 class="list-title">Your Addresses</h4>
        <div
          v-for="address in addresses"
          :key="address.id"
          class="address-item"
          :class="{ active: modelValue === address.id }"
          @click="selectExistingAddress(address.id)"
        >
          <div class="address-item-content">
            <div class="address-name">{{ address.name }}</div>
            <div class="address-details">{{ address.line1 }}, {{ address.city }} {{ address.pincode }}</div>
            <div v-if="address.is_default" class="default-badge">Default</div>
          </div>
          <input
            type="radio"
            :checked="modelValue === address.id"
            class="address-radio"
          />
        </div>
      </div>

      <!-- Create/Edit Address Form -->
      <div class="address-form">
        <h4 v-if="!selectedAddressIdLocal" class="form-title">Or create a new address</h4>

        <div class="input-group">
          <label class="input-label">Full Name</label>
          <input
            v-model="form.name"
            type="text"
            class="base-input"
            :class="{ 'input-error': errors.name }"
            placeholder="Your full name"
          />
          <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
        </div>

        <div class="input-group">
          <label class="input-label">Contact Phone</label>
          <input
            v-model="form.phone"
            type="tel"
            class="base-input"
            :class="{ 'input-error': errors.phone }"
            placeholder="9876543210"
            maxlength="15"
          />
          <span v-if="errors.phone" class="error-msg">{{ errors.phone }}</span>
        </div>

        <div class="input-group">
          <label class="input-label">Address Line</label>
          <input
            v-model="form.line1"
            type="text"
            class="base-input"
            :class="{ 'input-error': errors.line1 }"
            placeholder="House no, street, area"
          />
          <span v-if="errors.line1" class="error-msg">{{ errors.line1 }}</span>
        </div>

        <div class="input-row">
          <div class="input-group">
            <label class="input-label">City</label>
            <input
              v-model="form.city"
              type="text"
              class="base-input"
              :class="{ 'input-error': errors.city }"
              placeholder="City"
            />
            <span v-if="errors.city" class="error-msg">{{ errors.city }}</span>
          </div>
          <div class="input-group">
            <label class="input-label">Pincode</label>
            <input
              v-model="form.pincode"
              type="text"
              class="base-input"
              :class="{ 'input-error': errors.pincode }"
              placeholder="600001"
              maxlength="10"
            />
            <span v-if="errors.pincode" class="error-msg">{{ errors.pincode }}</span>
          </div>
        </div>

        <div class="form-actions">
          <button
            class="btn-secondary"
            @click="goBack"
          >
            Cancel
          </button>
          <button
            class="btn-primary"
            :disabled="isLoading"
            @click="saveAddress"
          >
            <span v-if="isLoading" class="btn-spinner"></span>
            <span v-else>{{ selectedAddressIdLocal ? 'Update' : 'Save' }} Address</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Geolocation Form View -->
    <div v-if="showLocationForm && !showAddressForm" class="location-form-container">
      <div class="form-header">
        <button class="back-btn" @click="goBack">← Back</button>
        <h3>Fetch Address from Location</h3>
      </div>

      <div class="location-info">
        <p v-if="!coords" class="info-text">
          Click below to fetch your current location, then complete the address details.
        </p>
        <p v-else class="info-text">
          <strong>Location fetched!</strong> Latitude: {{ coords.latitude }}, Longitude: {{ coords.longitude }}
        </p>
      </div>

      <div v-if="!coords" class="location-actions">
        <button
          class="btn-primary btn-large"
          :disabled="isLoading"
          @click="getCoordinates"
        >
          <span v-if="isLoading" class="btn-spinner"></span>
          <span v-else>📍 Get My Current Location</span>
        </button>
      </div>

      <div v-else class="address-form">
        <div class="input-group">
          <label class="input-label">Full Name</label>
          <input
            v-model="form.name"
            type="text"
            class="base-input"
            :class="{ 'input-error': errors.name }"
            placeholder="Your full name"
          />
          <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
        </div>

        <div class="input-group">
          <label class="input-label">Contact Phone</label>
          <input
            v-model="form.phone"
            type="tel"
            class="base-input"
            :class="{ 'input-error': errors.phone }"
            placeholder="9876543210"
            maxlength="15"
          />
          <span v-if="errors.phone" class="error-msg">{{ errors.phone }}</span>
        </div>

        <div class="input-group">
          <label class="input-label">Address Line</label>
          <input
            v-model="form.line1"
            type="text"
            class="base-input"
            :class="{ 'input-error': errors.line1 }"
            placeholder="House no, street, area"
          />
          <span v-if="errors.line1" class="error-msg">{{ errors.line1 }}</span>
        </div>

        <div class="input-row">
          <div class="input-group">
            <label class="input-label">City</label>
            <input
              v-model="form.city"
              type="text"
              class="base-input"
              :class="{ 'input-error': errors.city }"
              placeholder="City"
            />
            <span v-if="errors.city" class="error-msg">{{ errors.city }}</span>
          </div>
          <div class="input-group">
            <label class="input-label">Pincode</label>
            <input
              v-model="form.pincode"
              type="text"
              class="base-input"
              :class="{ 'input-error': errors.pincode }"
              placeholder="600001"
              maxlength="10"
            />
            <span v-if="errors.pincode" class="error-msg">{{ errors.pincode }}</span>
          </div>
        </div>

        <div class="form-actions">
          <button class="btn-secondary" @click="resetLocation">
            Get Location Again
          </button>
          <button
            class="btn-primary"
            :disabled="isLoading"
            @click="saveAddress"
          >
            <span v-if="isLoading" class="btn-spinner"></span>
            <span v-else>Save Address</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { useAddressStore } from '../stores/address'

const props = defineProps({
  modelValue: {
    type: [Number, null],
    default: null,
  },
})

const emit = defineEmits(['update:modelValue'])

const addressStore = useAddressStore()

const showAddressForm = ref(false)
const showLocationForm = ref(false)
const selectedAddressIdLocal = ref(null)
const coords = ref(null)
const isLoading = ref(false)

const form = reactive({
  name: '',
  phone: '',
  line1: '',
  city: '',
  pincode: '',
})

const errors = reactive({
  name: '',
  phone: '',
  line1: '',
  city: '',
  pincode: '',
})

const addresses = computed(() => addressStore.addresses)
const hasAddresses = computed(() => addresses.value.length > 0)

watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal) {
      const address = addressStore.addresses.find(a => a.id === newVal)
      if (address) {
        Object.assign(form, {
          name: address.name,
          phone: address.phone,
          line1: address.line1,
          city: address.city,
          pincode: address.pincode,
        })
        selectedAddressIdLocal.value = newVal
      }
    }
  },
  { immediate: true }
)

function goBack() {
  showAddressForm.value = false
  showLocationForm.value = false
  selectedAddressIdLocal.value = null
  coords.value = null
  resetForm()
}

function resetForm() {
  form.name = ''
  form.phone = ''
  form.line1 = ''
  form.city = ''
  form.pincode = ''
  Object.assign(errors, {
    name: '',
    phone: '',
    line1: '',
    city: '',
    pincode: '',
  })
}

function startGeolocation() {
  showLocationForm.value = true
}

function getCoordinates() {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser')
    return
  }

  isLoading.value = true
  navigator.geolocation.getCurrentPosition(
    (position) => {
      coords.value = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
      }
      isLoading.value = false
    },
    (error) => {
      console.error('Geolocation error:', error)
      alert('Unable to fetch your location. Please enable location permission.')
      isLoading.value = false
    }
  )
}

function resetLocation() {
  coords.value = null
  resetForm()
}

function selectExistingAddress(addressId) {
  emit('update:modelValue', addressId)
  addressStore.selectAddress(addressId)
  goBack()
}

function validate() {
  let valid = true

  errors.name = form.name.trim() ? '' : 'Full name is required.'
  errors.phone = form.phone.trim() ? '' : 'Phone number is required.'
  errors.line1 = form.line1.trim() ? '' : 'Address is required.'
  errors.city = form.city.trim() ? '' : 'City is required.'
  errors.pincode = form.pincode.trim() ? '' : 'Pincode is required.'

  for (const key of Object.keys(errors)) {
    if (errors[key]) {
      valid = false
      break
    }
  }

  return valid
}

async function saveAddress() {
  if (!validate()) return

  isLoading.value = true
  try {
    const addressData = {
      name: form.name.trim(),
      phone: form.phone.trim(),
      line1: form.line1.trim(),
      city: form.city.trim(),
      pincode: form.pincode.trim(),
    }

    if (coords.value) {
      addressData.latitude = coords.value.latitude
      addressData.longitude = coords.value.longitude
    }

    let savedAddress
    if (selectedAddressIdLocal.value) {
      savedAddress = await addressStore.updateAddress(selectedAddressIdLocal.value, addressData)
    } else {
      savedAddress = await addressStore.createAddress(addressData)
    }

    emit('update:modelValue', savedAddress.id)
    goBack()
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.address-selector {
  width: 100%;
}

.options-container {
  padding: 1rem 0;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1a1a1a;
}

.options-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

.option-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: #fff;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.option-card:hover {
  border-color: #ff6b00;
  background: #fff9f3;
}

.option-icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.option-content {
  flex: 1;
}

.option-content h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1a1a1a;
}

.option-content p {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
}

.option-arrow {
  font-size: 1.5rem;
  color: #999;
  flex-shrink: 0;
}

/* Form Containers */
.address-form-container,
.location-form-container {
  padding: 1rem 0;
}

.form-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.back-btn {
  background: none;
  border: none;
  font-size: 1rem;
  color: #ff6b00;
  cursor: pointer;
  padding: 0.5rem 0;
  font-weight: 600;
}

.back-btn:hover {
  opacity: 0.8;
}

.form-header h3 {
  margin: 0;
  font-size: 1.1rem;
  color: #1a1a1a;
  flex: 1;
}

/* Addresses List */
.addresses-list {
  margin-bottom: 2rem;
}

.list-title {
  margin: 0 0 1rem 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.address-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f9f9f9;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  margin-bottom: 0.75rem;
  transition: all 0.2s ease;
}

.address-item:hover {
  border-color: #ff6b00;
  background: #fff9f3;
}

.address-item.active {
  border-color: #ff6b00;
  background: #fff9f3;
}

.address-item-content {
  flex: 1;
}

.address-name {
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 0.25rem;
}

.address-details {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.default-badge {
  display: inline-block;
  background: #ff6b00;
  color: white;
  font-size: 0.7rem;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-weight: 600;
}

.address-radio {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

/* Forms */
.address-form,
.location-form-container {
  background: #f9f9f9;
  padding: 1.5rem;
  border-radius: 12px;
}

.form-title {
  margin: 0 0 1.5rem 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.input-group {
  margin-bottom: 1.25rem;
}

.input-label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #1a1a1a;
}

.base-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  transition: border-color 0.2s ease;
  box-sizing: border-box;
}

.base-input:focus {
  outline: none;
  border-color: #ff6b00;
  box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
}

.base-input.input-error {
  border-color: #dc3545;
}

.error-msg {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #dc3545;
}

.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

/* Location Info */
.location-info {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid #e0e0e0;
}

.info-text {
  margin: 0;
  font-size: 0.9rem;
  color: #666;
  line-height: 1.5;
}

.location-actions {
  margin-bottom: 2rem;
}

/* Form Actions */
.form-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-primary {
  background: #ff6b00;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #e55a00;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: white;
  color: #ff6b00;
  border: 1px solid #ff6b00;
}

.btn-secondary:hover {
  background: #fff9f3;
}

.btn-large {
  width: 100%;
}

.btn-spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 640px) {
  .form-actions {
    grid-template-columns: 1fr;
  }

  .input-row {
    grid-template-columns: 1fr;
  }
}
</style>
