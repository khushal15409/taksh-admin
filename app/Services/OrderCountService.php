<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductReturn;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderCountService
{
    /**
     * Get all order counts with caching
     * 
     * @return array
     */
    public static function getCounts(): array
    {
        return Cache::remember('admin_order_counts', 60, function () {
            return self::fetchCounts();
        });
    }

    /**
     * Fetch order counts from database
     * Uses optimized queries with indexed columns
     * 
     * @return array
     */
    private static function fetchCounts(): array
    {
        // Use a single query with conditional aggregation for better performance
        $orderCounts = DB::table('orders')
            ->selectRaw('
                COUNT(*) as all_orders,
                SUM(CASE WHEN order_status = "pending" AND delivery_type != "express_30" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN order_status = "confirmed" AND delivery_type != "express_30" THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN order_status = "delivered" AND delivery_type != "express_30" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN order_status = "cancelled" AND delivery_type != "express_30" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN delivery_type = "express_30" THEN 1 ELSE 0 END) as express_30
            ')
            ->first();

        // Get refund/return count
        $refundsCount = ProductReturn::count();

        return [
            'all' => (int) ($orderCounts->all_orders ?? 0),
            'pending' => (int) ($orderCounts->pending ?? 0),
            'confirmed' => (int) ($orderCounts->confirmed ?? 0),
            'delivered' => (int) ($orderCounts->delivered ?? 0),
            'cancelled' => (int) ($orderCounts->cancelled ?? 0),
            'express_30' => (int) ($orderCounts->express_30 ?? 0),
            'refunds' => (int) $refundsCount,
        ];
    }

    /**
     * Clear the cache (useful when order status changes)
     * 
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget('admin_order_counts');
    }
}

