@extends('layouts.admin.app')

@section('title', translate('messages.banners'))

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    /* DataTable Controls Styling */
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
    
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #009ef7;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(0, 158, 247, 0.25);
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
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #009ef7;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(0, 158, 247, 0.25);
    }
    
    .dataTables_wrapper .dataTables_info {
        padding-top: 0.85em;
        font-size: 0.925rem;
        color: #5e6278;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 15px;
    }
    
    .dataTables_wrapper .dataTables_paginate .pagination {
        margin: 0;
        justify-content: flex-end;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin-left: 0.25rem;
        border: 1px solid #e4e6ef;
        border-radius: 0.475rem;
        color: #5e6278;
        background-color: #fff;
        cursor: pointer;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #009ef7;
        background-color: #f1faff;
        border-color: #009ef7;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        color: #fff;
        background-color: #009ef7;
        border-color: #009ef7;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: #b5b5c3;
        cursor: not-allowed;
        background-color: #f5f8fa;
    }
    
    @media (max-width: 767px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            text-align: left;
            margin-bottom: 10px;
        }
        
        .dataTables_wrapper .dataTables_filter label {
            justify-content: flex-start;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            text-align: center;
        }
        
        .dataTables_wrapper .dataTables_paginate .pagination {
            justify-content: center;
        }
    }
    
    /* Ensure DataTable controls are visible */
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: block !important;
        visibility: visible !important;
    }
    
    .dataTables_wrapper .dataTables_length {
        float: left;
        padding-top: 0.755em;
    }
    
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        padding-top: 0.755em;
    }
    
    .dataTables_wrapper .dataTables_info {
        float: left;
        padding-top: 0.755em;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        float: right;
        text-align: right;
        padding-top: 0.25em;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-image"></i>
                </span>
                <span>
                    {{ translate('messages.banners') }}
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
                        {{ translate('messages.banners') }} <span class="badge badge-soft-dark ml-2" id="banner-count">{{ $banners->total() }}</span>
                    </h5>
                    <div>
                        <a href="{{ route('admin.banner.create') }}" class="btn btn--primary m-0 pull-right">
                            <i class="tio-add-circle"></i> {{ translate('messages.add_banner') }}
                        </a>
                    </div>
                </div>
            </div>
            <!-- Table -->
            <div class="card-body">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                           data-hs-datatables-options='{
                             "order": [],
                             "orderCellsTop": true,
                             "paging": true,
                             "pageLength": 25,
                             "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                             "searching": true,
                             "info": true,
                             "dom": "<\"row\"<\"col-sm-12 col-md-6\"l><\"col-sm-12 col-md-6\"f>>rtip"
                           }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{ translate('messages.sl') }}</th>
                        <th class="border-0">Image</th>
                        <th class="border-0">Title</th>
                        <th class="border-0">Position</th>
                        <th class="border-0">Redirect Type</th>
                        <th class="border-0">Sort Order</th>
                        <th class="border-0">Start Date</th>
                        <th class="border-0">End Date</th>
                        <th class="border-0 text-center">{{ translate('messages.status') }}</th>
                        <th class="border-0 text-center">{{ translate('messages.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($banners as $key => $banner)
                        <tr>
                            <td>{{ $banners->firstItem() + $key }}</td>
                            <td>
                                @if($banner->image_url)
                                    <img src="{{ \App\CentralLogics\Helpers::get_full_url('banner', $banner->image_url, \App\CentralLogics\Helpers::getDisk()) }}" 
                                         alt="{{ $banner->title }}" 
                                         class="img-thumbnail" 
                                         style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <img src="{{ asset('assets/admin/img/900x400/img1.jpg') }}" 
                                         alt="No Image" 
                                         class="img-thumbnail" 
                                         style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                @endif
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ Str::limit($banner->title, 30, '...') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-soft-info">
                                    {{ ucfirst(str_replace('_', ' ', $banner->position)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-soft-secondary">
                                    {{ ucfirst($banner->redirect_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ $banner->sort_order }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ $banner->start_date ? $banner->start_date->format('Y-m-d') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{ $banner->end_date ? $banner->end_date->format('Y-m-d') : 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <label class="toggle-switch toggle-switch-sm" for="status-{{ $banner->id }}">
                                    <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                           data-id="status-{{ $banner->id }}"
                                           data-type="status"
                                           data-image-on='{{ asset('assets/admin/img/modal/zone-status-on.png') }}'
                                           data-image-off="{{ asset('assets/admin/img/modal/zone-status-off.png') }}"
                                           data-title-on="Want to activate this banner?"
                                           data-title-off="Want to deactivate this banner?"
                                           id="status-{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <form action="{{ route('admin.banner.status') }}" method="post" id="status-{{ $banner->id }}_form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $banner->id }}">
                                    <input type="hidden" name="status" value="{{ $banner->is_active ? 0 : 1 }}">
                                </form>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary" href="{{ route('admin.banner.edit', $banner->id) }}" title="{{ translate('messages.edit') }}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" 
                                       data-id="banner-{{ $banner->id }}" 
                                       data-form-id="banner-{{ $banner->id }}"
                                       data-message="Want to delete this banner?" 
                                       data-title="Delete Banner"
                                       data-image-on="{{ asset('assets/admin/img/modal/delete-icon.png') }}"
                                       title="{{ translate('messages.delete') }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{ route('admin.banner.destroy', $banner->id) }}"
                                            method="post" id="banner-{{ $banner->id }}_form">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $banners->links() }}
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    $(document).ready(function () {
        // Show loader when page starts loading
        if (typeof PageLoader !== 'undefined') {
            PageLoader.show();
        }
        
        // Initialize DataTable directly to ensure controls are shown
        var table = $('#columnSearchDatatable').DataTable({
            paging: false, // Disable DataTable pagination since we're using Laravel pagination
            searching: true,
            info: true,
            order: [],
            orderCellsTop: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="Image Description"><h5>{{ translate('no_data_found') }}</h5></div>',
                lengthMenu: "Show _MENU_ entries",
                search: "Search:",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
            },
            initComplete: function() {
                // Hide loader when DataTable is initialized
                if (typeof PageLoader !== 'undefined') {
                    setTimeout(function() {
                        PageLoader.hide();
                    }, 300);
                }
            }
        });
        
        // Update count badge when table is drawn
        table.on('draw', function () {
            var info = table.page.info();
            $('#banner-count').text(info.recordsTotal);
        });
        
        // Ensure controls are visible after initialization
        setTimeout(function() {
            $('.dataTables_length').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_filter').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_info').css({'display': 'block', 'visibility': 'visible'});
        }, 200);
        
        // Handle delete confirmation with form-alert using project modal
        $(document).on('click', '.form-alert', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var baseId = $btn.data('form-id') || $btn.data('id');
            var message = $btn.data('message') || 'Want to delete this?';
            var title = $btn.data('title') || 'Delete Confirmation';
            var imageOn = $btn.data('image-on') || '{{ asset('assets/admin/img/modal/delete-icon.png') }}';
            
            // Set modal content
            $('#toggle-status-title').text(title);
            $('#toggle-status-message').text(message);
            $('#toggle-status-image').attr('src', imageOn);
            $('#toggle-status-ok-button').attr('toggle-ok-button', baseId);
            
            // Show modal
            $('#toggle-status-modal').modal('show');
        });
        
        // Hide loader if page is fully loaded (fallback)
        $(window).on('load', function() {
            setTimeout(function() {
                if (typeof PageLoader !== 'undefined') {
                    PageLoader.hide();
                }
            }, 500);
        });
    });
</script>
@endpush

