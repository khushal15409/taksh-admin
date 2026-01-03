<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Initiate payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway' => 'required|string|in:razorpay,paytm,stripe',
        ]);

        $user = $request->user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return $this->error('api.order_not_found', 404);
        }

        if ($order->payment_method !== 'online') {
            return $this->error('api.payment_method_invalid', 400);
        }

        // Generate transaction ID
        $transactionId = 'TXN' . strtoupper(Str::random(12));

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'gateway' => $request->gateway,
            'amount' => $order->total_amount,
            'status' => 'pending',
        ]);

        // In production, integrate with payment gateway
        // For now, return payment details
        return $this->success([
            'payment_id' => $payment->id,
            'transaction_id' => $transactionId,
            'amount' => $order->total_amount,
            'gateway' => $request->gateway,
        ], 'api.payment_initiated');
    }

    /**
     * Verify payment
     */
    public function verify(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'transaction_id' => 'required|string',
            'status' => 'required|in:success,failed',
        ]);

        $user = $request->user();
        $payment = Payment::where('id', $request->payment_id)
            ->where('transaction_id', $request->transaction_id)
            ->with('order')
            ->first();

        if (!$payment) {
            return $this->error('api.payment_not_found', 404);
        }

        // Verify order belongs to user
        if ($payment->order->user_id !== $user->id) {
            return $this->error('api.unauthorized', 403);
        }

        // Update payment status
        $payment->update([
            'status' => $request->status,
            'response_json' => $request->all(),
        ]);

        // Update order payment status
        $payment->order->update([
            'payment_status' => $request->status === 'success' ? 'paid' : 'failed',
        ]);

        if ($request->status === 'success') {
            // Release reserved stock and update actual stock
            foreach ($payment->order->items as $item) {
                $warehouseProduct = \App\Models\WarehouseProduct::where('warehouse_id', $payment->order->warehouse_id)
                    ->where('product_variant_id', $item->product_variant_id)
                    ->first();

                if ($warehouseProduct) {
                    $warehouseProduct->decrement('reserved_qty', $item->qty);
                    $warehouseProduct->decrement('stock_qty', $item->qty);
                }
            }
        }

        return $this->success($payment->load('order'), 'api.payment_verified');
    }
}

