<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\State;
use App\Models\City;
use App\Models\FulfillmentCenter;
use Illuminate\Http\Request;

class ExpressOrderController extends Controller
{
    /**
     * Display Express-30 orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address.state', 'address.city', 'warehouse', 'items.productVariant.product'])
            ->where('delivery_type', 'express_30');

        // Apply filters
        $this->applyFilters($query, $request);

        $orders = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get filter options
        $states = State::orderBy('name', 'asc')->get();
        $fulfillmentCenters = FulfillmentCenter::where('status', 'active')
            ->where('supports_express_30', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin-views.orders.express-30.index', compact('orders', 'states', 'fulfillmentCenters'));
    }

    /**
     * Display Express-30 order details
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'address.state',
            'address.city',
            'address.area',
            'warehouse',
            'items.productVariant.product.images',
            'items.productVariant.variantAttributes.attribute',
            'items.productVariant.variantAttributes.attributeValue',
            'payments',
            'returns'
        ])->where('delivery_type', 'express_30')
        ->findOrFail($id);

        return view('admin-views.orders.express-30.show', compact('order'));
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by state
        if ($request->filled('state_id')) {
            $query->whereHas('address', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->whereHas('address', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        // Filter by fulfillment center
        if ($request->filled('fulfillment_center_id')) {
            $query->where('warehouse_id', $request->fulfillment_center_id);
        }

        // Filter by order status
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer name/mobile
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
            });
        }
    }
}

