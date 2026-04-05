<?php

/**
 * Order Configuration
 * Uses ID-based mapping for future scalability
 * IDs are stored in database (small INT), values are descriptive
 * Can easily add new statuses/payment methods without schema changes
 */

return [
    /**
     * Order Statuses - ID based mapping
     * Store ID (1-6) in database instead of string
     * Example: 1 = 'pending', 2 = 'confirmed', etc.
     */
    'statuses' => [
        1 => 'pending',
        2 => 'confirmed',
        3 => 'preparing',
        4 => 'out_for_delivery',
        5 => 'delivered',
        6 => 'cancelled',
    ],

    /**
     * Payment Methods - ID based mapping
     * Store ID (1-3) in database instead of string
     * Easily add new payment methods without schema changes
     * Example: 1 = COD, 2 = Online, 3 = Card
     */
    'payment_methods' => [
        1 => 'cod',           // Cash on Delivery
        2 => 'online',        // Online Payment
        3 => 'card',          // Debit/Credit Card
    ],

    /**
     * Reverse mapping: value to ID
     * Used for validation and data insertion
     */
    'status_ids' => [
        'pending'           => 1,
        'confirmed'         => 2,
        'preparing'         => 3,
        'out_for_delivery'  => 4,
        'delivered'         => 5,
        'cancelled'         => 6,
    ],

    'payment_method_ids' => [
        'cod'      => 1,
        'online'   => 2,
        'card'     => 3,
    ],

    'default_status_id' => 2,                    // confirmed (ID)
    'default_payment_method_id' => 1,            // cod (ID)
    'default_delivery_fee' => 40.00,

    /**
     * Status Labels for UI display
     * Maps ID or string to human-readable label
     */
    'status_labels' => [
        1 => 'Order Placed',       // pending
        2 => 'Confirmed',          // confirmed
        3 => 'Being Prepared',     // preparing
        4 => 'Out for Delivery',   // out_for_delivery
        5 => 'Delivered',          // delivered
        6 => 'Cancelled',          // cancelled
    ],

    /**
     * Payment Method Labels
     */
    'payment_method_labels' => [
        1 => 'Cash on Delivery',
        2 => 'Online Payment',
        3 => 'Card Payment',
    ],
];
