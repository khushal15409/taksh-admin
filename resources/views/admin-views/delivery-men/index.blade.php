@extends('layouts.admin.app')

@section('title', translate('messages.deliveryman_list'))

@include('admin-views.partials._loader')

@push('css_or_js')
    <style>
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-rejected {
            background: #f8d7da;
            color: #842029;
        }

        .status-active {
            background: #28a745;
            color: #fff;
        }

        .status-inactive {
            background: #dc3545;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{ translate('messages.deliveryman_list') }}</h1>
                    <p class="page-header-text">Manage all delivery men</p>
                </div>
                <div class="col-sm-auto">
                    <a href="{{ route('admin.delivery-men.create') }}" class="btn btn-primary">
                        <i class="tio-add"></i> Add Delivery Man
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                    <h5 class="card-header-title">{{ translate('messages.delivery_men') }}</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Filters -->
                <form method="GET" action="{{ route('admin.delivery-men.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            @php
                                $searchValue = request('search');
                                $searchValue = is_array($searchValue) ? '' : (string) ($searchValue ?? '');
                            @endphp
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name, mobile, email" value="{{ $searchValue }}">
                        </div>
                        <div class="col-md-2">
                            <select name="fulfillment_center_id" class="form-control">
                                <option value="">All Fulfillment Centers</option>
                                @foreach ($fulfillmentCenters as $fc)
                                    @php
                                        $fcId = request('fulfillment_center_id');
                                        $fcId = is_array($fcId) ? null : $fcId;
                                    @endphp
                                    <option value="{{ $fc->id }}" {{ $fcId == $fc->id ? 'selected' : '' }}>
                                        {{ $fc->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="all">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            @php
                                $dateFrom = request('date_from');
                                $dateFrom = is_array($dateFrom) ? '' : (string) ($dateFrom ?? '');
                            @endphp
                            <input type="date" name="date_from" class="form-control" placeholder="Date From"
                                value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-2">
                            @php
                                $dateTo = request('date_to');
                                $dateTo = is_array($dateTo) ? '' : (string) ($dateTo ?? '');
                            @endphp
                            <input type="date" name="date_to" class="form-control" placeholder="Date To"
                                value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Fulfillment Center</th>
                                <th>Vehicle Type</th>
                                <th>Status</th>
                                <th>Active</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveryMen as $deliveryMan)
                                <tr>
                                    <td>{{ $deliveryMan->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($deliveryMan->profile_photo)
                                                <img src="{{ asset('storage/' . $deliveryMan->profile_photo) }}"
                                                    alt="{{ $deliveryMan->name }}"
                                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;"
                                                    onerror="this.src='{{ asset('assets/admin/img/160x160/img1.jpg') }}'">
                                            @else
                                                <img src="{{ asset('assets/admin/img/160x160/img1.jpg') }}"
                                                    alt="{{ $deliveryMan->name }}"
                                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                                            @endif
                                            <strong>{{ $deliveryMan->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $deliveryMan->mobile_number }}</td>
                                    <td>{{ $deliveryMan->email ?? 'N/A' }}</td>
                                    <td>{{ $deliveryMan->fulfillmentCenter ? $deliveryMan->fulfillmentCenter->name : 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($deliveryMan->vehicle_type)
                                            <span
                                                class="badge badge-soft-info">{{ ucfirst($deliveryMan->vehicle_type) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($deliveryMan->status === 'approved')
                                            <span class="status-badge status-approved">Approved</span>
                                        @elseif($deliveryMan->status === 'rejected')
                                            <span class="status-badge status-rejected">Rejected</span>
                                        @else
                                            <span class="status-badge status-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($deliveryMan->user && $deliveryMan->user->is_active)
                                            <span class="status-badge status-active">Active</span>
                                        @else
                                            <span class="status-badge status-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $deliveryMan->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.delivery-men.show', $deliveryMan->id) }}"
                                                class="btn btn-sm btn-info" title="View">
                                                <i class="tio-visible"></i>
                                            </a>
                                            @if($deliveryMan->status === 'approved')
                                            <a href="{{ route('admin.delivery-men.edit', $deliveryMan->id) }}"
                                                class="btn btn-sm btn-primary" title="Edit">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <p class="text-muted">No delivery men found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $deliveryMen->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function hideLoader() {
            if (typeof PageLoader !== 'undefined' && PageLoader.hide) {
                PageLoader.hide();
            }
            var loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('hide');
                loader.style.display = 'none';
                loader.style.visibility = 'hidden';
                loader.style.opacity = '0';
            }
            $('#page-loader').addClass('hide').hide().css({
                'display': 'none',
                'visibility': 'hidden',
                'opacity': '0'
            });
        }

        $(document).ready(function() {
            hideLoader();
            setTimeout(function() {
                hideLoader();
            }, 100);
        });

        $(window).on('load', function() {
            hideLoader();
        });

        if (document.readyState === 'complete') {
            hideLoader();
        }

        setTimeout(function() {
            hideLoader();
        }, 1000);
    </script>
@endpush
