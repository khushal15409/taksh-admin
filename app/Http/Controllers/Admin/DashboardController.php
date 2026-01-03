<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Simplified dashboard data - you can add actual data logic later
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => null,
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
        ];

        // Mock data for dashboard - replace with actual data later
        $data = [
            'searching_for_dm' => 0,
            'accepted_by_dm' => 0,
            'preparing_in_rs' => 0,
            'picked_up' => 0,
            'delivered' => 0,
            'canceled' => 0,
            'refund_requested' => 0,
            'refunded' => 0,
            'customer' => 0,
            'stores' => 0,
            'delivery_man' => 0,
            'top_restaurants' => collect([]),
            'popular' => collect([]),
            'top_sell' => collect([]),
            'top_rated_foods' => collect([]),
            'top_deliveryman' => collect([]),
            'top_customers' => collect([]),
        ];

        return view('admin-views.dashboard', compact('data', 'params'));
    }

    public function order(Request $request)
    {
        // Simplified order stats - return empty data for now
        $data = [
            'searching_for_dm' => 0,
            'accepted_by_dm' => 0,
            'preparing_in_rs' => 0,
            'picked_up' => 0,
            'delivered' => 0,
            'canceled' => 0,
            'refund_requested' => 0,
            'refunded' => 0,
            'total_orders' => 0,
            'total_items' => 0,
            'total_stores' => 0,
            'total_customers' => 0,
            'new_orders' => 0,
            'new_items' => 0,
            'new_stores' => 0,
            'new_customers' => 0,
        ];

        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function zone(Request $request)
    {
        // Simplified zone stats - return empty data for now
        $data = [
            'searching_for_dm' => 0,
            'accepted_by_dm' => 0,
            'preparing_in_rs' => 0,
            'picked_up' => 0,
            'delivered' => 0,
            'canceled' => 0,
            'refund_requested' => 0,
            'refunded' => 0,
            'total_orders' => 0,
            'total_items' => 0,
            'total_stores' => 0,
            'total_customers' => 0,
        ];

        $total_sell = [];
        $commission = [];
        $delivery_commission = [];
        $popular = collect([]);
        $top_deliveryman = collect([]);
        $top_rated_foods = collect([]);
        $top_restaurants = collect([]);
        $top_customers = collect([]);
        $top_sell = collect([]);

        return response()->json([
            'popular_restaurants' => view('admin-views.partials._popular-restaurants', compact('popular'))->render(),
            'top_deliveryman' => view('admin-views.partials._top-deliveryman', compact('top_deliveryman'))->render(),
            'top_rated_foods' => view('admin-views.partials._top-rated-foods', compact('top_rated_foods'))->render(),
            'top_restaurants' => view('admin-views.partials._top-restaurants', compact('top_restaurants'))->render(),
            'top_customers' => view('admin-views.partials._top-customer', compact('top_customers'))->render(),
            'top_selling_foods' => view('admin-views.partials._top-selling-foods', compact('top_sell'))->render(),
            'order_stats' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render(),
            'user_overview' => view('admin-views.partials._user-overview-chart', compact('data'))->render(),
            'monthly_graph' => view('admin-views.partials._monthly-earning-graph', compact('total_sell', 'commission', 'delivery_commission'))->render(),
            'stat_zone' => view('admin-views.partials._zone-change', compact('data'))->render(),
        ], 200);
    }

    public function user_overview(Request $request)
    {
        // Simplified user overview stats - return empty data for now
        $data = [
            'customer' => 0,
            'stores' => 0,
            'delivery_man' => 0,
        ];

        return response()->json([
            'view' => view('admin-views.partials._user-overview-chart', compact('data'))->render()
        ], 200);
    }
}

