# 🗺️ Live Order Tracking Guide

## Table of Contents
1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Current Implementation](#current-implementation)
4. [How Swiggy-like Live Tracking Works](#how-swiggy-like-live-tracking-works)
5. [Implementation with Map Libraries](#implementation-with-map-libraries)
6. [Local Testing Strategy](#local-testing-strategy)
7. [Production Setup](#production-setup)

---

## Overview

Live order tracking shows the real-time location of the delivery agent on a map, similar to Swiggy, Uber Eats, or Amazon delivery. 

**Key Components:**
- 📍 Delivery agent's current GPS location
- 🗺️ Interactive map showing agent → customer route
- ⏱️ Real-time status updates
- 📞 Contact delivery agent directly
- ⚠️ Order timeline and milestones

---

## Architecture

### Data Flow

```
Delivery Agent App
    ↓ (GPS coordinates every 5-10 seconds)
Server Database
    ↓ (Store location history)
Backend API (/api/orders/{id}/tracking)
    ↓ (REST endpoint or WebSocket)
Frontend Store (orders.js polling)
    ↓ (Poll every 5 seconds or real-time via WebSocket)
OrderTrackingMap Component
    ↓ (Render on Leaflet/Mapbox)
User's Browser
```

### Current Implementation (Mock)

**What we've done:**
- ✅ Backend tracking endpoint returns mock location data
- ✅ Frontend store with polling mechanism (every 5 seconds)
- ✅ OrderTrackingMap component ready for map library
- ✅ Real-time UI updates with refresh badge
- ✅ Delivery agent info (name, rating, vehicle)
- ✅ Distance and ETA calculations

**Data Structure Returned:**

```json
{
  "delivery_agent": {
    "id": 1,
    "name": "Rajesh Kumar",
    "rating": 4.8,
    "vehicle": "⚡ Bike",
    "phone": "+91-9876543210"
  },
  "current_location": {
    "latitude": 28.6139,
    "longitude": 77.2090,
    "address": "MG Road, New Delhi, India",
    "updated_at": "2024-04-10T14:35:22Z"
  },
  "pickup_location": {
    "latitude": 28.5355,
    "longitude": 77.3910,
    "address": "Restaurant Name, Delhi"
  },
  "delivery_location": {
    "latitude": 28.4595,
    "longitude": 77.0266,
    "address": "Customer Home, Delhi"
  },
  "distance_remaining_km": 2.5,
  "estimated_delivery": "2024-04-10T14:50:00Z",
  "milestones": [
    {
      "id": 1,
      "title": "Order Confirmed",
      "description": "Shop accepted your order",
      "timestamp": "2024-04-10T14:00:00Z",
      "status_id": 2
    },
    {
      "id": 2,
      "title": "Being Prepared",
      "description": "Chef is preparing your food",
      "timestamp": "2024-04-10T14:05:00Z",
      "status_id": 3
    },
    {
      "id": 3,
      "title": "Ready for Pickup",
      "description": "Your order is ready",
      "timestamp": "2024-04-10T14:15:00Z",
      "status_id": 4
    },
    {
      "id": 4,
      "title": "Out for Delivery",
      "description": "Rider is on the way",
      "timestamp": "2024-04-10T14:20:00Z",
      "status_id": 5
    }
  ]
}
```

---

## How Swiggy-like Live Tracking Works

### Phase 1: Data Collection

**Delivery Partner's Device:**
1. Driver app continuously captures GPS location (every 5-10 seconds)
2. Coordinates: latitude, longitude, accuracy, altitude, speed
3. Sent to backend via secure API call
4. Backend stores in database with timestamp

**Backend Processing:**
1. Validates GPS coordinates
2. Reverse geocoding: Convert lat/lng → address
3. Calculates distance to delivery location (Haversine formula)
4. Estimates time of arrival (ETA)
5. Updates delivery status if location within geofence

### Phase 2: Real-time Updates

**Option A: Polling (What we're using)**
```
Frontend polls: GET /api/orders/{id}/tracking
Every 5 seconds → Get latest location
Simple, works everywhere, slight delay (5s)
```

**Option B: WebSocket (Most Real-time)**
```
Server pushes: New location → Connected clients
Bidirectional communication
Updates within milliseconds
More resource-intensive
Requires socket.io or similar
```

**Option C: Server-Sent Events (SSE)**
```
Server Stream: GET /api/orders/{id}/tracking/stream
Unidirectional push
Works with HTTP/1.1
Less overhead than WebSocket
```

### Phase 3: Map Rendering

**Map Libraries Available:**

| Library | Best For | License | Cost |
|---------|----------|---------|------|
| **Leaflet** | Open-source, lightweight | MIT | FREE |
| **Mapbox** | Beautiful, detailed maps | Proprietary | $0-500+/month |
| **Google Maps** | Familiar, comprehensive | Proprietary | $7 per 1000 requests |
| **OpenStreetMap** | Community-driven | ODbL | FREE |

---

## Implementation with Map Libraries

### 1. Leaflet + OpenStreetMap (Recommended for MVP)

**Install:**
```bash
cd frontend
npm install leaflet
```

**Implementation:**

```vue
<template>
  <div class="tracking-map-container">
    <div ref="mapContainer" class="map" style="height: 400px;"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import L from 'leaflet'

const props = defineProps({
  trackingInfo: Object,
})

const mapContainer = ref(null)
let map
let agentMarker
let riderMarker
let routeLine

onMounted(() => {
  // Initialize map centered on Delhi
  map = L.map(mapContainer.value).setView([28.6139, 77.2090], 13)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(map)

  // Add custom icons
  const riderIcon = L.icon({
    iconUrl: '/icons/rider.png',
    iconSize: [40, 40],
    iconAnchor: [20, 40],
  })

  const homeIcon = L.icon({
    iconUrl: '/icons/home.png',
    iconSize: [40, 40],
    iconAnchor: [20, 40],
  })

  const shopIcon = L.icon({
    iconUrl: '/icons/shop.png',
    iconSize: [40, 40],
    iconAnchor: [20, 40],
  })

  // Watch for tracking updates
  watch(
    () => props.trackingInfo,
    (newTracking) => {
      if (!newTracking) return

      // Update rider marker
      const { latitude, longitude } = newTracking.current_location
      
      if (agentMarker) {
        agentMarker.setLatLng([latitude, longitude])
        agentMarker.bindPopup(
          `<strong>Current Location</strong><br>${newTracking.current_location.address}`
        )
      } else {
        agentMarker = L.marker([latitude, longitude], { icon: riderIcon })
          .addTo(map)
          .bindPopup(
            `<strong>Current Location</strong><br>${newTracking.current_location.address}`
          )
      }

      // Update/show delivery location marker
      const { latitude: deliveryLat, longitude: deliveryLng } =
        newTracking.delivery_location
      
      if (!riderMarker) {
        riderMarker = L.marker([deliveryLat, deliveryLng], { icon: homeIcon })
          .addTo(map)
          .bindPopup('<strong>Your Delivery Location</strong>')
      }

      // Draw route line
      const latlngs = [
        [latitude, longitude],
        [deliveryLat, deliveryLng],
      ]

      if (routeLine) {
        map.removeLayer(routeLine)
      }

      routeLine = L.polyline(latlngs, {
        color: '#ff6b00',
        weight: 3,
        opacity: 0.7,
      }).addTo(map)

      // Fit map to bounds
      const bounds = L.latLngBounds(latlngs)
      map.fitBounds(bounds, { padding: [50, 50] })
    },
    { deep: true }
  )
})
</script>

<style scoped>
.tracking-map-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.map {
  width: 100%;
  border-radius: 12px;
}
</style>
```

### 2. Mapbox (Production-Ready)

**Install:**
```bash
npm install @mapbox/mapbox-gl @mapbox/mapbox-gl-vue
```

**Benefits:**
- Real-time tracking UI
- Better performance
- Beautiful map styles
- Built-in geocoding

---

## Local Testing Strategy

### Step 1: Backend Mock Data

**Current Setup:**
The backend already returns mock location data with realistic coordinates.

File: `backend/app/Http/Controllers/Api/OrderController.php`

Check the `getTrackingInfo()` method - it returns:
- Simulated current location
- Distance remaining
- ETA
- Delivery agent info

### Step 2: Frontend Polling Setup

**Already Implemented in `stores/orders.js`:**

```javascript
// Start polling every 5 seconds
await ordersStore.startTrackingPoll(orderId, 5000)

// See location update in real-time
// Every 5 seconds, new coordinates fetched
```

### Step 3: Manual Testing Scenario

**Scenario 1: Watch Location Update Without Map**

1. Open browser dev tools → Network tab
2. Navigate to `/app/orders/1`
3. Observe requests to `/api/orders/1/tracking`
4. New request every 5 seconds
5. Check response → location changes slightly

**Scenario 2: Simulate Route with Mock Data**

Modify `backend/app/Http/Controllers/Api/OrderController.php`:

```php
private function getTrackingInfo($order)
{
    // Start: Restaurant (Connaught Place)
    $pickup = [
        'latitude' => 28.6313,
        'longitude' => 77.1832,
    ];

    // End: User's home (Dwarka)
    $delivery = [
        'latitude' => 28.5921,
        'longitude' => 77.0466,
    ];

    // Simulate progress (0-100%)
    $progress = (time() % 60) / 60; // Cycles every 60 seconds

    // Linear interpolation between pickup and delivery
    $current = [
        'latitude' => $pickup['latitude'] + 
                     ($delivery['latitude'] - $pickup['latitude']) * $progress,
        'longitude' => $pickup['longitude'] + 
                      ($delivery['longitude'] - $pickup['longitude']) * $progress,
    ];

    // Calculate distance remaining (in km)
    $distanceRemaining = 13 * (1 - $progress); // Total ~13km route

    return [
        'delivery_agent' => [
            'id' => 1,
            'name' => 'Rajesh Kumar',
            'rating' => 4.8,
            'vehicle' => '⚡ Bike',
            'phone' => '+91-9876543210',
        ],
        'current_location' => [
            'latitude' => $current['latitude'],
            'longitude' => $current['longitude'],
            'address' => $this->getAddressFromCoordinates(
                $current['latitude'],
                $current['longitude']
            ),
            'updated_at' => now()->toIso8601String(),
        ],
        'pickup_location' => $pickup,
        'delivery_location' => $delivery,
        'distance_remaining_km' => round($distanceRemaining, 1),
        'estimated_delivery' => now()
            ->addMinutes(max(5, (int)($distanceRemaining * 2)))
            ->toIso8601String(),
        'milestones' => [
            [
                'id' => 1,
                'title' => 'Order Confirmed',
                'description' => 'Shop accepted your order',
                'timestamp' => now()->subMinutes(10)->toIso8601String(),
                'status_id' => 2,
            ],
            [
                'id' => 2,
                'title' => 'Being Prepared',
                'description' => 'Chef is preparing your food',
                'timestamp' => now()->subMinutes(8)->toIso8601String(),
                'status_id' => 3,
            ],
            [
                'id' => 3,
                'title' => 'Ready for Pickup',
                'description' => 'Your order is ready',
                'timestamp' => now()->subMinutes(2)->toIso8601String(),
                'status_id' => 4,
            ],
            [
                'id' => 4,
                'title' => 'Out for Delivery',
                'description' => 'Rider is on the way',
                'timestamp' => now()->toIso8601String(),
                'status_id' => 5,
            ],
        ],
    ];
}

private function getAddressFromCoordinates($lat, $lng)
{
    // In real implementation: Use reverse geocoding API
    // For now: Mock addresses
    if ($lat > 28.6000) {
        return 'Connaught Place Area, Delhi';
    } elseif ($lat > 28.5900) {
        return 'Dwarka Area, Delhi';
    }
    return 'Somewhere in Delhi';
}
```

### Step 4: Test with Actual Map

**Using Leaflet (Simple):**

1. Update `OrderTrackingMap.vue` component:

```bash
npm install leaflet
```

2. Replace the placeholder code with actual map rendering (see code above)

3. Open `/app/orders/1`
4. See marker move along the route every 5 seconds
5. Watch distance decrease and ETA update

### Step 5: Testing Checklist

- [ ] Polling works (check Network tab, 1 request every 5s)
- [ ] Location updates on map
- [ ] Distance decreases over time
- [ ] ETA counts down
- [ ] Delivery agent info displays
- [ ] Status timeline shows progress
- [ ] Refresh button works manually
- [ ] Mobile responsive map
- [ ] Performance: No lag, smooth animations

**Browser Tools to Monitor:**
- DevTools → Network: See API calls
- DevTools → Console: Check for errors
- DevTools → Performance: Monitor frame rate

---

## Production Setup

### For Real GPS Tracking

**1. Delivery Partner App (Driver App)**

```javascript
// Every 5-10 seconds
navigator.geolocation.watchPosition(
  (position) => {
    // Send to backend
    fetch('/api/tracking/update', {
      method: 'POST',
      body: JSON.stringify({
        order_id: orderId,
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        accuracy: position.coords.accuracy,
        speed: position.coords.speed,
      }),
      headers: { 'Content-Type': 'application/json' },
    })
  },
  (error) => console.error(error),
  {
    enableHighAccuracy: true,
    maximumAge: 0,
    timeout: 5000,
  }
)
```

**2. Backend Endpoint (Store Location)**

```php
Route::post('/api/tracking/update', function (Request $request) {
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'accuracy' => 'nullable|numeric',
        'speed' => 'nullable|numeric',
    ]);

    // Store location history
    DB::table('delivery_locations')->insert([
        'order_id' => $validated['order_id'],
        'latitude' => $validated['latitude'],
        'longitude' => $validated['longitude'],
        'accuracy' => $validated['accuracy'],
        'speed' => $validated['speed'],
        'created_at' => now(),
    ]);

    // Get latest location
    $latest = DB::table('delivery_locations')
        ->where('order_id', $validated['order_id'])
        ->latest()
        ->first();

    return response()->json(['success' => true]);
});
```

**3. WebSocket for Real-time (Optional)**

For instant updates instead of polling:

```javascript
// Backend: Use Laravel Echo + Pusher/Ably
Channel::broadcast(new LocationUpdated($order->id, $location))

// Frontend: Subscribe
Echo.channel(`order.${orderId}`).listen('LocationUpdated', (data) => {
    trackingInfo.value = data
})
```

**4. Database Schema**

```sql
CREATE TABLE delivery_locations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    accuracy DECIMAL(8, 2),
    speed DECIMAL(8, 2),
    created_at TIMESTAMP,
    INDEX(order_id, created_at)
);
```

---

## Summary

**📊 Quick Comparison: Mock vs Real**

| Aspect | Mock (Current) | Real |
|--------|---|---|
| Setup | ✅ Done | Needs delivery app |
| Testing | ✅ Immediate | Needs Android/iOS app |
| Cost | $0 | Varies (geolocation APIs) |
| Accuracy | N/A | GPS ~5-10m |
| Real-time | Via polling | WebSocket/SSE |
| User Experience | Demo-ready | Production-ready |

**Next Steps:**
1. ✅ Current: Test with mock data locally
2. 📱 Future: Integrate real delivery partner GPS
3. 🔌 Future: Add WebSocket for true real-time
4. 🗺️ Future: Add Mapbox for professional UI

---

**Questions?**
- Need help setting up Leaflet? ➜ Check `OrderTrackingMap.vue`
- Want WebSocket? ➜ Use `laravel-echo` + Pusher
- Production GPS issues? ➜ Check device permissions in delivery app
