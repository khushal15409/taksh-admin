<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Orders
        $orders = [
            [
                'id' => 1,
                'user_id' => 1, // John Doe
                'warehouse_id' => 1, // Ahmedabad Warehouse
                'address_id' => 1, // John's address
                'order_number' => 'ORD00000001',
                'delivery_type' => '30_min',
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'order_status' => 'delivered',
                'total_amount' => 104899.00, // (1 * 109900) + 50 delivery
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id' => 2,
                'user_id' => 2, // Jane Smith
                'warehouse_id' => 2, // Mumbai Warehouse
                'address_id' => 2, // Jane's address
                'order_number' => 'ORD00000002',
                'delivery_type' => '1_day',
                'payment_method' => 'online',
                'payment_status' => 'paid',
                'order_status' => 'delivered',
                'total_amount' => 84930.00, // (1 * 84900) + 30 delivery
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(1),
            ],
            [
                'id' => 3,
                'user_id' => 3, // Raj Patel
                'warehouse_id' => 1, // Ahmedabad Warehouse
                'address_id' => 3, // Raj's address
                'order_number' => 'ORD00000003',
                'delivery_type' => 'normal',
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'order_status' => 'processing',
                'total_amount' => 134920.00, // (1 * 134900) + 20 delivery
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(12),
            ],
            [
                'id' => 4,
                'user_id' => 4, // Priya Sharma
                'warehouse_id' => 3, // Delhi Warehouse
                'address_id' => 4, // Priya's address
                'order_number' => 'ORD00000004',
                'delivery_type' => '1_day',
                'payment_method' => 'online',
                'payment_status' => 'paid',
                'order_status' => 'shipped',
                'total_amount' => 28020.00, // (1 * 27990) + 30 delivery
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(2),
            ],
        ];

        DB::table('orders')->insert($orders);

        // Order Items
        $orderItems = [
            // Order 1 items
            ['id' => 1, 'order_id' => 1, 'product_variant_id' => 2, 'qty' => 1, 'price' => 109900.00, 'created_at' => now()->subDays(5), 'updated_at' => now()->subDays(5)],
            
            // Order 2 items
            ['id' => 2, 'order_id' => 2, 'product_variant_id' => 5, 'qty' => 1, 'price' => 84900.00, 'created_at' => now()->subDays(3), 'updated_at' => now()->subDays(3)],
            
            // Order 3 items
            ['id' => 3, 'order_id' => 3, 'product_variant_id' => 3, 'qty' => 1, 'price' => 134900.00, 'created_at' => now()->subDays(1), 'updated_at' => now()->subDays(1)],
            
            // Order 4 items
            ['id' => 4, 'order_id' => 4, 'product_variant_id' => 11, 'qty' => 1, 'price' => 27990.00, 'created_at' => now()->subHours(6), 'updated_at' => now()->subHours(6)],
        ];

        DB::table('order_items')->insert($orderItems);

        // Payments
        $payments = [
            [
                'id' => 1,
                'order_id' => 1,
                'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
                'gateway' => 'cod',
                'amount' => 104899.00,
                'status' => 'success',
                'response_json' => json_encode(['method' => 'cod', 'status' => 'paid']),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id' => 2,
                'order_id' => 2,
                'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
                'gateway' => 'razorpay',
                'amount' => 84930.00,
                'status' => 'success',
                'response_json' => json_encode(['gateway' => 'razorpay', 'payment_id' => 'pay_' . Str::random(14), 'status' => 'success']),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'id' => 3,
                'order_id' => 4,
                'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
                'gateway' => 'paytm',
                'amount' => 28020.00,
                'status' => 'success',
                'response_json' => json_encode(['gateway' => 'paytm', 'transaction_id' => 'TXN' . Str::random(10), 'status' => 'success']),
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
        ];

        DB::table('payments')->insert($payments);

        // Returns
        $returns = [
            [
                'id' => 1,
                'order_id' => 1,
                'order_item_id' => 1,
                'reason' => 'Product damaged during delivery. Screen has cracks.',
                'status' => 'approved',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(12),
            ],
            [
                'id' => 2,
                'order_id' => 2,
                'order_item_id' => 2,
                'reason' => 'Wrong product received. Ordered different variant.',
                'status' => 'pending',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
        ];

        DB::table('returns')->insert($returns);

        // Refunds
        $refunds = [
            [
                'id' => 1,
                'return_id' => 1,
                'amount' => 104899.00,
                'payment_method' => 'cod',
                'status' => 'completed',
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
        ];

        DB::table('refunds')->insert($refunds);
    }
}
