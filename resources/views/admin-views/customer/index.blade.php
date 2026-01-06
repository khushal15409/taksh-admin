@extends('layouts.admin.app')

@section('title', translate('messages.customers'))

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
    
    .badge-active { background-color: #28a745; color: #fff; }
    .badge-inactive { background-color: #dc3545; color: #fff; }
    .badge-verified { background-color: #17a2b8; color: #fff; }
    .badge-unverified { background-color: #6c757d; color: #fff; }
    
    .no-data-row {
        display: table-row !important;
    }
    
    .no-data-row td {
        padding: 3rem 1rem !important;
    }
    
    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-users"></i>
                </span>
                <span>{{ translate('messages.customers') }}</span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{ translate('messages.customers') }}
                                <span class="badge badge-soft-dark ml-2">{{ $customers->total() }}</span>
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.customer.list') }}" id="customer-filter-form" class="mb-3">
                            <div class="search-filter-wrapper">
                                <div class="form-group">
                                    <label for="search">{{ translate('messages.search') }}</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Name, Mobile, Email" 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="form-group">
                                    <label for="status">{{ translate('messages.status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">{{ translate('messages.all') }}</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="is_verified">{{ translate('messages.verification_status') }}</label>
                                    <select name="is_verified" id="is_verified" class="form-control">
                                        <option value="">{{ translate('messages.all') }}</option>
                                        <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>Verified</option>
                                        <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>Unverified</option>
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
                                        @if(request()->anyFilled(['search', 'status', 'is_verified', 'date_from', 'date_to']))
                                            <a href="{{ route('admin.customer.list') }}" class="btn btn--reset">
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
                                        <th>{{ translate('messages.name') }}</th>
                                        <th>{{ translate('messages.mobile') }}</th>
                                        <th>{{ translate('messages.email') }}</th>
                                        <th>Total Orders</th>
                                        <th>{{ translate('messages.status') }}</th>
                                        <th>Verification</th>
                                        <th>Joined Date</th>
                                        <th class="text-center">{{ translate('messages.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $key => $customer)
                                        <tr>
                                            <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $key + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($customer->image)
                                                        <img src="{{ asset('storage/' . $customer->image) }}" 
                                                             alt="{{ $customer->name }}" 
                                                             class="customer-avatar mr-2"
                                                             onerror="this.src='{{ asset('assets/admin/img/160x160/img1.jpg') }}'">
                                                    @else
                                                        <img src="{{ asset('assets/admin/img/160x160/img1.jpg') }}" 
                                                             alt="{{ $customer->name }}" 
                                                             class="customer-avatar mr-2">
                                                    @endif
                                                    <strong>{{ $customer->name ?? 'N/A' }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span>{{ $customer->mobile ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $customer->email ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-info">{{ $customer->orders_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                @if($customer->is_active)
                                                    <span class="badge badge-status badge-active">Active</span>
                                                @else
                                                    <span class="badge badge-status badge-inactive">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer->is_verified)
                                                    <span class="badge badge-status badge-verified">Verified</span>
                                                @else
                                                    <span class="badge badge-status badge-unverified">Unverified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $customer->created_at->format('d M Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('admin.customer.view', $customer->id) }}" 
                                                       class="btn btn-sm btn--primary" 
                                                       title="{{ translate('messages.view') }}">
                                                        <i class="tio-visible"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="no-data-row">
                                            <td colspan="9" class="text-center">
                                                <div class="empty--data">
                                                    <img src="{{ asset('assets/admin/svg/illustrations/empty-state.svg') }}" alt="public">
                                                    <h5>{{ translate('messages.no_data_found') }}</h5>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($customers->hasPages())
                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-end">
                                    {{ $customers->links() }}
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
        // Auto-submit form on filter change (optional)
        // $('#status, #is_verified').on('change', function() {
        //     $('#customer-filter-form').submit();
        // });
        
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

