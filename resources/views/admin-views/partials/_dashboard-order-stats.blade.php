<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
        <div class="__dashboard-card-2" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
            <img src="{{asset('assets/admin/img/dashboard/stats/orders.svg')}}" alt="dashboard/stats">
            <h6 class="name">{{ translate('messages.total_orders') }}</h6>
            <h3 class="count">{{ $data['total_orders'] ?? 0 }}</h3>
            <div class="subtxt">{{ $data['new_orders'] ?? 0 }} {{ translate('messages.newly_added') }}</div>
        </div>
    </a>
</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <a href="{{ route('admin.orders.delivered') }}" class="text-decoration-none">
        <div class="__dashboard-card-2" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
            <img src="{{asset('assets/admin/img/dashboard/grocery/delivered.svg')}}" alt="dashboard/stats">
            <h6 class="name">Total Success</h6>
            <h3 class="count">{{ $data['total_delivered'] ?? $data['delivered'] ?? 0 }}</h3>
            <div class="subtxt">Delivered orders</div>
        </div>
    </a>
</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <a href="{{ route('admin.orders.cancelled') }}" class="text-decoration-none">
        <div class="__dashboard-card-2" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
            <img src="{{asset('assets/admin/img/order-status/canceled.svg')}}" alt="dashboard/stats">
            <h6 class="name">Total Cancel</h6>
            <h3 class="count">{{ $data['total_canceled'] ?? $data['canceled'] ?? 0 }}</h3>
            <div class="subtxt">Canceled orders</div>
        </div>
    </a>
</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <div class="__dashboard-card-2">
        <img src="{{asset('assets/admin/img/dashboard/stats/customers.svg')}}" alt="dashboard/stats">
        <h6 class="name">Total Customer</h6>
        <h3 class="count">{{ $data['total_customers'] ?? 0 }}</h3>
        <div class="subtxt">{{ $data['new_customers'] ?? 0 }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <div class="__dashboard-card-2">
        <img src="{{asset('assets/admin/img/dashboard/stats/stores.svg')}}" alt="dashboard/stats">
        <h6 class="name">Total Seller</h6>
        <h3 class="count">{{ $data['total_sellers'] ?? $data['total_stores'] ?? 0 }}</h3>
        <div class="subtxt">{{ $data['new_stores'] ?? 0 }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <div class="__dashboard-card-2">
        <img src="{{asset('assets/admin/img/dashboard/stats/products.svg')}}" alt="dashboard/stats">
        <h6 class="name">Total Services</h6>
        <h3 class="count">{{ $data['total_services'] ?? $data['total_items'] ?? 0 }}</h3>
        <div class="subtxt">{{ $data['new_items'] ?? 0 }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-12">
    <div class="row g-2">
        <!-- 1. Order Received -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Received</span>
                    </h6>
                    <span class="card-title text-3F8CE8">
                        {{$data['order_received'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 2. Order Accepted -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/accepted.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Accepted</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['order_accepted'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 3. Order Rejected -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/order-status/canceled.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Rejected</span>
                    </h6>
                    <span class="card-title text-danger">
                        {{$data['order_rejected'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 4. Ready to Ship -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/packaging.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Ready to Ship</span>
                    </h6>
                    <span class="card-title text-FFA800">
                        {{$data['ready_to_ship'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 5. Taksh Assigned -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/accepted.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Taksh Assigned</span>
                    </h6>
                    <span class="card-title text-info">
                        {{$data['taksh_assigned'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 6. Un-Assigned Pending -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Un-Assigned Pending</span>
                    </h6>
                    <span class="card-title text-warning">
                        {{$data['unassigned_pending'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 7. Other Logistics Assigned -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/accepted.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Other Logistics Assigned</span>
                    </h6>
                    <span class="card-title text-info">
                        {{$data['other_logistics_assigned'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 8. Total Assigned -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/accepted.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Total Assigned</span>
                    </h6>
                    <span class="card-title text-primary">
                        {{$data['total_assigned'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 9. Out for Pickup -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/packaging.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Out for Pickup</span>
                    </h6>
                    <span class="card-title text-FFA800">
                        {{$data['out_for_pickup'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 10. Order Picked-Up -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Picked-Up</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['order_picked_up'] ?? $data['picked_up'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 11. Order Connected to Hub -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Connected to Hub</span>
                    </h6>
                    <span class="card-title text-info">
                        {{$data['connected_to_hub'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 12. Order Received at Center -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Received at Center</span>
                    </h6>
                    <span class="card-title text-3F8CE8">
                        {{$data['received_at_center'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 13. Out for Delivery -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Out for Delivery</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['out_for_delivery'] ?? $data['picked_up'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 14. Order Delivered -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/delivered.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Delivered</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['order_delivered'] ?? $data['delivered'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 15. Order Rescheduled -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Rescheduled</span>
                    </h6>
                    <span class="card-title text-warning">
                        {{$data['order_rescheduled'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 16. Order Cancelled -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/order-status/canceled.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Cancelled</span>
                    </h6>
                    <span class="card-title text-danger">
                        {{$data['order_canceled'] ?? $data['canceled'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 17. On Hold -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/order-status/canceled.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>On Hold</span>
                    </h6>
                    <span class="card-title text-warning">
                        {{$data['on_hold'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 18. Order Reattempt -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Order Reattempt</span>
                    </h6>
                    <span class="card-title text-info">
                        {{$data['reattempt'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 19. Return to Origin -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Return to Origin</span>
                    </h6>
                    <span class="card-title text-warning">
                        {{$data['return_to_origin'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 20. Return to Origin Connected to Hub -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Return to Origin Connected to Hub</span>
                    </h6>
                    <span class="card-title text-info">
                        {{$data['return_connected_to_hub'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 21. Received at Hub -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Received at Hub</span>
                    </h6>
                    <span class="card-title text-3F8CE8">
                        {{$data['received_at_hub'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>

        <!-- 22. Hub Connected to Destination -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a class="order--card h-100" href="{{route('admin.dashboard')}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('assets/admin/img/dashboard/grocery/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>Hub Connected to Destination</span>
                    </h6>
                    <span class="card-title text-info">
                        {{$data['hub_connected_to_destination'] ?? 0}}
                    </span>
                </div>
            </a>
        </div>
    </div>
</div>
