<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Ecommerce dashboard data - this is the main dashboard
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => null,
            'statistics_type' => $request['statistics_type'] ?? 'this_year',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
        ];

        // Fetch real data from database
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalDelivered = Order::where('order_status', 'delivered')->count();
        $totalCanceled = Order::where('order_status', 'cancelled')->count();

        // Ecommerce dashboard data
        $data = [
            'total_orders' => $totalOrders,
            'new_orders' => $todayOrders,
            'total_delivered' => $totalDelivered,
            'delivered' => $totalDelivered,
            'total_canceled' => $totalCanceled,
            'canceled' => $totalCanceled,
            'total_customers' => 11,
            'new_customers' => 9,
            'total_sellers' => 1,
            'total_stores' => 1,
            'new_stores' => 0,
            'total_services' => 19,
            'total_items' => 19,
            'new_items' => 19,
            'customer' => 11,
            'stores' => 1,
            'delivery_man' => 0,
            'order_received' => 2,
            'order_accepted' => 0,
            'order_rejected' => 0,
            'ready_to_ship' => 0,
            'taksh_assigned' => 0,
            'unassigned_pending' => 0,
            'other_logistics_assigned' => 0,
            'total_assigned' => 0,
            'out_for_pickup' => 0,
            'order_picked_up' => 0,
            'picked_up' => 0,
            'connected_to_hub' => 0,
            'order_received_at_hub' => 0,
            'order_connected_hub_to_center' => 0,
            'received_at_center' => 0,
            'out_for_delivery' => 0,
            'order_delivered' => 1,
            'order_rescheduled' => 0,
            'order_canceled' => 0,
            'on_hold' => 0,
            'reattempt' => 0,
            'return_to_origin' => 0,
            'return_connected_to_hub' => 0,
            'received_at_hub' => 0,
            'hub_connected_to_destination' => 0,
            'order_30min' => [
                'order_received' => 0,
                'seller_order_accepted' => 0,
                'vendor_order_rejected' => 0,
                'ready_to_ship' => 0,
                'taksh_assign' => 0,
                'unassigned_pending' => 0,
                'other_logistics_assign' => 0,
                'total_assign' => 0,
                'out_for_pickup' => 0,
                'order_picked_up' => 0,
                'order_delivered' => 0,
                'order_cancelled' => 0,
                'return_to_seller_delivered' => 0,
                'return_to_seller_rejected' => 0,
                'loss' => 0,
            ],
            'order_fm' => [],
            'order_lm' => [],
            'order_reverse_pickup' => [],
            'order_rt' => [],
            'top_restaurants' => collect([]),
            'popular' => collect([]),
            'top_sell' => collect([]),
            'top_rated_foods' => collect([]),
            'top_deliveryman' => collect([]),
            'top_customers' => collect([]),
        ];

        $total_sell = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $commission = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $delivery_commission = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $label = ['"Jan"', '"Feb"', '"Mar"', '"Apr"', '"May"', '"Jun"', '"Jul"', '"Aug"', '"Sep"', '"Oct"', '"Nov"', '"Dec"'];

        return view('admin-views.dashboard-ecommerce', compact('data', 'params', 'total_sell', 'commission', 'delivery_commission', 'label'));
    }

    public function order(Request $request)
    {
        // Fetch real data from database
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalDelivered = Order::where('order_status', 'delivered')->count();
        $totalCanceled = Order::where('order_status', 'cancelled')->count();

        // Ecommerce dashboard order stats
        $data = [
            'total_orders' => $totalOrders,
            'new_orders' => $todayOrders,
            'total_delivered' => $totalDelivered,
            'delivered' => $totalDelivered,
            'total_canceled' => $totalCanceled,
            'canceled' => $totalCanceled,
            'total_customers' => 11,
            'new_customers' => 9,
            'total_sellers' => 1,
            'total_stores' => 1,
            'new_stores' => 0,
            'total_services' => 19,
            'total_items' => 19,
            'new_items' => 19,
            'order_received' => 2,
            'order_accepted' => 0,
            'order_rejected' => 0,
            'ready_to_ship' => 0,
            'taksh_assigned' => 0,
            'unassigned_pending' => 0,
            'other_logistics_assigned' => 0,
            'total_assigned' => 0,
            'out_for_pickup' => 0,
            'order_picked_up' => 0,
            'picked_up' => 0,
            'connected_to_hub' => 0,
            'received_at_center' => 0,
            'out_for_delivery' => 0,
            'order_delivered' => 1,
            'order_rescheduled' => 0,
            'order_canceled' => 0,
            'on_hold' => 0,
            'reattempt' => 0,
            'return_to_origin' => 0,
            'return_connected_to_hub' => 0,
            'received_at_hub' => 0,
            'hub_connected_to_destination' => 0,
        ];

        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function zone(Request $request)
    {
        // Get params from request or session
        $session_params = session('dash_params') ?? [];
        $params = [
            'zone_id' => $request['zone_id'] ?? $session_params['zone_id'] ?? 'all',
            'module_id' => null,
            'statistics_type' => $session_params['statistics_type'] ?? 'this_year',
            'user_overview' => $session_params['user_overview'] ?? 'overall',
            'commission_overview' => $session_params['commission_overview'] ?? 'this_year',
        ];
        session()->put('dash_params', $params);

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
            'stat_zone' => view('admin-views.partials._zone-change', ['data' => $data, 'params' => $params])->render(),
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

    public function dashboardEcommerce(Request $request)
    {
        // Ecommerce dashboard data
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => null,
            'statistics_type' => $request['statistics_type'] ?? 'this_year',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
        ];

        // Fetch real data from database
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalDelivered = Order::where('order_status', 'delivered')->count();
        $totalCanceled = Order::where('order_status', 'cancelled')->count();

        // Ecommerce dashboard data
        $data = [
            'total_orders' => $totalOrders,
            'new_orders' => $todayOrders,
            'total_delivered' => $totalDelivered,
            'delivered' => $totalDelivered,
            'total_canceled' => $totalCanceled,
            'canceled' => $totalCanceled,
            'total_customers' => 11,
            'new_customers' => 9,
            'total_sellers' => 1,
            'total_stores' => 1,
            'new_stores' => 0,
            'total_services' => 19,
            'total_items' => 19,
            'new_items' => 19,
            'customer' => 11,
            'stores' => 1,
            'delivery_man' => 0,
            'order_received' => 2,
            'order_accepted' => 0,
            'order_rejected' => 0,
            'ready_to_ship' => 0,
            'taksh_assigned' => 0,
            'unassigned_pending' => 0,
            'other_logistics_assigned' => 0,
            'total_assigned' => 0,
            'out_for_pickup' => 0,
            'order_picked_up' => 0,
            'picked_up' => 0,
            'connected_to_hub' => 0,
            'received_at_center' => 0,
            'out_for_delivery' => 0,
            'order_delivered' => $totalDelivered,
            'order_rescheduled' => 0,
            'order_canceled' => $totalCanceled,
            'on_hold' => 0,
            'reattempt' => 0,
            'return_to_origin' => 0,
            'return_connected_to_hub' => 0,
            'received_at_hub' => 0,
            'hub_connected_to_destination' => 0,
            'order_30min' => [
                'order_received' => 0,
                'seller_order_accepted' => 0,
                'vendor_order_rejected' => 0,
                'ready_to_ship' => 0,
                'taksh_assign' => 0,
                'unassigned_pending' => 0,
                'other_logistics_assign' => 0,
                'total_assign' => 0,
                'out_for_pickup' => 0,
                'order_picked_up' => 0,
                'order_delivered' => 0,
                'order_cancelled' => 0,
                'return_to_seller_delivered' => 0,
                'return_to_seller_rejected' => 0,
                'loss' => 0,
            ],
            'order_fm' => [],
            'order_lm' => [],
            'order_reverse_pickup' => [],
            'order_rt' => [],
            'top_restaurants' => collect([]),
            'popular' => collect([]),
            'top_sell' => collect([]),
            'top_rated_foods' => collect([]),
            'top_deliveryman' => collect([]),
            'top_customers' => collect([]),
        ];

        $total_sell = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $commission = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $delivery_commission = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $label = ['"Jan"', '"Feb"', '"Mar"', '"Apr"', '"May"', '"Jun"', '"Jul"', '"Aug"', '"Sep"', '"Oct"', '"Nov"', '"Dec"'];

        return view('admin-views.dashboard-ecommerce', compact('data', 'params', 'total_sell', 'commission', 'delivery_commission', 'label'));
    }

    public function commission_overview(Request $request)
    {
        // Simplified commission overview stats
        $total_sell = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $commission = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $delivery_commission = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $label = ['"Jan"', '"Feb"', '"Mar"', '"Apr"', '"May"', '"Jun"', '"Jul"', '"Aug"', '"Sep"', '"Oct"', '"Nov"', '"Dec"'];

        $gross_sale = array_sum($total_sell);

        return response()->json([
            'view' => view('admin-views.partials._commission-overview-chart', compact('total_sell', 'commission', 'delivery_commission', 'label'))->render(),
            'gross_sale' => '<h6>$' . number_format($gross_sale, 2) . '</h6><span>Gross Sale</span>'
        ], 200);
    }
}
