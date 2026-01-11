<?php

use Illuminate\Support\Facades\Route;

// Common routes (no prefix)
require __DIR__ . '/common.php';

// Authentication routes
require __DIR__ . '/auth.php';

// Express 30 routes
require __DIR__ . '/express-30.php';

// Cart routes
require __DIR__ . '/cart.php';

// Vendor routes
require __DIR__ . '/vendor.php';

// Salesman routes
require __DIR__ . '/salesman.php';

// Delivery Man routes
require __DIR__ . '/delivery-man.php';

// Protected routes (require authentication)
require __DIR__ . '/address.php';
require __DIR__ . '/order.php';
require __DIR__ . '/payment.php';
require __DIR__ . '/return.php';

// Admin routes (require authentication and super-admin role)
require __DIR__ . '/admin.php';
