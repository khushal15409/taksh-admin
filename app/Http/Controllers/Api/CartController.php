<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    use ApiResponseTrait;

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'qty' => 'required|integer|min:1',
            'guest_token' => 'nullable|string',
        ]);

        $user = $request->user();
        $guestToken = $request->input('guest_token');

        // Get or create cart
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            if (!$guestToken) {
                $guestToken = Str::random(32);
            }
            $cart = Cart::firstOrCreate(
                ['guest_token' => $guestToken],
                ['guest_token' => $guestToken]
            );
        }

        // Add or update cart item
        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_variant_id' => $request->product_variant_id,
            ],
            [
                'qty' => $request->qty,
            ]
        );

        return $this->success([
            'cart' => $this->getCartData($cart),
            'guest_token' => $cart->guest_token,
        ], 'api.item_added_to_cart');
    }

    /**
     * Get cart
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $guestToken = $request->input('guest_token');

        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();
        } else {
            if (!$guestToken) {
                return $this->success([
                    'items' => [],
                    'total' => 0,
                    'guest_token' => null,
                ], 'api.cart_fetched');
            }
            $cart = Cart::where('guest_token', $guestToken)->first();
        }

        if (!$cart) {
            return $this->success([
                'items' => [],
                'total' => 0,
                'guest_token' => $guestToken,
            ], 'api.cart_fetched');
        }

        return $this->success([
            'cart' => $this->getCartData($cart),
            'guest_token' => $cart->guest_token,
        ], 'api.cart_fetched');
    }

    /**
     * Update cart item
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'qty' => 'required|integer|min:1',
            'guest_token' => 'nullable|string',
        ]);

        $user = $request->user();
        $guestToken = $request->input('guest_token');

        // Verify cart ownership
        $cartItem = CartItem::find($request->cart_item_id);
        $cart = $cartItem->cart;

        if ($user && $cart->user_id !== $user->id) {
            return $this->error('api.unauthorized', 403);
        }

        if (!$user && $cart->guest_token !== $guestToken) {
            return $this->error('api.unauthorized', 403);
        }

        $cartItem->update(['qty' => $request->qty]);

        return $this->success([
            'cart' => $this->getCartData($cart),
        ], 'api.cart_updated');
    }

    /**
     * Remove item from cart
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $guestToken = $request->input('guest_token');

        $cartItem = CartItem::find($id);
        if (!$cartItem) {
            return $this->error('api.not_found', 404);
        }

        $cart = $cartItem->cart;

        // Verify cart ownership
        if ($user && $cart->user_id !== $user->id) {
            return $this->error('api.unauthorized', 403);
        }

        if (!$user && $cart->guest_token !== $guestToken) {
            return $this->error('api.unauthorized', 403);
        }

        $cartItem->delete();

        return $this->success([
            'cart' => $this->getCartData($cart),
        ], 'api.item_removed_from_cart');
    }

    /**
     * Get cart data with items and total
     */
    private function getCartData($cart)
    {
        $items = $cart->items()->with(['productVariant.product', 'productVariant.images'])->get();
        $total = 0;

        $itemsData = $items->map(function ($item) use (&$total) {
            $variant = $item->productVariant;
            $price = $variant->sale_price ?? $variant->price;
            $itemTotal = $price * $item->qty;
            $total += $itemTotal;

            return [
                'id' => $item->id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'sku' => $variant->sku,
                'price' => $price,
                'qty' => $item->qty,
                'total' => $itemTotal,
                'image' => $variant->images->first()?->image_url,
            ];
        });

        return [
            'items' => $itemsData,
            'total' => $total,
        ];
    }
}
