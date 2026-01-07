@extends('layouts.admin.app')

@section('title', 'Vendor Assignment')

@push('css_or_js')
<style>
    .dataTables_wrapper {
        padding: 15px 0;
    }
</style>
@endpush

@section('content')
@include('admin-views.partials._loader')
<div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm mb-2 mb-sm-0">
                        <h1 class="page-header-title">Pending Vendors</h1>
                        <p class="page-header-text">Vendors waiting for salesman verification (auto-matched by pincode)</p>
                    </div>
                </div>
            </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Pending Vendors</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendor Name</th>
                            <th>Shop Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Pincode</th>
                            <th>Location</th>
                            <th>Assigned Salesman</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->id }}</td>
                            <td>{{ $vendor->vendor_name }}</td>
                            <td>{{ $vendor->shop_name }}</td>
                            <td>{{ $vendor->mobile_number }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td>
                                <strong>{{ $vendor->shop_pincode ?? $vendor->pincode ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $vendor->city->name ?? '' }}, {{ $vendor->state->name ?? '' }}</td>
                            <td>
                                @if($vendor->assignedSalesman)
                                    <strong>{{ $vendor->assignedSalesman->name }}</strong><br>
                                    <small class="text-muted">{{ $vendor->assignedSalesman->mobile }}</small><br>
                                    @if(isset($vendor->assignedSalesman->pending_count))
                                    <small class="text-info">Pending: {{ $vendor->assignedSalesman->pending_count }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-warning">{{ ucfirst($vendor->verification_status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.vendor.assignment.show', $vendor->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No pending vendors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    // Function to hide loader - multiple methods to ensure it works
    function hideLoader() {
        // Method 1: Use PageLoader object if available
        if (typeof PageLoader !== 'undefined' && PageLoader.hide) {
            PageLoader.hide();
        }
        // Method 2: Directly hide the loader element
        var loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hide');
            loader.style.display = 'none';
            loader.style.visibility = 'hidden';
            loader.style.opacity = '0';
        }
        // Method 3: jQuery fallback
        $('#page-loader').addClass('hide').hide().css({
            'display': 'none',
            'visibility': 'hidden',
            'opacity': '0'
        });
    }
    
    $(document).ready(function() {
        // Hide loader when page is ready
        hideLoader();
        
        // Additional fallback with delay
        setTimeout(function() {
            hideLoader();
        }, 100);
    });
    
    // Fallback: Hide loader when window is fully loaded
    $(window).on('load', function() {
        hideLoader();
    });
    
    // Force hide loader immediately if page is already loaded
    if (document.readyState === 'complete') {
        hideLoader();
    }
    
    // Final fallback - hide after 1 second
    setTimeout(function() {
        hideLoader();
    }, 1000);
</script>
@endpush

