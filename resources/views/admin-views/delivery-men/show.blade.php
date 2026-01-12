@extends('layouts.admin.app')

@section('title', 'Delivery Man Details')

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }
    .image-gallery img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    .image-gallery img:hover {
        border-color: #0177cd;
        transform: scale(1.05);
    }
    .document-item {
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #f9f9f9;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d1e7dd; color: #0f5132; }
    .status-rejected { background: #f8d7da; color: #842029; }
    .status-active { background: #28a745; color: #fff; }
    .status-inactive { background: #dc3545; color: #fff; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Delivery Man Details</h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.delivery-men.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
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
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Main Details -->
        <div class="col-md-8">
            <!-- Personal Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">👤 Personal Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $deliveryMan->name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile Number:</th>
                            <td>{{ $deliveryMan->mobile_number }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $deliveryMan->email ?? 'N/A' }}</td>
                        </tr>
                        @if($deliveryMan->profile_photo)
                        <tr>
                            <th>Profile Photo:</th>
                            <td>
                                <img src="{{ asset('storage/' . $deliveryMan->profile_photo) }}" 
                                     alt="Profile Photo" 
                                     style="max-width: 200px; border-radius: 8px; cursor: pointer;"
                                     onclick="openImageModal('{{ asset('storage/' . $deliveryMan->profile_photo) }}')">
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Address Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">📍 Address Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Address:</th>
                            <td>{{ $deliveryMan->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Pincode:</th>
                            <td>{{ $deliveryMan->pincode ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>State:</th>
                            <td>{{ $deliveryMan->state ? $deliveryMan->state->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>City:</th>
                            <td>{{ $deliveryMan->city ? $deliveryMan->city->name : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Vehicle Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">🚗 Vehicle Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Vehicle Type:</th>
                            <td>{{ ucfirst($deliveryMan->vehicle_type ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th>Deliveryman Type:</th>
                            <td>
                                @if($deliveryMan->deliveryman_type)
                                    <span class="badge badge-{{ $deliveryMan->deliveryman_type == 'salary_based' ? 'primary' : 'info' }}">
                                        {{ ucfirst(str_replace('_', ' ', $deliveryMan->deliveryman_type)) }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Freelancer (Default)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Vehicle Number:</th>
                            <td>{{ $deliveryMan->vehicle_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">📄 Documents</h5>
                </div>
                <div class="card-body">
                    @if($deliveryMan->aadhaar_front || $deliveryMan->aadhaar_back || $deliveryMan->driving_license_photo)
                        <div class="row">
                            @if($deliveryMan->aadhaar_front)
                            <div class="col-md-4 mb-3">
                                <div class="document-item">
                                    <h6 class="mb-2"><strong>Aadhaar Front</strong></h6>
                                    <p class="mb-2 text-muted">Number: {{ $deliveryMan->aadhaar_number ?? 'N/A' }}</p>
                                    <img src="{{ asset('storage/' . $deliveryMan->aadhaar_front) }}" 
                                         alt="Aadhaar Front" 
                                         style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                         onclick="openImageModal('{{ asset('storage/' . $deliveryMan->aadhaar_front) }}')">
                                </div>
                            </div>
                            @endif
                            @if($deliveryMan->aadhaar_back)
                            <div class="col-md-4 mb-3">
                                <div class="document-item">
                                    <h6 class="mb-2"><strong>Aadhaar Back</strong></h6>
                                    <p class="mb-2 text-muted">Number: {{ $deliveryMan->aadhaar_number ?? 'N/A' }}</p>
                                    <img src="{{ asset('storage/' . $deliveryMan->aadhaar_back) }}" 
                                         alt="Aadhaar Back" 
                                         style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                         onclick="openImageModal('{{ asset('storage/' . $deliveryMan->aadhaar_back) }}')">
                                </div>
                            </div>
                            @endif
                            @if($deliveryMan->driving_license_photo)
                            <div class="col-md-4 mb-3">
                                <div class="document-item">
                                    <h6 class="mb-2"><strong>Driving License</strong></h6>
                                    <p class="mb-2 text-muted">Number: {{ $deliveryMan->driving_license_number ?? 'N/A' }}</p>
                                    <img src="{{ asset('storage/' . $deliveryMan->driving_license_photo) }}" 
                                         alt="Driving License" 
                                         style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                         onclick="openImageModal('{{ asset('storage/' . $deliveryMan->driving_license_photo) }}')">
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted">No documents uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Fulfillment Center -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">🏢 Fulfillment Center</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Assigned Center:</th>
                            <td>{{ $deliveryMan->fulfillmentCenter ? $deliveryMan->fulfillmentCenter->name : 'Not Assigned' }}</td>
                        </tr>
                    </table>
                    @if($deliveryMan->status === 'approved')
                    <button type="button" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#assignFcModal">
                        <i class="tio-edit"></i> Change Fulfillment Center
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Status & Actions -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">Status & Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Approval Status:</strong><br>
                        <span class="status-badge status-{{ $deliveryMan->status }}">
                            {{ ucfirst($deliveryMan->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Account Status:</strong><br>
                        @if($deliveryMan->user && $deliveryMan->user->is_active)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </div>
                    @if($deliveryMan->approvedBy)
                    <div class="mb-3">
                        <strong>Approved By:</strong><br>
                        <p class="mb-0">{{ $deliveryMan->approvedBy->name }}</p>
                        @if($deliveryMan->approved_at)
                        <small class="text-muted">{{ $deliveryMan->approved_at->format('d M Y H:i') }}</small>
                        @endif
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Registration Date:</strong><br>
                        <small class="text-muted">{{ $deliveryMan->created_at->format('d M Y H:i') }}</small>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">Actions</h5>
                </div>
                <div class="card-body">
                    @if($deliveryMan->status === 'pending')
                    <form id="approveForm" action="{{ route('admin.delivery-men.approve', $deliveryMan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" id="approveBtn">
                            <span class="btn-text">Approve Delivery Man</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                        </button>
                    </form>
                    <form id="rejectForm" action="{{ route('admin.delivery-men.reject', $deliveryMan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block" id="rejectBtn" onclick="return confirm('Are you sure you want to reject this delivery man?')">
                            <span class="btn-text">Reject Delivery Man</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('admin.delivery-men.edit', $deliveryMan->id) }}" class="btn btn-primary btn-block mb-2">
                        <i class="tio-edit"></i> Edit Delivery Man
                    </a>
                    @if($deliveryMan->status === 'approved')
                    <form id="toggleStatusForm" action="{{ route('admin.delivery-men.toggle-status', $deliveryMan->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn {{ $deliveryMan->user && $deliveryMan->user->is_active ? 'btn-warning' : 'btn-success' }} btn-block" 
                                onclick="return confirm('Are you sure you want to {{ $deliveryMan->user && $deliveryMan->user->is_active ? 'deactivate' : 'activate' }} this delivery man?')">
                            <i class="tio-{{ $deliveryMan->user && $deliveryMan->user->is_active ? 'block' : 'checkmark' }}"></i>
                            {{ $deliveryMan->user && $deliveryMan->user->is_active ? 'Deactivate' : 'Activate' }} Account
                        </button>
                    </form>
                    @endif
                    <p class="text-muted text-center mb-0">Status: <strong>{{ ucfirst($deliveryMan->status) }}</strong></p>
                    @if($deliveryMan->approved_at)
                    <p class="text-muted text-center small">Approved on: {{ $deliveryMan->approved_at->format('d M Y H:i') }}</p>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
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

<!-- Assign Fulfillment Center Modal -->
@if($deliveryMan->status === 'approved')
<div class="modal fade" id="assignFcModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Fulfillment Center</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.delivery-men.assign-fc', $deliveryMan->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Fulfillment Center <span class="text-danger">*</span></label>
                        <select name="fulfillment_center_id" class="form-control" required>
                            <option value="">-- Select Fulfillment Center --</option>
                            @php
                                $fulfillmentCenters = \App\Models\FulfillmentCenter::where('status', 'active')->orderBy('name')->get();
                            @endphp
                            @foreach($fulfillmentCenters as $fc)
                            <option value="{{ $fc->id }}" {{ $deliveryMan->fulfillment_center_id == $fc->id ? 'selected' : '' }}>
                                {{ $fc->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Center</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
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
            loader.style.pointerEvents = 'none';
        }
        
        // jQuery fallback if available
        if (typeof $ !== 'undefined') {
            $('#page-loader').addClass('hide').hide().css({
                'display': 'none',
                'visibility': 'hidden',
                'opacity': '0',
                'pointer-events': 'none'
            });
        }
    }
    
    // Function to wait for all images to load
    function waitForImages(callback) {
        var images = document.querySelectorAll('img');
        var loadedCount = 0;
        var totalImages = images.length;
        
        if (totalImages === 0) {
            callback();
            return;
        }
        
        var imageLoadHandler = function() {
            loadedCount++;
            if (loadedCount === totalImages) {
                callback();
            }
        };
        
        var imageErrorHandler = function() {
            loadedCount++;
            if (loadedCount === totalImages) {
                callback();
            }
        };
        
        images.forEach(function(img) {
            if (img.complete) {
                loadedCount++;
                if (loadedCount === totalImages) {
                    callback();
                }
            } else {
                img.addEventListener('load', imageLoadHandler);
                img.addEventListener('error', imageErrorHandler);
            }
        });
        
        // Fallback timeout if images take too long
        setTimeout(function() {
            callback();
        }, 2000);
    }
    
    // Hide loader when everything is ready
    function hideLoaderWhenReady() {
        waitForImages(function() {
            hideLoader();
            // Additional safety check
            setTimeout(function() {
                hideLoader();
            }, 200);
        });
    }
    
    // Hide loader immediately if page is already loaded
    if (document.readyState === 'complete') {
        hideLoaderWhenReady();
    } else if (document.readyState === 'interactive') {
        hideLoader();
    }
    
    // Hide loader when DOM is ready
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            hideLoaderWhenReady();
        });
        
        // Hide loader when window is fully loaded (including images)
        $(window).on('load', function() {
            setTimeout(function() {
                hideLoaderWhenReady();
            }, 100);
        });
    } else {
        // Fallback if jQuery is not loaded yet
        document.addEventListener('DOMContentLoaded', function() {
            hideLoaderWhenReady();
        });
        
        window.addEventListener('load', function() {
            setTimeout(function() {
                hideLoaderWhenReady();
            }, 100);
        });
    }
    
    // Final fallback timeout - ensure loader is hidden
    setTimeout(function() {
        hideLoader();
    }, 1500);
    
    // Image modal
    function openImageModal(imageSrc) {
        if (typeof $ !== 'undefined') {
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        }
    }
    
    // Handle form submissions
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            $('#approveForm').on('submit', function(e) {
                var $btn = $('#approveBtn');
                $btn.prop('disabled', true);
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-loading').removeClass('d-none');
                if (typeof PageLoader !== 'undefined' && PageLoader.show) {
                    PageLoader.show();
                }
            });
            
            $('#rejectForm').on('submit', function(e) {
                var $btn = $('#rejectBtn');
                $btn.prop('disabled', true);
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-loading').removeClass('d-none');
                if (typeof PageLoader !== 'undefined' && PageLoader.show) {
                    PageLoader.show();
                }
            });
        });
    }
</script>
@endpush

