/**
 * Price Formatting Utility
 * Formats prices with the configured currency symbol and format
 */

import { currencyConfig } from '../config/currency'

/**
 * Format a price with the configured currency
 * @param {number} price - The price to format
 * @param {object} options - Optional overrides for currency config
 * @returns {string} Formatted price string
 */
export function formatPrice(price, options = {}) {
  const config = { ...currencyConfig, ...options }

  // Parse the price to a number
  const amount = parseFloat(price)

  // If not a valid number, return empty string
  if (isNaN(amount)) {
    return ''
  }

  // Format the amount with specified decimal places
  const formattedAmount = amount.toFixed(config.decimalPlaces)

  // Build the price string based on symbol position
  if (config.symbolPosition === 'before') {
    return `${config.symbol}${formattedAmount}`
  } else {
    return `${formattedAmount} ${config.symbol}`
  }
}

/**
 * Format multiple prices and return their sum
 * @param {number[]} prices - Array of prices to sum and format
 * @param {object} options - Optional overrides for currency config
 * @returns {string} Formatted total price
 */
export function formatTotalPrice(prices, options = {}) {
  const total = Array.isArray(prices)
    ? prices.reduce((sum, price) => sum + parseFloat(price), 0)
    : parseFloat(prices)

  return formatPrice(total, options)
}

// Export default for convenience
export default formatPrice
