// Order configuration constants
// ID-based mapping for future scalability
// These should match backend config/order.php values

export const ORDER_STATUSES = {
  PENDING: 1,                    // pending
  CONFIRMED: 2,                  // confirmed
  PREPARING: 3,                  // preparing
  OUT_FOR_DELIVERY: 4,           // out_for_delivery
  DELIVERED: 5,                  // delivered
  CANCELLED: 6,                  // cancelled
}

export const PAYMENT_METHODS = {
  COD: 'cod',
  ONLINE: 'online',
  CARD: 'card',
}

export const PAYMENT_METHOD_IDS = {
  COD: 1,
  ONLINE: 2,
  CARD: 3,
}

export const DEFAULT_PAYMENT_METHOD = PAYMENT_METHODS.COD
export const DEFAULT_PAYMENT_METHOD_ID = PAYMENT_METHOD_IDS.COD

export const DEFAULT_DELIVERY_FEE = 40 // Should match backend

export const ORDER_STATUS_LABELS = {
  1: 'Order Placed',
  2: 'Confirmed',
  3: 'Being Prepared',
  4: 'Out for Delivery',
  5: 'Delivered',
  6: 'Cancelled',
}

export const PAYMENT_METHOD_LABELS = {
  1: 'Cash on Delivery',
  2: 'Online Payment',
  3: 'Card Payment',
}

/**
 * Get human-readable label for order status ID
 */
export const getStatusLabel = (statusId) => {
  return ORDER_STATUS_LABELS[statusId] || 'Unknown'
}

/**
 * Get human-readable label for payment method ID
 */
export const getPaymentMethodLabel = (paymentMethodId) => {
  return PAYMENT_METHOD_LABELS[paymentMethodId] || 'Unknown'
}

/**
 * Comprehensive ORDER_CONFIG object for component usage
 */
export const ORDER_CONFIG = {
  statuses: [
    { id: 1, label: 'Order Placed', icon: '📝', color: '#fbbf24' },
    { id: 2, label: 'Confirmed', icon: '📋', color: '#3b82f6' },
    { id: 3, label: 'Being Prepared', icon: '👨‍🍳', color: '#a855f7' },
    { id: 4, label: 'Ready for Pickup', icon: '✅', color: '#f97316' },
    { id: 5, label: 'Out for Delivery', icon: '🚚', color: '#10b981' },
    { id: 6, label: 'Delivered', icon: '📦', color: '#10b981' },
    { id: 7, label: 'Cancelled', icon: '❌', color: '#ef4444' },
  ],
  paymentMethods: [
    { id: 1, label: 'Cash on Delivery', code: 'cod', icon: '💵' },
    { id: 2, label: 'Online Payment', code: 'online', icon: '💳' },
    { id: 3, label: 'Card Payment', code: 'card', icon: '🏧' },
  ],
  statusLabels: ORDER_STATUS_LABELS,
  paymentMethodLabels: PAYMENT_METHOD_LABELS,
}
