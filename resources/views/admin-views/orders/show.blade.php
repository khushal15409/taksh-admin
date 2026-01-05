@extends('layouts.admin.app')

@section('title', translate('messages.order_details'))

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .order-detail-card {
        margin-bottom: 1.5rem;
    }
    
    .order-detail-card .card-header {
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
                            <i class="tio-shopping-cart"></i>
                        </span>
                        <span>
                            {{ translate('messages.order_details') }} - {{ $order->order_number }}
                        </span>
                    </h1>
                </div>
                <div class="col-sm-auto">
                    <a href="{{ url()->previous() }}" class="btn btn--primary">
                        <i class="tio-arrow-backward"></i> {{ translate('messages.back') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <!-- Order Information -->
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card order-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.order_items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ translate('messages.product') }}</th>
                                        <th>{{ translate('messages.variant') }}</th>
                                        <th class="text-center">{{ translate('messages.quantity') }}</th>
                                        <th class="text-right">{{ translate('messages.price') }}</th>
                                        <th class="text-right">{{ translate('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $product = $item->productVariant->product ?? null;
                                                        $primaryImage = $product && $product->images ? $product->images->where('is_primary', true)->first() ?? $product->images->first() : null;
                                                    @endphp
                                                    @if($primaryImage)
                                                        <img src="{{ \App\CentralLogics\Helpers::get_full_url('product', $primaryImage->image_url, \App\CentralLogics\Helpers::getDisk()) }}" 
                                                             alt="{{ $product->name ?? 'Product' }}" 
                                                             class="img-thumbnail mr-2" 
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $product->name ?? 'N/A' }}</strong><br>
                                                        <small class="text-muted">SKU: {{ $item->productVariant->sku ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->productVariant && $item->productVariant->variantAttributes)
                                                    @foreach($item->productVariant->variantAttributes as $attr)
                                                        <small>
                                                            <strong>{{ $attr->attribute->name ?? '' }}:</strong> 
                                                            {{ $attr->attributeValue->value ?? '' }}
                                                        </small><br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                                            <td class="text-right">₹{{ number_format($item->price * $item->qty, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>{{ translate('messages.total_amount') }}:</strong></td>
                                        <td class="text-right"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Order Timeline -->
                <div class="card order-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.order_timeline') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="info-row">
                                <div class="info-label">{{ translate('messages.order_placed') }}</div>
                                <div class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            @if($order->order_status == 'confirmed' || in_array($order->order_status, ['processing', 'shipped', 'delivered']))
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.confirmed') }}</div>
                                    <div class="info-value">{{ $order->updated_at->format('d M Y, h:i A') }}</div>
                                </div>
                            @endif
                            @if(in_array($order->order_status, ['processing', 'shipped', 'delivered']))
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.processing') }}</div>
                                    <div class="info-value">{{ $order->updated_at->format('d M Y, h:i A') }}</div>
                                </div>
                            @endif
                            @if(in_array($order->order_status, ['shipped', 'delivered']))
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.shipped') }}</div>
                                    <div class="info-value">{{ $order->updated_at->format('d M Y, h:i A') }}</div>
                                </div>
                            @endif
                            @if($order->order_status == 'delivered')
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.delivered') }}</div>
                                    <div class="info-value">{{ $order->updated_at->format('d M Y, h:i A') }}</div>
                                </div>
                            @endif
                            @if($order->order_status == 'cancelled')
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.cancelled') }}</div>
                                    <div class="info-value">{{ $order->updated_at->format('d M Y, h:i A') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Information -->
            <div class="col-lg-4">
                <!-- Order Status -->
                <div class="card order-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.order_status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.order_status') }}</div>
                            <div class="info-value">
                                <span class="badge badge-status badge-{{ $order->order_status }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.payment_status') }}</div>
                            <div class="info-value">
                                <span class="badge badge-status badge-payment-{{ $order->payment_status }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.payment_method') }}</div>
                            <div class="info-value">{{ strtoupper($order->payment_method) }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.delivery_type') }}</div>
                            <div class="info-value">
                                @if($order->delivery_type == '30_min')
                                    <span class="badge badge-soft-warning">30 Min</span>
                                @elseif($order->delivery_type == '1_day')
                                    <span class="badge badge-soft-info">1 Day</span>
                                @elseif($order->delivery_type == 'express_30')
                                    <span class="badge badge-express">Express-30</span>
                                @else
                                    <span class="badge badge-soft-secondary">Normal</span>
                                @endif
                            </div>
                        </div>
                        @if($order->estimated_delivery_time)
                            <div class="info-row">
                                <div class="info-label">{{ translate('messages.estimated_delivery') }}</div>
                                <div class="info-value">{{ $order->estimated_delivery_time->format('d M Y, h:i A') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="card order-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.customer_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.name') }}</div>
                            <div class="info-value">{{ $order->user->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.mobile') }}</div>
                            <div class="info-value">{{ $order->user->mobile ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.email') }}</div>
                            <div class="info-value">{{ $order->user->email ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Address -->
                <div class="card order-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.delivery_address') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-value">
                                <strong>{{ $order->address->name ?? 'N/A' }}</strong><br>
                                {{ $order->address->address_line_1 ?? '' }}<br>
                                @if($order->address->address_line_2)
                                    {{ $order->address->address_line_2 }}<br>
                                @endif
                                {{ $order->address->city->name ?? '' }}, 
                                {{ $order->address->state->name ?? '' }}<br>
                                @if($order->address->pincode)
                                    {{ translate('messages.pincode') }}: {{ $order->address->pincode }}<br>
                                @endif
                                @if($order->address->landmark)
                                    {{ translate('messages.landmark') }}: {{ $order->address->landmark }}<br>
                                @endif
                                <strong>{{ translate('messages.mobile') }}:</strong> {{ $order->address->mobile ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Fulfillment Center -->
                <div class="card order-detail-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.fulfillment_center') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">{{ translate('messages.name') }}</div>
                            <div class="info-value">{{ $order->warehouse->name ?? 'N/A' }}</div>
                        </div>
                        @if($order->warehouse)
                            <div class="info-row">
                                <div class="info-label">{{ translate('messages.location') }}</div>
                                <div class="info-value">
                                    {{ $order->warehouse->city->name ?? '' }}, 
                                    {{ $order->warehouse->state->name ?? '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Payment Information -->
                @if($order->payments && $order->payments->count() > 0)
                    <div class="card order-detail-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ translate('messages.payment_information') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach($order->payments as $payment)
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.transaction_id') }}</div>
                                    <div class="info-value">{{ $payment->transaction_id ?? 'N/A' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.amount') }}</div>
                                    <div class="info-value">₹{{ number_format($payment->amount ?? 0, 2) }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.gateway') }}</div>
                                    <div class="info-value">{{ $payment->gateway ?? 'N/A' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">{{ translate('messages.status') }}</div>
                                    <div class="info-value">{{ ucfirst($payment->status ?? 'N/A') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    $(document).ready(function() {
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

