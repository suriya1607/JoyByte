# 🧪 Local Testing Checklist for Order Tracking

## Quick Start (5 minutes)

### 1. Start Your Development Servers

```bash
# Terminal 1 - Backend
cd backend
php artisan serve

# Terminal 2 - Frontend
cd frontend
npm run dev
```

### 2. Access Your App

```
Frontend: http://localhost:5173
Backend API: http://localhost:8000
```

---

## Manual Testing Scenarios

### Scenario 1: View Order History

**Steps:**
1. Login with your credentials
2. Click "My Orders" (or navigate to `/app/orders`)
3. See list of previous orders

**Expected Results:**
- ✅ Orders display with status badge
- ✅ Shop name, items count, total amount visible
- ✅ Filtering by status works
- ✅ Sorting options work (newest, oldest, price)
- ✅ Pagination works (if 10+ orders)
- ✅ Click "View Details" navigates to order detail

**API Endpoint to Test:**
```bash
curl http://localhost:8000/api/orders?status=1&sort_by=created_at&sort_order=desc
```

---

### Scenario 2: View Order Details

**Steps:**
1. From order history, click "View Details" on any order
2. Should see order summary page

**Expected Results:**
- ✅ Order ID, date, status badge display
- ✅ Order items list with images, shop name, quantities
- ✅ Delivery address shown
- ✅ Payment method displayed
- ✅ Order summary (subtotal, delivery fee, tax, total)
- ✅ Status timeline shows progress

**API Endpoint to Test:**
```bash
curl http://localhost:8000/api/orders/1
```

---

### Scenario 3: Order Status Badge

**Steps:**
1. View any order in history or detail view
2. Check the colored status badge

**Expected Results:**
- ✅ Pending (Status 1): Yellow badge with 📝 icon
- ✅ Confirmed (Status 2): Blue badge with 📋 icon
- ✅ Preparing (Status 3): Purple badge with 👨‍🍳 icon
- ✅ Ready (Status 4): Orange badge with ✅ icon
- ✅ Out for Delivery (Status 5): Green badge with 🚚 icon
- ✅ Delivered (Status 6): Green badge with 📦 icon
- ✅ Cancelled (Status 7): Red badge with ❌ icon

---

### Scenario 4: Order Timeline

**Steps:**
1. View order detail page
2. Look at "Order Status" section below tracking (if visible)
3. See timeline of milestones

**Expected Results:**
- ✅ Shows step number (e.g., "3/5")
- ✅ Each milestone has title, description, timestamp
- ✅ Current step highlighted in blue
- ✅ Completed steps have checkmarks (✓)
- ✅ Timeline connectors visible between steps

---

### Scenario 5: Live Tracking (Mock)

**Steps:**
1. View order with status >= Confirmed (status_id 2+)
2. Refresh page multiple times
3. Watch tracking info panel

**Expected Results:**
- ✅ "Live Tracking" section visible for confirmed orders
- ✅ Delivery agent details show (name, rating, vehicle)
- ✅ Distance remaining decreases (every 5 seconds)
- ✅ ETA counts down
- ✅ Current location address updates
- ✅ "Updating..." badge appears briefly when fetching
- ✅ "Real-time" badge shows when stable

**DevTools - Network Tab Test:**
1. Open DevTools (F12)
2. Go to Network tab
3. Refresh order detail page
4. Click on `/api/orders/1/tracking` requests
5. New request should appear every ~5 seconds

**API Endpoint to Test Directly:**
```bash
# Get tracking info
curl http://localhost:8000/api/orders/1/tracking

# You should see JSON response with:
# - delivery_agent (name, rating, vehicle)
# - current_location (lat, lng, address)
# - distance_remaining_km
# - estimated_delivery
# - milestones array
```

---

### Scenario 6: Manual Refresh

**Steps:**
1. View order detail page with active tracking
2. Click "🔄 Refresh" button at the top
3. Watch location update immediately

**Expected Results:**
- ✅ Button shows "Updating..." state
- ✅ Network request fired to `/api/orders/1/tracking`
- ✅ All tracking info updates
- ✅ Button returns to normal state

---

## Testing with Chrome DevTools

### Console Errors Check

1. Open DevTools (F12)
2. Go to Console tab
3. Should show NO RED ERRORS about:
   - Missing components
   - Missing routes
   - API failures
   - Undefined store properties

**If errors appear:**
- Check component imports
- Verify route paths
- Check store initialization

### Network Requests Check

1. Network tab (F12)
2. Navigate to `/app/orders`
3. Should see requests: `orders?status=...`
4. Status codes: All 200s (success)

**Slow request? Check:**
- Backend server running?
- Database queries optimized?
- Any N+1 query issues?

### Performance Monitor

1. DevTools → Performance tab
2. Record while scrolling order list
3. FPS should be 60 (smooth)
4. No memory leaks

---

## Component Testing

### OrderHistoryView.vue
- [ ] Orders load on mount
- [ ] Search filter works in real-time
- [ ] Status dropdown filters correctly
- [ ] Sort options change order
- [ ] Pagination works (if 10+ orders)
- [ ] Empty state shows when no orders
- [ ] Click order card opens detail view

### OrderDetailView.vue
- [ ] Back button navigates to history
- [ ] Refresh button works
- [ ] Order items display correctly
- [ ] Delivery address shows
- [ ] Payment info displays
- [ ] Order summary grid shows all fields
- [ ] Tracking section visible for confirmed+ orders

### OrderTimeline.vue
- [ ] Timeline renders milestones
- [ ] Current step highlighted
- [ ] Completed steps show checkmarks
- [ ] Step counter updates
- [ ] Timestamps formatted correctly

### OrderTrackingMap.vue
- [ ] Map container renders
- [ ] Placeholder message displays (until map lib integrated)
- [ ] Delivery agent card shows info
- [ ] Distance/ETA stats display
- [ ] Current location section visible
- [ ] "Real-time" badge displays

### StatusBadge.vue
- [ ] Correct color for each status
- [ ] Correct icon for each status
- [ ] Text label correct
- [ ] Responsive on mobile

---

## Database/Backend Checks

### Check Mock Orders in Database

```bash
# SSH into your server or use laravel tinker
php artisan tinker
```

```php
# Inside tinker

# Check orders exist
Order::count()

# Get first order
Order::with('items', 'shop', 'user')->first()

# Check status values (should be integers 1-7)
Order::pluck('status')->unique()

# Check payment methods (should be integers 1-3)
Order::pluck('payment_method')->unique()

# Check delivery addresses (should be JSON)
Order::first()->delivery_address
```

### Create Test Data if Needed

```php
php artisan tinker

# If no orders exist, create mock data
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;

$user = User::first();
$shop = Shop::first();

for ($i = 0; $i < 5; $i++) {
    Order::create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'status' => rand(1, 7),
        'payment_method' => rand(1, 3),
        'subtotal' => 500,
        'delivery_fee' => 50,
        'tax' => 81,
        'total_amount' => 631,
        'delivery_address' => json_encode([
            'street' => '123 Main St',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'postal_code' => '110001',
            'phone' => '9876543210',
            'notes' => 'Gate 5'
        ]),
        'created_at' => Carbon::now()->subDays(rand(0, 30))
    ]);
}

# Confirm creation
Order::count()
```

---

## Common Issues & Solutions

### Issue 1: Orders showing but tracking not updating

**Problem:** Tracking data not changing every 5 seconds

**Solution:**
1. Check DevTools Network tab → Is API called every 5s?
2. If not, check `stores/orders.js` → `startTrackingPoll()`
3. Verify polling interval: Should see request every ~5000ms
4. Check if polling was started: Call `startTrackingPoll(orderId, 5000)`

### Issue 2: Status badge not showing correct icon/color

**Problem:** Wrong icon or color for status

**Solution:**
1. Check `config/order.js` → status mapping correct?
2. Verify StatusBadge receives correct statusId (Integer, not String)
3. Check ComponentStatusBadge.vue → Icon/color mapping matches config

### Issue 3: Timeline not rendering milestones

**Problem:** Timeline component empty

**Solution:**
1. Verify API returns milestones array
2. Check OrderTimeline receives milestones prop
3. Ensure milestones have required fields: id, title, description, timestamp

### Issue 4: Map not displaying (if using Leaflet)

**Problem:** Map area blank or error

**Solution:**
1. Install leaflet: `npm install leaflet`
2. Verify OrderTrackingMap imports L from 'leaflet'
3. Check browser console for errors
4. Verify tracking data has valid lat/lng coordinates

---

## Interactive Testing Session

### Complete Flow Test (10 minutes)

```
1. Login
   ↓
2. Navigate to /app/orders
   ↓
3. See order list (apply filters/sorts)
   ↓
4. Click order with status >= 2 (Confirmed)
   ↓
5. View order detail with timeline
   ↓
6. Scroll down to see tracking (if status >= 2)
   ↓
7. Wait 5 seconds, watch location update
   ↓
8. Click Refresh button
   ↓
9. Verify all data updates


✅ If everything works: You're ready for production!
❌ If something fails: Check corresponding section above
```

---

## API Testing with cURL

### Get Orders List
```bash
curl \
  -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost:8000/api/orders?status=2&per_page=10"
```

### Get Order Detail
```bash
curl \
  -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost:8000/api/orders/1"
```

### Get Order Tracking
```bash
curl \
  -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost:8000/api/orders/1/tracking"
```

### Update Order Status (Admin Only)
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status": 3}' \
  "http://localhost:8000/api/orders/1/status"
```

---

## Next Steps After Testing

### ✅ All tests pass?
- Commit your work to git
- Deploy to staging
- Prepare production deployment

### ❌ Tests fail?
- Debug using sections above
- Check error logs: `storage/logs/laravel.log`
- Review browser console errors
- Check database constraints

### 🚀 Ready for production?
- Integrate real Mapbox/Google Maps API
- Set up real delivery partner location tracking
- Configure WebSocket for real-time updates
- Add push notifications for status changes
- Set up SSL/TLS certificates
- Configure CORS for production domain
- Set up monitoring and alerting

---

## Performance Metrics to Monitor

### Frontend
- Page load time: < 2s
- Time to interactive: < 3s
- First contentful paint: < 1.5s
- Order list scroll: 60 FPS
- Map render: 30 FPS (acceptable)

### Backend
- GET /orders: < 500ms
- GET /orders/{id}: < 300ms
- GET /orders/{id}/tracking: < 200ms
- POST /orders/{id}/status: < 300ms

### Database
- Orders list query: < 100ms
- Order detail query: < 50ms
- Tracking query: < 10ms

---

## Need Help?

**Still having issues?**
1. Check browser console (F12 → Console)
2. Check network requests (F12 → Network)
3. Check database directly (`php artisan tinker`)
4. Review server logs: `tail -f storage/logs/laravel.log`
5. Check route definitions: `php artisan route:list | grep order`

**Pro Tips:**
- Always check browser DevTools first (90% of issues are there)
- Use `php artisan tinker` to verify data
- Check order table values are integers 1-7, not strings
- Ensure delivery_address is valid JSON
- Verify timestamps are in ISO8601 format
