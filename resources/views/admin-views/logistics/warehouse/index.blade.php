@extends('layouts.admin.app')

@section('title','Warehouse Creation')

@include('admin-views.partials._loader')

@push('css_or_js')
<link rel="stylesheet" href="{{ asset('assets/admin/css/logistics/warehouse.css') }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-shop-outlined"></i>
                </span>
                <span>
                    Warehouse Creation
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
                        Warehouse List<span class="badge badge-soft-dark ml-2" id="warehouse-count">{{count($warehouses)}}</span>
                    </h5>
                    <div>
                        <a href="{{route('admin.logistics.warehouse.create')}}" class="btn btn--primary m-0 pull-right">
                            <i class="tio-add-circle"></i> {{translate('messages.add_new')}}
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
                        <th class="border-0">{{translate('messages.sl')}}</th>
                        <th class="border-0">Image</th>
                        <th class="border-0">{{translate('messages.name')}}</th>
                        <th class="border-0">Owner Name</th>
                        <th class="border-0">Full Address</th>
                        <th class="border-0">Location</th>
                        <th class="border-0">Pincode</th>
                        <th class="border-0">{{translate('messages.zone')}}</th>
                        <th class="border-0 text-center">{{translate('messages.status')}}</th>
                        <th class="border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($warehouses as $key=>$warehouse)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                @php
                                    $firstImage = null;
                                    if ($warehouse->images && is_array($warehouse->images) && count($warehouse->images) > 0) {
                                        $firstImage = $warehouse->images[0];
                                    }
                                @endphp
                                @if($firstImage && isset($firstImage['img']))
                                    <img src="{{ \App\CentralLogics\Helpers::get_full_url('warehouse', $firstImage['img'], $firstImage['storage'] ?? 'public') }}" 
                                         alt="{{ $warehouse->name }}" 
                                         class="img-thumbnail warehouse-img-thumbnail">
                                @else
                                    <img src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" 
                                         alt="No Image" 
                                         class="img-thumbnail warehouse-img-thumbnail no-image">
                                @endif
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$warehouse->name}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$warehouse->owner_name ?? 'taksh'}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($warehouse->full_address, 30, '...')}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$warehouse->location ?? 'N/A'}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$warehouse->pincode}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$warehouse->zone->name ?? 'N/A'}}
                                </span>
                            </td>
                            <td class="text-center">
                                <label class="toggle-switch toggle-switch-sm" for="status-{{$warehouse['id']}}">
                                    <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                           data-id="status-{{$warehouse['id']}}"
                                           data-type="status"
                                           data-image-on='{{asset('/public/assets/admin/img/modal/zone-status-on.png')}}'
                                           data-image-off="{{asset('/public/assets/admin/img/modal/zone-status-off.png')}}"
                                           data-title-on="Want to activate this warehouse?"
                                           data-title-off="Want to deactivate this warehouse?"
                                           id="status-{{$warehouse['id']}}" {{$warehouse->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <form action="{{route('admin.logistics.warehouse.status')}}" method="post" id="status-{{$warehouse['id']}}_form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$warehouse['id']}}">
                                    <input type="hidden" name="status" value="{{$warehouse->status?0:1}}" id="status-value-{{$warehouse['id']}}">
                                </form>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.logistics.warehouse.edit',[$warehouse['id']])}}" title="{{translate('messages.edit')}}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="warehouse-{{$warehouse['id']}}" data-message="Want to delete this warehouse?" title="{{translate('messages.delete')}}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.logistics.warehouse.destroy',[$warehouse['id']])}}"
                                            method="post" id="warehouse-{{$warehouse['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
            paging: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            searching: true,
            info: true,
            order: [],
            orderCellsTop: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description"><h5>{{translate('no_data_found')}}</h5></div>',
                lengthMenu: "Show _MENU_ entries",
                search: "Search:",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
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
            $('#warehouse-count').text(info.recordsTotal);
        });
        
        // Ensure controls are visible after initialization
        setTimeout(function() {
            $('.dataTables_length').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_filter').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_info').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_paginate').css({'display': 'block', 'visibility': 'visible'});
        }, 200);
        
        // Handle delete confirmation with SweetAlert
        $(document).on('click', '.form-alert', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var formId = $btn.data('id');
            var message = $btn.data('message') || 'Are you sure you want to delete this warehouse?';
            
            if (!formId) {
                console.error('Form ID not found');
                return false;
            }
            
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof PageLoader !== 'undefined') {
                        PageLoader.show();
                    }
                    $('#' + formId).submit();
                }
            });
        });
        
        // Hide loader if page is fully loaded (fallback)
        $(window).on('load', function() {
            setTimeout(function() {
                if (typeof PageLoader !== 'undefined') {
                    PageLoader.hide();
                }
            }, 500);
        });
        
        // Handle dynamic checkbox status update for warehouse
        // Update status value when checkbox state changes (before modal confirmation)
        $(document).on('change', '.dynamic-checkbox[data-id^="status-"]', function() {
            var checkbox = $(this);
            var checkboxId = checkbox.attr('data-id');
            var warehouseId = checkboxId.replace('status-', '');
            
            // Update the status value in the form based on NEW checkbox state
            // When checkbox is checked, status should be 1, when unchecked, status should be 0
            var statusValue = checkbox.is(':checked') ? 1 : 0;
            $('#status-value-' + warehouseId).val(statusValue);
        });
        
        // Also handle the confirm button click to ensure status is updated correctly
        $(document).on('click', '.confirm-Status-Toggle', function() {
            // Wait a moment for common.js to toggle the checkbox
            setTimeout(function() {
                $('.dynamic-checkbox[data-id^="status-"]').each(function() {
                    var checkbox = $(this);
                    var checkboxId = checkbox.attr('data-id');
                    var warehouseId = checkboxId.replace('status-', '');
                    var statusValue = checkbox.is(':checked') ? 1 : 0;
                    $('#status-value-' + warehouseId).val(statusValue);
                });
            }, 100);
        });
    });
</script>
@endpush

