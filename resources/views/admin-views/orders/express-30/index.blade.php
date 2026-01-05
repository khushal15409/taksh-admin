@extends('layouts.admin.app')

@section('title', translate('messages.express_30_orders'))

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .search-filter-wrapper {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    
    .search-filter-wrapper .form-group {
        margin-bottom: 0;
        flex: 1;
        min-width: 200px;
    }
    
    .search-filter-wrapper .form-group label {
        font-weight: 500;
        color: #5e6278;
        margin-bottom: 5px;
        font-size: 0.875rem;
    }
    
    .badge-status {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 500;
    }
    
    .badge-express {
        background-color: #f0ad4e;
        color: #fff;
    }
    
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-confirmed { background-color: #17a2b8; color: #fff; }
    .badge-processing { background-color: #6c757d; color: #fff; }
    .badge-shipped { background-color: #007bff; color: #fff; }
    .badge-delivered { background-color: #28a745; color: #fff; }
    .badge-cancelled { background-color: #dc3545; color: #fff; }
    
    .badge-payment-pending { background-color: #ffc107; color: #000; }
    .badge-payment-paid { background-color: #28a745; color: #fff; }
    .badge-payment-failed { background-color: #dc3545; color: #fff; }
    .badge-payment-refunded { background-color: #6c757d; color: #fff; }
    
    .no-data-row {
        display: table-row !important;
    }
    
    .no-data-row td {
        padding: 3rem 1rem !important;
    }
    
    .deadline-warning {
        color: #dc3545;
        font-weight: 600;
    }
    
    .deadline-ok {
        color: #28a745;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-shopping-cart"></i>
                </span>
                <span>
                    {{ translate('messages.express_30_orders') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{ translate('messages.express_30_orders') }}
                                <span class="badge badge-soft-dark ml-2">{{ $orders->total() }}</span>
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.orders.express-30.index') }}" id="order-filter-form" class="mb-3">
                            <div class="search-filter-wrapper">
                                <div class="form-group">
                                    <label for="search">{{ translate('messages.search') }}</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Order ID, Customer Name/Mobile" 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="form-group">
                                    <label for="state_id">{{ translate('messages.state') }}</label>
                                    <select name="state_id" id="state_id" class="form-control">
                                        <option value="">{{ translate('messages.all_states') }}</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="city_id">{{ translate('messages.city') }}</label>
                                    <select name="city_id" id="city_id" class="form-control">
                                        <option value="">{{ translate('messages.all_cities') }}</option>
                                        @if(request('state_id'))
                                            @php
                                                $cities = \App\Models\City::where('state_id', request('state_id'))->get();
                                            @endphp
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fulfillment_center_id">{{ translate('messages.fulfillment_center') }}</label>
                                    <select name="fulfillment_center_id" id="fulfillment_center_id" class="form-control">
                                        <option value="">{{ translate('messages.all_fulfillment_centers') }}</option>
                                        @foreach($fulfillmentCenters as $center)
                                            <option value="{{ $center->id }}" {{ request('fulfillment_center_id') == $center->id ? 'selected' : '' }}>
                                                {{ $center->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="order_status">{{ translate('messages.order_status') }}</label>
                                    <select name="order_status" id="order_status" class="form-control">
                                        <option value="">{{ translate('messages.all_status') }}</option>
                                        <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ request('order_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="payment_method">{{ translate('messages.payment_method') }}</label>
                                    <select name="payment_method" id="payment_method" class="form-control">
                                        <option value="">{{ translate('messages.all_payment_methods') }}</option>
                                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                                        <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="date_from">{{ translate('messages.date_from') }}</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="form-group">
                                    <label for="date_to">{{ translate('messages.date_to') }}</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn--primary">
                                            <i class="tio-search"></i> {{ translate('messages.filter') }}
                                        </button>
                                        @if(request()->anyFilled(['search', 'state_id', 'city_id', 'fulfillment_center_id', 'order_status', 'payment_method', 'date_from', 'date_to']))
                                            <a href="{{ route('admin.orders.express-30.index') }}" class="btn btn--reset">
                                                <i class="tio-clear"></i> {{ translate('messages.clear') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ translate('messages.sl') }}</th>
                                        <th>{{ translate('messages.order_id') }}</th>
                                        <th>{{ translate('messages.customer') }}</th>
                                        <th>{{ translate('messages.total_amount') }}</th>
                                        <th>{{ translate('messages.payment_method') }}</th>
                                        <th>{{ translate('messages.order_status') }}</th>
                                        <th>{{ translate('messages.payment_status') }}</th>
                                        <th>{{ translate('messages.fulfillment_center') }}</th>
                                        <th>{{ translate('messages.deadline') }}</th>
                                        <th>{{ translate('messages.state') }} / {{ translate('messages.city') }}</th>
                                        <th>{{ translate('messages.order_date') }}</th>
                                        <th class="text-center">{{ translate('messages.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $key => $order)
                                        @php
                                            $isDeadlinePassed = $order->estimated_delivery_time && $order->estimated_delivery_time->isPast() && $order->order_status != 'delivered';
                                        @endphp
                                        <tr>
                                            <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $key + 1 }}</td>
                                            <td>
                                                <span class="font-weight-bold">{{ $order->order_number }}</span>
                                                <br>
                                                <span class="badge badge-express">Express-30</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->user->name ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $order->user->mobile ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">₹{{ number_format($order->total_amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-info">
                                                    {{ strtoupper($order->payment_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-status badge-{{ $order->order_status }}">
                                                    {{ ucfirst($order->order_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-status badge-payment-{{ $order->payment_status }}">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $order->warehouse->name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if($order->estimated_delivery_time)
                                                    <small class="{{ $isDeadlinePassed ? 'deadline-warning' : 'deadline-ok' }}">
                                                        {{ $order->estimated_delivery_time->format('d M Y, h:i A') }}
                                                        @if($isDeadlinePassed)
                                                            <br><span class="text-danger">Overdue</span>
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $order->address->state->name ?? 'N/A' }} / 
                                                    {{ $order->address->city->name ?? 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <small>{{ $order->created_at->format('d M Y, h:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn--container justify-content-center">
                                                    <a class="btn action-btn btn--primary btn-outline-primary" 
                                                       href="{{ route('admin.orders.express-30.show', $order->id) }}" 
                                                       title="{{ translate('messages.view') }}">
                                                        <i class="tio-visible"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="no-data-row">
                                            <td colspan="12" class="text-center py-5">
                                                <div class="text-center p-4">
                                                    <img class="w-7rem mb-3" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="Image Description">
                                                    <h5 class="mb-2">{{ translate('messages.no_data_found') }}</h5>
                                                    @if(request()->anyFilled(['search', 'state_id', 'city_id', 'fulfillment_center_id', 'order_status', 'payment_method', 'date_from', 'date_to']))
                                                        <p class="text-muted">Try adjusting your search or filter criteria.</p>
                                                        <a href="{{ route('admin.orders.express-30.index') }}" class="btn btn--primary btn-sm mt-2">
                                                            <i class="tio-clear"></i> Clear Filters
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($orders->hasPages() && $orders->total() > 0)
                            <div class="mt-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="pagination-info">
                                            <p class="text-muted mb-0">
                                                Showing <strong>{{ $orders->firstItem() ?? 0 }}</strong> to <strong>{{ $orders->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($orders->total()) }}</strong> orders
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            {{ $orders->appends(request()->query())->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    $(document).ready(function() {
        // Load cities when state changes
        $('#state_id').on('change', function() {
            var stateId = $(this).val();
            var citySelect = $('#city_id');
            
            citySelect.html('<option value="">{{ translate('messages.all_cities') }}</option>');
            
            if (stateId) {
                $.ajax({
                    url: '{{ route('admin.get-cities') }}',
                    method: 'GET',
                    data: { state_id: stateId },
                    success: function(response) {
                        if (response.cities && response.cities.length > 0) {
                            response.cities.forEach(function(city) {
                                citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                            });
                        }
                    }
                });
            }
        });
        
        // Hide loader
        function hideLoader() {
            if (typeof PageLoader !== 'undefined' && PageLoader.hide) {
                PageLoader.hide();
            }
            var loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('hide');
                loader.style.display = 'none';
            }
        }
        
        hideLoader();
    });
</script>
@endpush

