<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FulfillmentCenter;
use App\Models\WarehouseProduct;
use App\Models\DeliveryType;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Place order
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'warehouse_id' => 'required|exists:fulfillment_centers,id',
            'delivery_type' => 'required|string|in:30_min,1_day,normal',
            'payment_method' => 'required|in:cod,online',
        ]);

        $user = $request->user();

        // Verify address belongs to user
        $address = \App\Models\Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return $this->error('api.address_not_found', 404);
        }

        // Get user cart
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return $this->error('api.cart_empty', 400);
        }

        // Verify warehouse
        $warehouse = FulfillmentCenter::find($request->warehouse_id);
        if (!$warehouse) {
            return $this->error('api.warehouse_not_found', 404);
        }

        // Check delivery type
        $deliveryType = DeliveryType::where('code', $request->delivery_type)->first();
        if (!$deliveryType) {
            return $this->error('api.delivery_type_invalid', 400);
        }

        // Check if 30_min delivery is supported
        if ($request->delivery_type === '30_min' && !$warehouse->supports_30_min_delivery) {
            return $this->error('api.delivery_type_invalid', 400);
        }

        DB::beginTransaction();
        try {
            // Check stock availability and reserve items
            $orderItems = [];
            $totalAmount = 0;

            foreach ($cart->items as $cartItem) {
                $variant = $cartItem->productVariant;
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $warehouse->id)
                    ->where('product_variant_id', $variant->id)
                    ->first();

                $availableStock = ($warehouseProduct->stock_qty ?? 0) - ($warehouseProduct->reserved_qty ?? 0);

                if ($availableStock < $cartItem->qty) {
                    DB::rollBack();
                    return $this->error('api.insufficient_stock', 400);
                }

                // Reserve stock
                $warehouseProduct->increment('reserved_qty', $cartItem->qty);

                $price = $variant->sale_price ?? $variant->price;
                $itemTotal = $price * $cartItem->qty;
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_variant_id' => $variant->id,
                    'qty' => $cartItem->qty,
                    'price' => $price,
                ];
            }

            // Add delivery charge
            $totalAmount += $deliveryType->price;

            // Generate order number
            $orderNumber = 'ORD' . strtoupper(Str::random(8));

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'warehouse_id' => $warehouse->id,
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'delivery_type' => $request->delivery_type,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'order_status' => 'pending',
                'total_amount' => $totalAmount,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return $this->success($order->load(['items.productVariant', 'address', 'warehouse']), 'api.order_placed');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * Get user orders
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.productVariant', 'address', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->success($orders, 'api.orders_fetched');
    }

    /**
     * Get order details
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['items.productVariant.product', 'address.state', 'address.city', 'address.area', 'warehouse', 'payments'])
            ->first();

        if (!$order) {
            return $this->error('api.order_not_found', 404);
        }

        return $this->success($order, 'api.order_details_fetched');
    }
}

