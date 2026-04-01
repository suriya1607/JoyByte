/**
 * Currency Configuration
 * Change these values to update the currency used throughout the app
 */

export const currencyConfig = {
  // Currency code: 'USD', 'INR', 'EUR', etc.
  code: 'INR',

  // Currency symbol: '$', '₹', '€', etc.
  symbol: '₹',

  // Position: 'before' or 'after'
  symbolPosition: 'before',

  // Decimal places for price formatting
  decimalPlaces: 2,

  // Alternative symbols for different contexts
  symbols: {
    INR: '₹',
    USD: '$',
    EUR: '€',
    GBP: '£'
  }
}

// Export preset configurations for easy switching
export const currencyPresets = {
  INR: {
    code: 'INR',
    symbol: '₹',
    symbolPosition: 'before',
    decimalPlaces: 2
  },
  USD: {
    code: 'USD',
    symbol: '$',
    symbolPosition: 'before',
    decimalPlaces: 2
  },
  EUR: {
    code: 'EUR',
    symbol: '€',
    symbolPosition: 'after',
    decimalPlaces: 2
  },
  GBP: {
    code: 'GBP',
    symbol: '£',
    symbolPosition: 'before',
    decimalPlaces: 2
  }
}
