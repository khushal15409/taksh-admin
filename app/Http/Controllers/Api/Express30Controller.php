<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\FulfillmentCenter;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WarehouseProduct;
use App\Models\Address;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Express30Controller extends Controller
{
    use ApiResponseTrait;

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    /**
     * Find nearest fulfillment center within radius
     */
    private function findNearestFulfillmentCenter($latitude, $longitude)
    {
        $fulfillmentCenters = FulfillmentCenter::where('status', 'active')
            ->where('supports_express_30', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearest = null;
        $minDistance = null;

        foreach ($fulfillmentCenters as $center) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $center->latitude,
                $center->longitude
            );

            $radius = $center->express_radius_km ?? 5;

            if ($distance <= $radius) {
                if ($nearest === null || $distance < $minDistance) {
                    $nearest = $center;
                    $minDistance = $distance;
                }
            }
        }

        return $nearest;
    }

    /**
     * Get express 30 products
     * GET /api/express-30/products
     */
    public function products(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Find nearest fulfillment center
        $fulfillmentCenter = $this->findNearestFulfillmentCenter($latitude, $longitude);

        if (!$fulfillmentCenter) {
            return $this->error('express.not_available', 404);
        }

        // Get express-eligible products with available stock
        $products = Product::where('is_express_30', true)
            ->where('status', 'active')
            ->with(['variants' => function ($query) use ($fulfillmentCenter) {
                $query->where('status', 'active')
                    ->whereHas('warehouseProducts', function ($q) use ($fulfillmentCenter) {
                        $q->where('warehouse_id', $fulfillmentCenter->id)
                            ->whereRaw('(stock_qty - reserved_qty) > 0');
                    });
            }])
            ->whereHas('variants', function ($query) use ($fulfillmentCenter) {
                $query->where('status', 'active')
                    ->whereHas('warehouseProducts', function ($q) use ($fulfillmentCenter) {
                        $q->where('warehouse_id', $fulfillmentCenter->id)
                            ->whereRaw('(stock_qty - reserved_qty) > 0');
                    });
            })
            ->with(['images', 'category', 'brand'])
            ->get();

        // Format products with stock information
        $formattedProducts = $products->map(function ($product) use ($fulfillmentCenter) {
            $variants = $product->variants->map(function ($variant) use ($fulfillmentCenter) {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $fulfillmentCenter->id)
                    ->where('product_variant_id', $variant->id)
                    ->first();

                $availableStock = ($warehouseProduct->stock_qty ?? 0) - ($warehouseProduct->reserved_qty ?? 0);

                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'available_stock' => $availableStock,
                ];
            });

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'images' => $product->images->map(fn($img) => [
                    'id' => $img->id,
                    'image_url' => $img->image_url,
                    'is_primary' => $img->is_primary,
                ]),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,
                'brand' => $product->brand ? [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name,
                ] : null,
                'variants' => $variants,
            ];
        });

        return $this->success([
            'fulfillment_center' => [
                'id' => $fulfillmentCenter->id,
                'name' => $fulfillmentCenter->name,
            ],
            'products' => $formattedProducts,
        ], 'express.products_loaded');
    }

    /**
     * Place express 30 order
     * POST /api/express-30/order
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:product_variants,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:online,cod',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Validate arrays have same length
        if (count($request->product_id) !== count($request->quantity)) {
            return $this->validationError([
                'product_id' => ['Product IDs and quantities must have the same count'],
            ]);
        }

        $user = $request->user();

        // Verify address belongs to user
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return $this->error('api.address_not_found', 404);
        }

        // Find nearest fulfillment center
        $fulfillmentCenter = $this->findNearestFulfillmentCenter(
            $request->latitude,
            $request->longitude
        );

        if (!$fulfillmentCenter) {
            return $this->error('express.not_available', 404);
        }

        DB::beginTransaction();
        try {
            $orderItems = [];
            $totalAmount = 0;

            // Validate stock and prepare order items
            foreach ($request->product_id as $index => $productVariantId) {
                $quantity = $request->quantity[$index];

                // Get product variant
                $variant = \App\Models\ProductVariant::with('product')->find($productVariantId);
                if (!$variant) {
                    DB::rollBack();
                    return $this->error('api.product_variant_not_found', 404);
                }

                // Check if product is express eligible
                if (!$variant->product->is_express_30) {
                    DB::rollBack();
                    return $this->error('express.product_not_eligible', 400);
                }

                // Check stock availability
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $fulfillmentCenter->id)
                    ->where('product_variant_id', $productVariantId)
                    ->first();

                $availableStock = ($warehouseProduct->stock_qty ?? 0) - ($warehouseProduct->reserved_qty ?? 0);

                if ($availableStock < $quantity) {
                    DB::rollBack();
                    return $this->error('api.insufficient_stock', 400);
                }

                // Reserve stock
                if ($warehouseProduct) {
                    $warehouseProduct->increment('reserved_qty', $quantity);
                } else {
                    DB::rollBack();
                    return $this->error('api.insufficient_stock', 400);
                }

                $price = $variant->sale_price ?? $variant->price;
                $itemTotal = $price * $quantity;
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_variant_id' => $productVariantId,
                    'qty' => $quantity,
                    'price' => $price,
                ];
            }

            // Generate order number
            $orderNumber = 'EXP' . strtoupper(Str::random(8));

            // Calculate estimated delivery time (30 minutes from now)
            $estimatedDeliveryTime = Carbon::now()->addMinutes(30);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'warehouse_id' => $fulfillmentCenter->id,
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'delivery_type' => 'express_30',
                'estimated_delivery_time' => $estimatedDeliveryTime,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'order_status' => 'pending',
                'total_amount' => $totalAmount,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return $this->success([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'estimated_delivery' => $estimatedDeliveryTime->format('Y-m-d H:i'),
            ], 'express.order_placed');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Express 30 Order Error: ' . $e->getMessage());
            return $this->error('api.server_error', 500);
        }
    }
}
