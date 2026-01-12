@extends('layouts.admin.app')

@section('title', translate('messages.new_joining_requests'))

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .document-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">{{ translate('messages.new_joining_requests') }}</h1>
                <p class="page-header-text">Approve or reject new delivery man registration requests</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Pending Requests</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Search -->
            <form method="GET" action="{{ route('admin.delivery-men.pending') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        @php
                            $searchValue = request('search');
                            $searchValue = is_array($searchValue) ? '' : (string)($searchValue ?? '');
                        @endphp
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" type="search" class="form-control" 
                                   placeholder="Ex : search delivery man email or phone" 
                                   value="{{ $searchValue }}" 
                                   aria-label="Search here">
                            <button type="submit" class="btn btn--secondary">
                                <i class="tio-search"></i>
                            </button>
                        </div>
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
                            <th>Documents</th>
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
                                    @if($deliveryMan->profile_photo)
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
                            <td>{{ $deliveryMan->fulfillmentCenter ? $deliveryMan->fulfillmentCenter->name : 'N/A' }}</td>
                            <td>
                                @if($deliveryMan->vehicle_type)
                                    <span class="badge badge-soft-info">{{ ucfirst($deliveryMan->vehicle_type) }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($deliveryMan->aadhaar_front)
                                        <img src="{{ asset('storage/' . $deliveryMan->aadhaar_front) }}" 
                                             class="document-preview" 
                                             onclick="openImageModal('{{ asset('storage/' . $deliveryMan->aadhaar_front) }}')"
                                             title="Aadhaar Front">
                                    @endif
                                    @if($deliveryMan->driving_license_photo)
                                        <img src="{{ asset('storage/' . $deliveryMan->driving_license_photo) }}" 
                                             class="document-preview" 
                                             onclick="openImageModal('{{ asset('storage/' . $deliveryMan->driving_license_photo) }}')"
                                             title="Driving License">
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small>{{ $deliveryMan->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.delivery-men.show', $deliveryMan->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="tio-visible"></i> View
                                    </a>
                                    <form action="{{ route('admin.delivery-men.approve', $deliveryMan->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                onclick="return confirm('Approve this delivery man?')">
                                            <i class="tio-checkmark"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.delivery-men.reject', $deliveryMan->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Reject this delivery man?')">
                                            <i class="tio-clear"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <p class="text-muted">No pending requests found</p>
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

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Preview" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    // Hide loader function - robust implementation
    function hideLoader() {
        // Use PageLoader API if available
        if (typeof PageLoader !== 'undefined' && PageLoader.hide) {
            PageLoader.hide();
        }
        
        // Direct DOM manipulation fallback
        var loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hide');
            loader.style.display = 'none';
            loader.style.visibility = 'hidden';
            loader.style.opacity = '0';
        }
        
        // jQuery fallback if available
        if (typeof $ !== 'undefined') {
            $('#page-loader').addClass('hide').hide().css({
                'display': 'none',
                'visibility': 'hidden',
                'opacity': '0'
            });
        }
    }
    
    // Hide loader immediately if page is already loaded
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        hideLoader();
    }
    
    // Hide loader when DOM is ready
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            hideLoader();
            setTimeout(function() {
                hideLoader();
            }, 100);
        });
        
        // Hide loader when window is fully loaded
        $(window).on('load', function() {
            setTimeout(function() {
                hideLoader();
            }, 100);
        });
    } else {
        // Fallback if jQuery is not loaded yet
        document.addEventListener('DOMContentLoaded', function() {
            hideLoader();
            setTimeout(function() {
                hideLoader();
            }, 100);
        });
        
        window.addEventListener('load', function() {
            setTimeout(function() {
                hideLoader();
            }, 100);
        });
    }
    
    // Final fallback timeout
    setTimeout(function() {
        hideLoader();
    }, 500);
    
    // Image modal
    function openImageModal(imageSrc) {
        if (typeof $ !== 'undefined') {
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        }
    }
</script>
@endpush

