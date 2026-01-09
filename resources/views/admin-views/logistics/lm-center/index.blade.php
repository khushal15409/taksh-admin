@extends('layouts.admin.app')

@section('title',translate('LM Center Creation'))

@include('admin-views.partials._loader')

@push('css_or_js')
<link rel="stylesheet" href="{{ asset('assets/admin/css/logistics/lm-center.css') }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-map"></i>
                </span>
                <span>
                    LM Center Creation
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
                        LM Center List<span class="badge badge-soft-dark ml-2" id="lm-center-count">{{count($lmCenters)}}</span>
                    </h5>
                    <div>
                        <a href="{{route('admin.logistics.lm-center.create')}}" class="btn btn--primary m-0 pull-right">
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
                        <th class="border-0">Center Name</th>
                        <th class="border-0">Full Address</th>
                        <th class="border-0">Location</th>
                        <th class="border-0">Pincode</th>
                        <th class="border-0">Owner Name</th>
                        <th class="border-0">Owner ID</th>
                        <th class="border-0">Document</th>
                        <th class="border-0">{{translate('messages.zone')}}</th>
                        <th class="border-0 text-center">{{translate('messages.status')}}</th>
                        <th class="border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($lmCenters as $key=>$lmCenter)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$lmCenter->center_name}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($lmCenter->full_address, 30, '...')}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$lmCenter->location ?? 'N/A'}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$lmCenter->pincode}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$lmCenter->owner_name}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$lmCenter->owner_id ?? 'N/A'}}
                                </span>
                            </td>
                            <td>
                                @if($lmCenter->document)
                                    <a href="{{asset('storage/app/public/lm-center/documents/' . $lmCenter->document)}}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download Document">
                                        <i class="tio-download"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$lmCenter->zone->name ?? 'N/A'}}
                                </span>
                            </td>
                            <td class="text-center">
                                <label class="toggle-switch toggle-switch-sm" for="status-{{$lmCenter['id']}}">
                                    <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                           data-id="status-{{$lmCenter['id']}}"
                                           data-type="status"
                                           data-image-on='{{asset('/public/assets/admin/img/modal/zone-status-on.png')}}'
                                           data-image-off="{{asset('/public/assets/admin/img/modal/zone-status-off.png')}}"
                                           data-title-on="<?php echo e(translate('Want to activate this LM center?')); ?>"
                                           data-title-off="<?php echo e(translate('Want to deactivate this LM center?')); ?>"
                                           id="status-{{$lmCenter['id']}}" {{$lmCenter->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <form action="{{route('admin.logistics.lm-center.status')}}" method="post" id="status-{{$lmCenter['id']}}_form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$lmCenter['id']}}">
                                    <input type="hidden" name="status" value="{{$lmCenter->status?0:1}}" id="status-value-{{$lmCenter['id']}}">
                                </form>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.logistics.lm-center.edit',[$lmCenter['id']])}}" title="{{translate('messages.edit')}}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="lm-center-{{$lmCenter['id']}}" data-message="<?php echo e(translate('Want to delete this LM center?')); ?>" title="{{translate('messages.delete')}}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.logistics.lm-center.destroy',[$lmCenter['id']])}}"
                                            method="post" id="lm-center-{{$lmCenter['id']}}">
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
            $('#lm-center-count').text(info.recordsTotal);
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
            var message = $btn.data('message') || 'Are you sure you want to delete this LM center?';
            
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
        
        // Handle dynamic checkbox status update for LM Center
        // Update status value when checkbox state changes (before modal confirmation)
        $(document).on('change', '.dynamic-checkbox[data-id^="status-"]', function() {
            var checkbox = $(this);
            var checkboxId = checkbox.attr('data-id');
            var lmCenterId = checkboxId.replace('status-', '');
            
            // Update the status value in the form based on NEW checkbox state
            // When checkbox is checked, status should be 1, when unchecked, status should be 0
            var statusValue = checkbox.is(':checked') ? 1 : 0;
            $('#status-value-' + lmCenterId).val(statusValue);
        });
        
        // Also handle the confirm button click to ensure status is updated correctly
        $(document).on('click', '.confirm-Status-Toggle', function() {
            // Wait a moment for common.js to toggle the checkbox
            setTimeout(function() {
                $('.dynamic-checkbox[data-id^="status-"]').each(function() {
                    var checkbox = $(this);
                    var checkboxId = checkbox.attr('data-id');
                    var lmCenterId = checkboxId.replace('status-', '');
                    var statusValue = checkbox.is(':checked') ? 1 : 0;
                    $('#status-value-' + lmCenterId).val(statusValue);
                });
            }, 100);
        });
    });
</script>
@endpush

