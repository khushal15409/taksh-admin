<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display customer list
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'customer')
            ->withCount(['orders']);

        // Apply filters
        $this->applyFilters($query, $request);

        $customers = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('admin-views.customer.index', compact('customers'));
    }

    /**
     * Display customer details
     */
    public function show($id)
    {
        $customer = User::where('user_type', 'customer')
            ->with(['addresses.state', 'addresses.city', 'addresses.area'])
            ->findOrFail($id);

        // Get customer statistics
        $totalOrders = $customer->orders()->count();
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');
        $pendingOrders = $customer->orders()->where('order_status', 'pending')->count();
        $deliveredOrders = $customer->orders()->where('order_status', 'delivered')->count();
        $cancelledOrders = $customer->orders()->where('order_status', 'cancelled')->count();

        // Get paginated orders
        $orders = $customer->orders()
            ->with(['items.productVariant.product.images', 'address.state', 'address.city', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin-views.customer.show', compact('customer', 'totalOrders', 'totalSpent', 'pendingOrders', 'deliveredOrders', 'cancelledOrders', 'orders'));
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Search by name, mobile, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        // Filter by verification status
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }
}

