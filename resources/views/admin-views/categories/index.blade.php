@extends('layouts.admin.app')

@section('title', translate('messages.categories'))

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .dataTables_wrapper {
        padding: 15px 0;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }
    
    .dataTables_wrapper .dataTables_length label {
        font-weight: 500;
        color: #5e6278;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e4e6ef;
        border-radius: 0.475rem;
        padding: 0.55rem 0.75rem;
        font-size: 0.925rem;
        color: #5e6278;
        background-color: #fff;
        min-width: 80px;
    }
    
    .dataTables_wrapper .dataTables_filter {
        text-align: right;
    }
    
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 500;
        color: #5e6278;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e4e6ef;
        border-radius: 0.475rem;
        padding: 0.55rem 0.75rem;
        font-size: 0.925rem;
        color: #5e6278;
        background-color: #fff;
        min-width: 200px;
    }
    
    .no-data-row {
        display: table-row !important;
    }
    
    .no-data-row td {
        padding: 3rem 1rem !important;
    }
    
    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
    }
    
    .pagination .page-link {
        color: #5e6278;
        border-color: #e4e6ef;
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        min-width: 44px;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        color: #009ef7;
        background-color: #f1faff;
        border-color: #009ef7;
        transform: translateY(-1px);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #009ef7;
        border-color: #009ef7;
        color: #fff;
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #b5b5c3;
        background-color: #f5f8fa;
        border-color: #e4e6ef;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .pagination .page-link i {
        font-size: 0.875rem;
        vertical-align: middle;
    }
    
    .pagination-info {
        font-size: 0.875rem;
    }
    
    .pagination-info strong {
        color: #009ef7;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .pagination-info {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .pagination {
            justify-content: center;
            width: 100%;
        }
        
        .pagination .page-link {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
        }
        
        .pagination .page-item:not(.disabled) .page-link {
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-category"></i>
                </span>
                <span>
                    {{ translate('messages.categories') }}
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
                        {{ translate('messages.categories') }} <span class="badge badge-soft-dark ml-2" id="category-count">{{ $categories->total() }}</span>
                    </h5>
                    <div>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn--primary m-0 pull-right">
                            <i class="tio-add-circle"></i> {{ translate('messages.add_category') }}
                        </a>
                    </div>
                </div>
            </div>
            <!-- Table -->
            <div class="card-body">
                <!-- Search and Filter -->
                <form method="GET" action="{{ route('admin.categories.index') }}" id="category-filter-form" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Categories</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or slug..." value="{{ old('search', request('search')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ old('status', request('status')) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', request('status')) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="parent_id">Parent Category</label>
                                <select name="parent_id" id="parent_id" class="form-control">
                                    <option value="">All Categories</option>
                                    <option value="null" {{ old('parent_id', request('parent_id')) == 'null' ? 'selected' : '' }}>Main Categories Only</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', request('parent_id')) == $parent->id || (string)old('parent_id', request('parent_id')) === (string)$parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn--primary w-100">
                                        <i class="tio-search"></i> Filter
                                    </button>
                                    @if(request('search') || request('status') || request('parent_id'))
                                        <a href="{{ route('admin.categories.index') }}" class="btn btn--reset">
                                            <i class="tio-clear"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                           data-hs-datatables-options='{
                             "order": [],
                             "orderCellsTop": true,
                             "paging": false,
                             "searching": false,
                             "info": false,
                             "dom": "rt"
                           }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{ translate('messages.sl') }}</th>
                        <th class="border-0">Image</th>
                        <th class="border-0">Name</th>
                        <th class="border-0">Parent</th>
                        <th class="border-0">Slug</th>
                        <th class="border-0 text-center">{{ translate('messages.status') }}</th>
                        <th class="border-0 text-center">{{ translate('messages.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $key => $category)
                        <tr>
                            <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $key + 1 }}</td>
                            <td>
                                @if($category->image_url)
                                    <img src="{{ \App\CentralLogics\Helpers::get_full_url('category', $category->image_url, \App\CentralLogics\Helpers::getDisk()) }}" 
                                         alt="{{ $category->name }}" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <img src="{{ asset('assets/admin/img/100x100/2.jpg') }}" 
                                         alt="No Image" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                @endif
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ $category->name }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ $category->parent ? $category->parent->name : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ $category->slug }}
                                </span>
                            </td>
                            <td class="text-center">
                                <label class="toggle-switch toggle-switch-sm" for="status-{{ $category->id }}">
                                    <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                           data-id="status-{{ $category->id }}"
                                           data-type="status"
                                           data-image-on='{{ asset('assets/admin/img/modal/zone-status-on.png') }}'
                                           data-image-off="{{ asset('assets/admin/img/modal/zone-status-off.png') }}"
                                           data-title-on="Want to activate this category?"
                                           data-title-off="Want to deactivate this category?"
                                           id="status-{{ $category->id }}" {{ $category->status == 'active' ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <form action="{{ route('admin.categories.status') }}" method="post" id="status-{{ $category->id }}_form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $category->id }}">
                                    <input type="hidden" name="status" value="{{ $category->status == 'active' ? 'inactive' : 'active' }}">
                                </form>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary" href="{{ route('admin.categories.edit', $category->id) }}" title="{{ translate('messages.edit') }}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" 
                                       data-id="category-{{ $category->id }}" 
                                       data-form-id="category-{{ $category->id }}"
                                       data-message="Want to delete this category?" 
                                       data-title="Delete Category"
                                       data-image-on="{{ asset('assets/admin/img/modal/delete-icon.png') }}"
                                       title="{{ translate('messages.delete') }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                            method="post" id="category-{{ $category->id }}_form">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="no-data-row">
                            <td colspan="7" class="text-center py-5">
                                <div class="text-center p-4">
                                    <img class="w-7rem mb-3" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="Image Description">
                                    <h5 class="mb-2">{{ translate('no_data_found') }}</h5>
                                    @if(request('search') || request('status') || request('parent_id'))
                                        <p class="text-muted">Try adjusting your search or filter criteria.</p>
                                        <a href="{{ route('admin.categories.index') }}" class="btn btn--primary btn-sm mt-2">
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
                @if($categories->hasPages() && $categories->total() > 0)
                    <div class="mt-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="pagination-info">
                                    <p class="text-muted mb-0">
                                        Showing <strong>{{ $categories->firstItem() ?? 0 }}</strong> to <strong>{{ $categories->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($categories->total()) }}</strong> categories
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    {{ $categories->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($categories->total() > 0)
                    <div class="mt-3">
                        <p class="text-muted mb-0">
                            Showing all <strong>{{ number_format($categories->total()) }}</strong> categories
                        </p>
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
    
    $(document).ready(function () {
        // Don't show loader if page is already loaded or if we're filtering
        var isFiltering = {{ (request()->has('search') || request()->has('status') || request()->has('parent_id')) ? 'true' : 'false' }};
        if (!isFiltering && typeof PageLoader !== 'undefined' && document.readyState !== 'complete') {
            PageLoader.show();
        } else {
            // If filtering or page already loaded, hide loader immediately
            hideLoader();
        }
        
        var table = $('#columnSearchDatatable').DataTable({
            paging: false,
            searching: false,
            info: false,
            order: [],
            orderCellsTop: true,
            dom: "rt",
            language: {
                zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="Image Description"><h5>{{ translate('no_data_found') }}</h5></div>',
            },
            initComplete: function() {
                // Ensure empty state row is visible
                $('.no-data-row').show();
                // Hide loader immediately after DataTables initialization
                hideLoader();
            }
        });
        
        // Hide loader immediately after table initialization
        hideLoader();
        
        // Ensure empty state is always visible when table is empty
        setTimeout(function() {
            if (table.rows().count() === 0) {
                $('.no-data-row').show();
            }
            hideLoader();
        }, 100);
        
        // Fallback: Hide loader after a short delay to ensure it's hidden
        setTimeout(function() {
            hideLoader();
        }, 300);
        
        // Additional fallback with longer delay
        setTimeout(function() {
            hideLoader();
        }, 600);
        
        $(document).on('click', '.form-alert', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var baseId = $btn.data('form-id') || $btn.data('id');
            var message = $btn.data('message') || 'Want to delete this?';
            var title = $btn.data('title') || 'Delete Confirmation';
            var imageOn = $btn.data('image-on') || '{{ asset('assets/admin/img/modal/delete-icon.png') }}';
            
            $('#toggle-status-title').text(title);
            $('#toggle-status-message').text(message);
            $('#toggle-status-image').attr('src', imageOn);
            $('#toggle-status-ok-button').attr('toggle-ok-button', baseId);
            
            $('#toggle-status-modal').modal('show');
        });
        
        // Hide loader on window load as final fallback
        $(window).on('load', function() {
            hideLoader();
        });
        
        // Force hide loader immediately if page is already loaded
        if (document.readyState === 'complete') {
            hideLoader();
        }
        
        // Additional event listeners to ensure loader is hidden
        window.addEventListener('load', function() {
            hideLoader();
        });
        
        // Final fallback - hide after 1 second
        setTimeout(function() {
            hideLoader();
        }, 1000);
    });
</script>
@endpush

