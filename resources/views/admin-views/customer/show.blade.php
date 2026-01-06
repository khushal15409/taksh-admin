@extends('layouts.admin.app')

@section('title', translate('messages.customer_details'))

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .customer-detail-card {
        margin-bottom: 1.5rem;
    }
    
    .customer-detail-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
    }
    
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #5e6278;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        color: #181c32;
    }
    
    .badge-status {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 500;
    }
    
    .badge-active { background-color: #28a745; color: #fff; }
    .badge-inactive { background-color: #dc3545; color: #fff; }
    .badge-verified { background-color: #17a2b8; color: #fff; }
    .badge-unverified { background-color: #6c757d; color: #fff; }
    
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
    
    .customer-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9ecef;
    }
    
    .stat-card {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #181c32;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #5e6278;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <i class="tio-user"></i>
                        </span>
                        <span>
                            {{ translate('messages.customer_details') }} - {{ $customer->name }}
                        </span>
                    </h1>
                </div>
                <div class="col-sm-auto">
                    <a href="{{ route('admin.customer.list') }}" class="btn btn--primary">
                        <i class="tio-arrow-backward"></i> {{ translate('messages.back') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <!-- Customer Information -->
            <div class="col-lg-4">
                <!-- Customer Profile -->
                <div class="card customer-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.customer_information') }}</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($customer->image)
                                <img src="{{ asset('storage/' . $customer->image) }}" 
                                     alt="{{ $customer->name }}" 
                                     class="customer-avatar-large"
                                     onerror="this.src='{{ asset('assets/admin/img/160x160/img1.jpg') }}'">
                            @else
                                <img src="{{ asset('assets/admin/img/160x160/img1.jpg') }}" 
                                     alt="{{ $customer->name }}" 
                                     class="customer-avatar-large">
                            @endif
                        </div>
                        <h4 class="mb-2">{{ $customer->name ?? 'N/A' }}</h4>
                        <div class="mb-3">
                            @if($customer->is_active)
                                <span class="badge badge-status badge-active mr-2">Active</span>
                            @else
                                <span class="badge badge-status badge-inactive mr-2">Inactive</span>
                            @endif
                            @if($customer->is_verified)
                                <span class="badge badge-status badge-verified">Verified</span>
                            @else
                                <span class="badge badge-status badge-unverified">Unverified</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.mobile') }}</div>
                            <div class="info-value">{{ $customer->mobile ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.email') }}</div>
                            <div class="info-value">{{ $customer->email ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Joined Date</div>
                            <div class="info-value">{{ $customer->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">{{ $customer->updated_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Statistics -->
                <div class="card customer-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-value">{{ $totalOrders }}</div>
                                    <div class="stat-label">Total Orders</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-value">₹{{ number_format($totalSpent, 0) }}</div>
                                    <div class="stat-label">Total Spent</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card">
                                    <div class="stat-value" style="font-size: 1.5rem;">{{ $pendingOrders }}</div>
                                    <div class="stat-label">Pending</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card">
                                    <div class="stat-value" style="font-size: 1.5rem;">{{ $deliveredOrders }}</div>
                                    <div class="stat-label">Delivered</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card">
                                    <div class="stat-value" style="font-size: 1.5rem;">{{ $cancelledOrders }}</div>
                                    <div class="stat-label">Cancelled</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Details -->
            <div class="col-lg-8">
                <!-- Addresses -->
                @if($customer->addresses->count() > 0)
                <div class="card customer-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Addresses ({{ $customer->addresses->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($customer->addresses as $address)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        @if($address->is_default)
                                            <span class="badge badge-soft-success mb-2">Default</span>
                                        @endif
                                        <div class="info-row">
                                            <div class="info-label">Name</div>
                                            <div class="info-value">{{ $address->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Mobile</div>
                                            <div class="info-value">{{ $address->mobile ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Address</div>
                                            <div class="info-value">
                                                {{ $address->address_line_1 ?? '' }}
                                                @if($address->address_line_2)
                                                    <br>{{ $address->address_line_2 }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Location</div>
                                            <div class="info-value">
                                                {{ $address->state->name ?? 'N/A' }}, 
                                                {{ $address->city->name ?? 'N/A' }}
                                                @if($address->area)
                                                    , {{ $address->area->name }}
                                                @endif
                                            </div>
                                        </div>
                                        @if($address->pincode)
                                            <div class="info-row">
                                                <div class="info-label">{{ translate('messages.pincode') }}</div>
                                                <div class="info-value">{{ $address->pincode }}</div>
                                            </div>
                                        @endif
                                        @if($address->landmark)
                                            <div class="info-row">
                                                <div class="info-label">{{ translate('messages.landmark') }}</div>
                                                <div class="info-value">{{ $address->landmark }}</div>
                                            </div>
                                        @endif
                                        @if($address->type)
                                            <div class="info-row">
                                                <div class="info-label">Type</div>
                                                <div class="info-value">{{ ucfirst($address->type) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Orders List -->
                <div class="card customer-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Orders ({{ $totalOrders }})</h5>
                    </div>
                    <div class="card-body">
                        @if($orders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ translate('messages.order_id') }}</th>
                                            <th>Date</th>
                                            <th>{{ translate('messages.total_amount') }}</th>
                                            <th>{{ translate('messages.order_status') }}</th>
                                            <th>{{ translate('messages.payment_status') }}</th>
                                            <th>{{ translate('messages.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>
                                                    <strong>{{ $order->order_number }}</strong>
                                                </td>
                                                <td>
                                                    <small>{{ $order->created_at->format('d M Y, h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <strong>₹{{ number_format($order->total_amount, 2) }}</strong>
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
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                       class="btn btn-sm btn--primary" 
                                                       title="{{ translate('messages.view') }}">
                                                        <i class="tio-visible"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            @if($orders->hasPages())
                                <div class="mt-3">
                                    {{ $orders->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <img src="{{ asset('assets/admin/svg/illustrations/empty-state.svg') }}" alt="public" style="max-width: 200px;">
                                <h5 class="mt-3">{{ translate('messages.no_data_found') }}</h5>
                                <p class="text-muted">This customer has not placed any orders yet.</p>
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
        
        // Hide loader when page is fully loaded
        hideLoader();
        
        // Fallback: Hide loader when window is fully loaded
        $(window).on('load', function() {
            setTimeout(function() {
                hideLoader();
            }, 100);
        });
    });
</script>
@endpush

