<?php

return [
    'success' => 'Operation successful',
    'error' => 'An error occurred',
    'validation_error' => 'Validation failed',
    
    // Auth
    'otp_sent' => 'OTP sent successfully',
    'otp_verified' => 'OTP verified successfully',
    'otp_invalid' => 'Invalid OTP',
    'otp_expired' => 'OTP has expired',
    'otp_already_used' => 'OTP has already been used',
    'logged_out' => 'Logged out successfully',
    'mobile_required' => 'Mobile number is required',
    'otp_required' => 'OTP is required',
    
    // Products
    'products_fetched' => 'Products fetched successfully',
    'product_not_found' => 'Product not found',
    'categories_fetched' => 'Categories fetched successfully',
    'product' => [
        'details_loaded' => 'Product details fetched successfully',
        'reviews_loaded' => 'Reviews loaded successfully',
        'questions_loaded' => 'Questions loaded successfully',
    ],
    
    // Cart
    'item_added_to_cart' => 'Item added to cart successfully',
    'cart_fetched' => 'Cart fetched successfully',
    'cart_updated' => 'Cart updated successfully',
    'item_removed_from_cart' => 'Item removed from cart',
    'cart_empty' => 'Cart is empty',
    'product_variant_not_found' => 'Product variant not found',
    'guest_token_required' => 'Guest token is required',
    'quantity_required' => 'Quantity is required',
    
    // Address
    'address_added' => 'Address added successfully',
    'addresses_fetched' => 'Addresses fetched successfully',
    'address_not_found' => 'Address not found',
    'address_required' => 'Address is required',
    
    // Order
    'order_placed' => 'Order placed successfully',
    'orders_fetched' => 'Orders fetched successfully',
    'order_not_found' => 'Order not found',
    'order_not_delivered' => 'Order has not been delivered yet',
    'order_details_fetched' => 'Order details fetched successfully',
    'insufficient_stock' => 'Insufficient stock available',
    'warehouse_not_found' => 'Warehouse not found',
    'delivery_type_invalid' => 'Invalid delivery type',
    'payment_method_invalid' => 'Invalid payment method',
    'otp_rate_limit' => 'Too many OTP requests. Please try again later.',
    
    // Payment
    'payment_initiated' => 'Payment initiated successfully',
    'payment_verified' => 'Payment verified successfully',
    'payment_failed' => 'Payment failed',
    'payment_not_found' => 'Payment not found',
    
    // Return
    'return_requested' => 'Return requested successfully',
    'return_not_found' => 'Return not found',
    'return_reason_required' => 'Return reason is required',
    
    // Banners
    'banners' => [
        'fetched' => 'Banners fetched successfully',
        'not_found' => 'No banners found',
    ],
    
    // Dashboard
    'dashboard' => [
        'loaded' => 'Dashboard data loaded successfully',
        'failed' => 'Failed to load dashboard data',
    ],
    
    // General
    'unauthorized' => 'Unauthorized access',
    'not_found' => 'Resource not found',
    'server_error' => 'Internal server error',
];

