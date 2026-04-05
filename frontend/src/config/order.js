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
