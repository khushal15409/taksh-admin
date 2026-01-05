@extends('layouts.admin.app')

@section('title', 'Assign Salesman')

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
    .document-item.verified {
        background: #d4edda;
        border-color: #c3e6cb;
    }
    .location-badge {
        display: inline-block;
        padding: 5px 10px;
        background: #e3f2fd;
        border-radius: 4px;
        font-size: 12px;
        margin-top: 5px;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-assigned { background: #cfe2ff; color: #084298; }
</style>
@endpush

@section('content')
@include('admin-views.partials._loader')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Assign Salesman to Vendor</h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.vendor.assignment.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Vendor Details -->
        <div class="col-md-8">
            <!-- Shop Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">🏪 Shop Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Shop Name:</th>
                            <td>{{ $vendor->shop_name }}</td>
                        </tr>
                        <tr>
                            <th>Shop Address:</th>
                            <td>{{ $vendor->shop_address ?? $vendor->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Shop Pincode:</th>
                            <td>{{ $vendor->shop_pincode ?? $vendor->pincode ?? 'N/A' }}</td>
                        </tr>
                        @if($vendor->shop_latitude && $vendor->shop_longitude)
                        <tr>
                            <th>Location (Lat-Long):</th>
                            <td>
                                <span class="location-badge">
                                    📍 {{ $vendor->shop_latitude }}, {{ $vendor->shop_longitude }}
                                </span>
                                <a href="https://www.google.com/maps?q={{ $vendor->shop_latitude }},{{ $vendor->shop_longitude }}" 
                                   target="_blank" class="btn btn-sm btn-link ml-2">View on Map</a>
                            </td>
                        </tr>
                        @endif
                        @if($vendor->category)
                        <tr>
                            <th>Category:</th>
                            <td>{{ $vendor->category->name }}</td>
                        </tr>
                        @endif
                        @if($vendor->shop_images && is_array($vendor->shop_images) && count($vendor->shop_images) > 0)
                        <tr>
                            <th>Shop Images:</th>
                            <td>
                                <div class="image-gallery">
                                    @foreach($vendor->shop_images as $image)
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Shop Image" 
                                         onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Owner Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">👤 Owner Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Owner Name:</th>
                            <td>{{ $vendor->owner_name ?? $vendor->vendor_name }}</td>
                        </tr>
                        <tr>
                            <th>Owner Address:</th>
                            <td>{{ $vendor->owner_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Owner Pincode:</th>
                            <td>{{ $vendor->owner_pincode ?? 'N/A' }}</td>
                        </tr>
                        @if($vendor->owner_latitude && $vendor->owner_longitude)
                        <tr>
                            <th>Location (Lat-Long):</th>
                            <td>
                                <span class="location-badge">
                                    📍 {{ $vendor->owner_latitude }}, {{ $vendor->owner_longitude }}
                                </span>
                                <a href="https://www.google.com/maps?q={{ $vendor->owner_latitude }},{{ $vendor->owner_longitude }}" 
                                   target="_blank" class="btn btn-sm btn-link ml-2">View on Map</a>
                            </td>
                        </tr>
                        @endif
                        @if($vendor->owner_image)
                        <tr>
                            <th>Owner Image:</th>
                            <td>
                                <img src="{{ asset('storage/' . $vendor->owner_image) }}" 
                                     alt="Owner Image" 
                                     style="max-width: 200px; border-radius: 8px; cursor: pointer;"
                                     onclick="openImageModal('{{ asset('storage/' . $vendor->owner_image) }}')">
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">📞 Contact Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Mobile Number:</th>
                            <td>{{ $vendor->mobile_number }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $vendor->email }}</td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>{{ $vendor->city->name ?? '' }}, {{ $vendor->state->name ?? '' }} - {{ $vendor->pincode ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Documents -->
            @if($vendor->documents && $vendor->documents->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">📄 Documents</h5>
                </div>
                <div class="card-body">
                    @foreach($vendor->documents as $document)
                    <div class="document-item {{ $document->is_verified ? 'verified' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</strong>
                                    @if($document->is_verified)
                                    <span class="badge badge-success ml-2">Verified</span>
                                    @else
                                    <span class="badge badge-warning ml-2">Pending</span>
                                    @endif
                                </h6>
                                @if($document->document_number)
                                <p class="mb-1 text-muted">Number: {{ $document->document_number }}</p>
                                @endif
                            </div>
                            <div>
                                <a href="{{ asset('storage/' . $document->document_file) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-primary">
                                    <i class="tio-download"></i> View Document
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Assignment Form & Status -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Verification Status:</strong><br>
                        <span class="status-badge status-{{ $vendor->verification_status }}">
                            {{ ucfirst($vendor->verification_status) }}
                        </span>
                    </div>
                    @if($vendor->assignedSalesman)
                    <div class="mb-3">
                        <strong>Currently Assigned:</strong><br>
                        <p class="mb-0">{{ $vendor->assignedSalesman->name }}</p>
                        <small class="text-muted">{{ $vendor->assignedSalesman->mobile }}</small>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Registration Date:</strong><br>
                        <small class="text-muted">{{ $vendor->created_at->format('d M Y H:i') }}</small>
                    </div>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">Verification Info</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Auto-Match System:</strong><br>
                        Salesmen will automatically see this vendor if they are within 15 KM radius.
                        No manual assignment needed.
                    </div>
                    @if($vendor->shop_latitude && $vendor->shop_longitude)
                    <p><strong>Shop Location:</strong><br>
                    <small>{{ $vendor->shop_latitude }}, {{ $vendor->shop_longitude }}</small></p>
                    <a href="https://www.google.com/maps?q={{ $vendor->shop_latitude }},{{ $vendor->shop_longitude }}" 
                       target="_blank" class="btn btn-sm btn-link">View on Map</a>
                    @else
                    <div class="alert alert-warning">
                        <small>Shop location coordinates are missing. Vendor will not appear to salesmen.</small>
                    </div>
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
@endsection

@push('script_2')
<script>
    // Function to hide loader
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
    
    // Image modal
    function openImageModal(imageSrc) {
        $('#modalImage').attr('src', imageSrc);
        $('#imageModal').modal('show');
    }
</script>
@endpush
