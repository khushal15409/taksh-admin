<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderCountService;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        OrderCountService::clearCache();
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Clear cache when order status or delivery type changes
        if ($order->isDirty(['order_status', 'delivery_type'])) {
            OrderCountService::clearCache();
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        OrderCountService::clearCache();
    }
}

