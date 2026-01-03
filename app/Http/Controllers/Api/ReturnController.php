<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    use ApiResponseTrait;

    /**
     * Request return
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'required|exists:order_items,id',
            'reason' => 'required|string|min:10',
        ]);

        $user = $request->user();

        // Verify order belongs to user
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return $this->error('api.order_not_found', 404);
        }

        // Verify order item belongs to order
        $orderItem = $order->items()->where('id', $request->order_item_id)->first();
        if (!$orderItem) {
            return $this->error('api.order_not_found', 404);
        }

        // Check if order is delivered
        if ($order->order_status !== 'delivered') {
            return $this->error('api.order_not_delivered', 400);
        }

        // Create return request
        $return = ProductReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return $this->success($return->load(['order', 'orderItem']), 'api.return_requested');
    }
}

