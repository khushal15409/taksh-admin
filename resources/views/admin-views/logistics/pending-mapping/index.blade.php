@extends('layouts.admin.app')

@section('title','Pending Mapping')

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
    
    /* Tab Styling - Scoped to Pending Mapping Module Only */
    #pendingMappingTabs {
        border-bottom: none;
        margin-bottom: 25px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    #pendingMappingTabs .nav-item {
        margin-bottom: 0;
    }
    
    #pendingMappingTabs .nav-link {
        border: 2px solid #e4e6ef;
        border-radius: 10px;
        color: #5e6278;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        white-space: nowrap;
        position: relative;
    }
    
    #pendingMappingTabs .nav-link:hover {
        border-color: #009ef7;
        color: #009ef7;
        background: #f1faff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 158, 247, 0.15);
    }
    
    #pendingMappingTabs .nav-link.active {
        color: #ffffff !important;
        background-color: #009ef7;
        border-color: #009ef7;
        box-shadow: 0 4px 8px rgba(0, 158, 247, 0.2);
        transform: translateY(-2px);
    }
    
    #pendingMappingTabs .nav-link.active:hover {
        background-color: #0085d1;
        border-color: #0085d1;
        color: #ffffff !important;
    }
    
    /* Badge Styling for Pending Mapping Tabs */
    #pendingMappingTabs .nav-link .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    
    #pendingMappingTabs .nav-link:not(.active) .badge {
        background: #e4e6ef;
        color: #5e6278;
    }
    
    #pendingMappingTabs .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    
    #pendingMappingTabs .nav-link.active span:not(.badge) {
        color: #ffffff !important;
    }
    
    #pendingMappingTabs .nav-link:hover:not(.active) .badge {
        background: #009ef7;
        color: #ffffff;
    }
    
    /* Prevent content flash on tab navigation */
    .tab-content {
        min-height: 400px;
        padding-top: 20px;
    }
    
    .tab-pane {
        opacity: 1;
        transition: opacity 0.2s ease-in-out;
    }
    
    /* Regular nav-tabs styling for other modules (if needed elsewhere) */
    .nav-tabs:not(#pendingMappingTabs) {
        border-bottom: 2px solid #e4e6ef;
        margin-bottom: 20px;
    }
    
    .nav-tabs:not(#pendingMappingTabs) .nav-item {
        margin-bottom: -2px;
    }
    
    .nav-tabs:not(#pendingMappingTabs) .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #5e6278;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .nav-tabs:not(#pendingMappingTabs) .nav-link:hover {
        border-bottom-color: #009ef7;
        color: #009ef7;
    }
    
    .nav-tabs:not(#pendingMappingTabs) .nav-link.active {
        color: #009ef7;
        border-bottom-color: #009ef7;
        background-color: transparent;
    }
    
    /* Delivery Type Filter Tabs Styling - Button Design */
    #deliveryTypeTabs {
        margin-top: 0;
        margin-bottom: 20px;
        border-bottom: none;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    #deliveryTypeTabs .nav-item {
        margin-bottom: 0;
    }
    
    #deliveryTypeTabs .nav-link {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 2px solid #e4e6ef;
        border-radius: 0.5rem;
        background-color: #ffffff;
        color: #5e6278;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-block;
        margin: 0;
        text-decoration: none;
    }
    
    #deliveryTypeTabs .nav-link:hover {
        border-color: #009ef7;
        color: #009ef7;
        background-color: #f1faff;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.1);
    }
    
    #deliveryTypeTabs .nav-link.active {
        background-color: #009ef7;
        border-color: #009ef7;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 158, 247, 0.2);
        transform: translateY(-2px);
    }
    
    #deliveryTypeTabs .nav-link.active:hover {
        background-color: #0085d1;
        border-color: #0085d1;
        color: #ffffff;
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
        
        #pendingMappingTabs {
            padding: 10px;
            gap: 8px;
        }
        
        #pendingMappingTabs .nav-link {
            padding: 10px 16px;
            font-size: 0.85rem;
            flex: 1;
            min-width: 140px;
            justify-content: center;
        }
        
        #pendingMappingTabs .nav-link .badge {
            font-size: 0.75rem;
            padding: 3px 8px;
        }
        
        .nav-tabs:not(#pendingMappingTabs) .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    }
    
    /* Pincode Status Switch Styling - Inactive (Red) */
    .toggle-switch-input:not(:checked) + .toggle-switch-label {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    
    /* Ensure active state remains green */
    .toggle-switch-input:checked + .toggle-switch-label {
        background-color: #14b19e !important;
        border-color: #14b19e !important;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-list-outlined"></i>
                </span>
                <span>
                    Pending Mapping
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <h5 class="card-title">Pending Mappings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-3" id="pendingMappingTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'warehouse' ? 'active' : '' }}" 
                                   id="warehouse-tab" 
                                   data-toggle="tab" 
                                   href="#warehouse" 
                                   role="tab"
                                   data-tab="warehouse"
                                   onclick="navigateToTab('warehouse'); return false;">
                                    Pending Warehouse
                                    <span class="badge badge-soft-dark ml-2">{{ count($pendingWarehouses) }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'miniwarehouse' ? 'active' : '' }}" 
                                   id="miniwarehouse-tab" 
                                   data-toggle="tab" 
                                   href="#miniwarehouse" 
                                   role="tab"
                                   data-tab="miniwarehouse"
                                   onclick="navigateToTab('miniwarehouse'); return false;">
                                    Pending Miniwarehouse
                                    <span class="badge badge-soft-dark ml-2">{{ count($pendingMiniwarehouses) }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'lm-center' ? 'active' : '' }}" 
                                   id="lm-center-tab" 
                                   data-toggle="tab" 
                                   href="#lm-center" 
                                   role="tab"
                                   data-tab="lm-center"
                                   onclick="navigateToTab('lm-center'); return false;">
                                    Pending LM Center
                                    <span class="badge badge-soft-dark ml-2">{{ count($pendingLmCenters) }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'fm-rt-center' ? 'active' : '' }}" 
                                   id="fm-rt-center-tab" 
                                   data-toggle="tab" 
                                   href="#fm-rt-center" 
                                   role="tab"
                                   data-tab="fm-rt-center"
                                   onclick="navigateToTab('fm-rt-center'); return false;">
                                    Pending FM/RT Center
                                    <span class="badge badge-soft-dark ml-2">{{ count($pendingFmRtCenters) }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'pincode' ? 'active' : '' }}" 
                                   id="pincode-tab" 
                                   data-toggle="tab" 
                                   href="#pincode" 
                                   role="tab"
                                   data-tab="pincode"
                                   onclick="navigateToTab('pincode'); return false;">
                                    Pending Pincode Pen India
                                    <span class="badge badge-soft-dark ml-2">{{ $pendingPincodesCount ?? 0 }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'live-ecommerce-pincode' ? 'active' : '' }}" 
                                   id="live-ecommerce-pincode-tab" 
                                   data-toggle="tab" 
                                   href="#live-ecommerce-pincode" 
                                   role="tab"
                                   data-tab="live-ecommerce-pincode"
                                   onclick="navigateToTab('live-ecommerce-pincode'); return false;">
                                    Live pincode Ecommerce
                                    <span class="badge badge-soft-dark ml-2">{{ $liveEcommercePincodesCount ?? 0 }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'pending-logistic-pincode' ? 'active' : '' }}" 
                                   id="pending-logistic-pincode-tab" 
                                   data-toggle="tab" 
                                   href="#pending-logistic-pincode" 
                                   role="tab"
                                   data-tab="pending-logistic-pincode"
                                   onclick="navigateToTab('pending-logistic-pincode'); return false;">
                                    Pending logistic Pincode
                                    <span class="badge badge-soft-dark ml-2">{{ $pendingLogisticPincodesCount ?? 0 }}</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab == 'active-pincode' ? 'active' : '' }}" 
                                   id="active-pincode-tab" 
                                   data-toggle="tab" 
                                   href="#active-pincode" 
                                   role="tab"
                                   data-tab="active-pincode"
                                   onclick="navigateToTab('active-pincode'); return false;">
                                    Logistic Live pincode
                                    <span class="badge badge-soft-dark ml-2">{{ $activePincodesCount ?? 0 }}</span>
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="pendingMappingTabContent">
                            <!-- Pending Warehouse Tab -->
                            <div class="tab-pane fade {{ $tab == 'warehouse' ? 'show active' : '' }}" id="warehouse" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="warehouseTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Image</th>
                                            <th class="border-0">{{translate('messages.name')}}</th>
                                            <th class="border-0">Owner Name</th>
                                            <th class="border-0">Full Address</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">{{translate('messages.zone')}}</th>
                                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pendingWarehouses as $key=>$warehouse)
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
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <img src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" 
                                                             alt="No Image" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                                    @endif
                                                </td>
                                                <td>{{$warehouse->name}}</td>
                                                <td>{{$warehouse->owner_name ?? 'N/A'}}</td>
                                                <td>{{Str::limit($warehouse->full_address, 30, '...')}}</td>
                                                <td>{{$warehouse->pincode}}</td>
                                                <td>{{$warehouse->zone->name ?? 'N/A'}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin.logistics.warehouse.edit',[$warehouse['id']])}}" class="btn btn-sm btn-white" title="Edit">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Pending Miniwarehouse Tab -->
                            <div class="tab-pane fade {{ $tab == 'miniwarehouse' ? 'show active' : '' }}" id="miniwarehouse" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="miniwarehouseTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Image</th>
                                            <th class="border-0">{{translate('messages.name')}}</th>
                                            <th class="border-0">Owner Name</th>
                                            <th class="border-0">Full Address</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">{{translate('messages.zone')}}</th>
                                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pendingMiniwarehouses as $key=>$miniwarehouse)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>
                                                    @php
                                                        $firstImage = null;
                                                        if ($miniwarehouse->images && is_array($miniwarehouse->images) && count($miniwarehouse->images) > 0) {
                                                            $firstImage = $miniwarehouse->images[0];
                                                        }
                                                    @endphp
                                                    @if($firstImage && isset($firstImage['img']))
                                                        <img src="{{ \App\CentralLogics\Helpers::get_full_url('miniwarehouse', $firstImage['img'], $firstImage['storage'] ?? 'public') }}" 
                                                             alt="{{ $miniwarehouse->name }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <img src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" 
                                                             alt="No Image" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                                    @endif
                                                </td>
                                                <td>{{$miniwarehouse->name}}</td>
                                                <td>{{$miniwarehouse->owner_name ?? 'N/A'}}</td>
                                                <td>{{Str::limit($miniwarehouse->full_address, 30, '...')}}</td>
                                                <td>{{$miniwarehouse->pincode}}</td>
                                                <td>{{$miniwarehouse->zone->name ?? 'N/A'}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin.logistics.miniwarehouse.edit',[$miniwarehouse['id']])}}" class="btn btn-sm btn-white" title="Edit">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Pending LM Center Tab -->
                            <div class="tab-pane fade {{ $tab == 'lm-center' ? 'show active' : '' }}" id="lm-center" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="lmCenterTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Center Name</th>
                                            <th class="border-0">Owner Name</th>
                                            <th class="border-0">Full Address</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">{{translate('messages.zone')}}</th>
                                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pendingLmCenters as $key=>$lmCenter)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$lmCenter->center_name}}</td>
                                                <td>{{$lmCenter->owner_name ?? 'N/A'}}</td>
                                                <td>{{Str::limit($lmCenter->full_address, 30, '...')}}</td>
                                                <td>{{$lmCenter->pincode}}</td>
                                                <td>{{$lmCenter->zone->name ?? 'N/A'}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin.logistics.lm-center.edit',[$lmCenter['id']])}}" class="btn btn-sm btn-white" title="Edit">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Pending FM/RT Center Tab -->
                            <div class="tab-pane fade {{ $tab == 'fm-rt-center' ? 'show active' : '' }}" id="fm-rt-center" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="fmRtCenterTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Image</th>
                                            <th class="border-0">Center Name</th>
                                            <th class="border-0">Owner Name</th>
                                            <th class="border-0">Full Address</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">{{translate('messages.zone')}}</th>
                                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pendingFmRtCenters as $key=>$fmRtCenter)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>
                                                    @php
                                                        $firstImage = null;
                                                        if ($fmRtCenter->images && is_array($fmRtCenter->images) && count($fmRtCenter->images) > 0) {
                                                            $firstImage = $fmRtCenter->images[0];
                                                        }
                                                    @endphp
                                                    @if($firstImage && isset($firstImage['img']))
                                                        <img src="{{ \App\CentralLogics\Helpers::get_full_url('fm_rt_center', $firstImage['img'], $firstImage['storage'] ?? 'public') }}" 
                                                             alt="{{ $fmRtCenter->center_name }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <img src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" 
                                                             alt="No Image" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                                    @endif
                                                </td>
                                                <td>{{$fmRtCenter->center_name}}</td>
                                                <td>{{$fmRtCenter->owner_name ?? 'N/A'}}</td>
                                                <td>{{Str::limit($fmRtCenter->full_address, 30, '...')}}</td>
                                                <td>{{$fmRtCenter->pincode}}</td>
                                                <td>{{$fmRtCenter->zone->name ?? 'N/A'}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin.logistics.fm-rt-center.edit',[$fmRtCenter['id']])}}" class="btn btn-sm btn-white" title="Edit">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Pending Pincode Pen India Tab -->
                            <div class="tab-pane fade {{ $tab == 'pincode' ? 'show active' : '' }}" id="pincode" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="pincodeTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">Office Name</th>
                                            <th class="border-0">District</th>
                                            <th class="border-0">State</th>
                                            <th class="border-0 text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Live pincode Ecommerce Tab -->
                            <div class="tab-pane fade {{ $tab == 'live-ecommerce-pincode' ? 'show active' : '' }}" id="live-ecommerce-pincode" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="liveEcommercePincodeTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">Office Name</th>
                                            <th class="border-0">District</th>
                                            <th class="border-0">State</th>
                                            <th class="border-0 text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Pending logistic Pincode Tab -->
                            <div class="tab-pane fade {{ $tab == 'pending-logistic-pincode' ? 'show active' : '' }}" id="pending-logistic-pincode" role="tabpanel">
                                <div class="table-responsive datatable-custom">
                                    <table id="pendingLogisticPincodeTable"
                                           class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{translate('messages.sl')}}</th>
                                            <th class="border-0">Pincode</th>
                                            <th class="border-0">Office Name</th>
                                            <th class="border-0">District</th>
                                            <th class="border-0">State</th>
                                            <th class="border-0 text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Logistic Live pincode Tab -->
                            <div class="tab-pane fade {{ $tab == 'active-pincode' ? 'show active' : '' }}" id="active-pincode" role="tabpanel">
                                <!-- Delivery Type Filter Tabs -->
                                <ul class="nav nav-tabs mb-3" id="deliveryTypeTabs" role="tablist" style="border-bottom: 1px solid #e4e6ef;">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" 
                                           id="delivery-both-tab" 
                                           data-toggle="tab" 
                                           href="#delivery-both" 
                                           role="tab"
                                           data-delivery-type="both"
                                           onclick="switchDeliveryType('both'); return false;">
                                            Taksh Logistic LM Live pincode
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" 
                                           id="delivery-thirty-min-tab" 
                                           data-toggle="tab" 
                                           href="#delivery-thirty-min" 
                                           role="tab"
                                           data-delivery-type="thirty_min"
                                           onclick="switchDeliveryType('thirty_min'); return false;">
                                            30 Min Delivery
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" 
                                           id="delivery-normal-tab" 
                                           data-toggle="tab" 
                                           href="#delivery-normal" 
                                           role="tab"
                                           data-delivery-type="normal"
                                           onclick="switchDeliveryType('normal'); return false;">
                                            Normal Delivery
                                        </a>
                                    </li>
                                </ul>
                                
                                <div class="tab-content" id="deliveryTypeTabContent">
                                    <div class="tab-pane fade show active" id="delivery-both" role="tabpanel">
                                        <div class="table-responsive datatable-custom">
                                            <table id="activePincodeTable"
                                                   class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th class="border-0">{{translate('messages.sl')}}</th>
                                                    <th class="border-0">Pincode</th>
                                                    <th class="border-0">Office Name</th>
                                                    <th class="border-0">District</th>
                                                    <th class="border-0">State</th>
                                                    <th class="border-0">Mapped LM Center</th>
                                                    <th class="border-0">Mapped Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    // Global variable to store current delivery type filter - DEFAULT: both (Taksh Logistic LM Live pincode)
    var currentDeliveryType = 'both';
    
    // Function to switch delivery type filter - COMPLETE STATE RESET (GLOBAL SCOPE)
    function switchDeliveryType(deliveryType) {
        // CRITICAL: Update filter state FIRST before any AJAX call
        currentDeliveryType = deliveryType;
        
        // Reset all tab active states - only one active at a time
        $('#deliveryTypeTabs .nav-link').removeClass('active');
        // Set only the clicked filter as active
        $('#deliveryTypeTabs .nav-link[data-delivery-type="' + deliveryType + '"]').addClass('active');
        
        // MANDATORY: Reload DataTable with new filter - ALWAYS execute backend query
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#activePincodeTable')) {
            var table = $('#activePincodeTable').DataTable();
            
            // Clear any existing search to prevent state leak
            table.search('').draw();
            
            // Reset to first page
            table.page('first').draw('page');
            
            // CRITICAL: Force complete AJAX reload
            // The data() function in ajax config will be called with updated currentDeliveryType
            // This ensures backend receives the correct filter parameter
            table.ajax.reload(function(json) {
                // Verify data changed after reload
                console.log('Filter changed to:', deliveryType);
                console.log('Records returned:', json.recordsFiltered);
            }, false); // false = don't reset paging (already reset above)
        } else {
            console.error('DataTable not initialized yet');
        }
    }
    
    // Function to navigate to tab without showing loader
    function navigateToTab(tabName) {
        // Prevent loader from showing
        if (typeof window !== 'undefined') {
            window._preventLoaderOnNavigation = true;
        }
        // Navigate to the tab
        window.location.href = '{{ route("admin.logistics.pending-mapping.index") }}?tab=' + tabName;
    }
    
    $(document).ready(function() {
        // Hide loader immediately on page load to prevent blink
        if (typeof PageLoader !== 'undefined') {
            PageLoader.hide();
        }
        
        // Initialize DataTables for each table with full configuration
        // Check if table exists and hasn't been initialized to prevent reinitialization errors
        if ($('#warehouseTable').length && !$.fn.DataTable.isDataTable('#warehouseTable')) {
            var warehouseTable = $('#warehouseTable').DataTable({
                paging: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                searching: true,
                info: true,
                order: [],
                orderCellsTop: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description"><h5>{{translate('no_data_found')}}</h5></div>',
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
                }
            });
        }
        
        if ($('#miniwarehouseTable').length && !$.fn.DataTable.isDataTable('#miniwarehouseTable')) {
            var miniwarehouseTable = $('#miniwarehouseTable').DataTable({
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
            }
            });
        }
        
        if ($('#lmCenterTable').length && !$.fn.DataTable.isDataTable('#lmCenterTable')) {
            var lmCenterTable = $('#lmCenterTable').DataTable({
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
            }
            });
        }
        
        if ($('#fmRtCenterTable').length && !$.fn.DataTable.isDataTable('#fmRtCenterTable')) {
            var fmRtCenterTable = $('#fmRtCenterTable').DataTable({
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
            }
            });
        }
        
        if ($('#pincodeTable').length && !$.fn.DataTable.isDataTable('#pincodeTable')) {
            var pincodeTable = $('#pincodeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.logistics.pending-mapping.pending-pincodes") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'pincode', name: 'pincode' },
                    { data: 'officename', name: 'officename' },
                    { data: 'district', name: 'district' },
                    { data: 'statename', name: 'statename' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                paging: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                searching: true,
                info: true,
                order: [[1, 'asc']],
                orderCellsTop: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description"><h5>{{translate('no_data_found')}}</h5></div>',
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    processing: "Loading pincodes...",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }
        
        // Intercept the confirm button click BEFORE common.js submits the form
        // This handler must run BEFORE common.js's handler to prevent form submission
        $(document).on('click', '.confirm-Status-Toggle', function(e) {
            var button = $(this);
            var checkboxId = button.attr('toggle-ok-button');
            
            // Only handle pincode status forms
            if (!checkboxId || (!checkboxId.startsWith('pincode-status-') && 
                                !checkboxId.startsWith('live-pincode-status-') && 
                                !checkboxId.startsWith('pending-logistic-pincode-status-'))) {
                // Let common.js handle other status toggles
                return true;
            }
            
            // Prevent default form submission
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Find the form
            var formId = checkboxId + '_form';
            var form = $('#' + formId);
            
            if (form.length === 0) {
                console.error('Form not found with ID:', formId);
                return false;
            }
            
            // Wait for common.js to finish toggling the checkbox
            setTimeout(function() {
                // Find checkbox to read its current state
                var checkbox = $('#' + checkboxId);
                var isChecked = false;
                
                if (checkbox.length > 0) {
                    isChecked = checkbox.is(':checked');
                } else {
                    // Fallback: try DOM directly
                    var domCheckbox = document.getElementById(checkboxId);
                    if (domCheckbox) {
                        isChecked = domCheckbox.checked;
                        checkbox = $(domCheckbox);
                    }
                }
                
                var pincodeId = form.find('input[name="id"]').val();
                var csrfToken = $('meta[name="csrf-token"]').attr('content') || form.find('input[name="_token"]').val();
                var newStatus = isChecked ? 1 : 0;
                
                // Debug log
                console.log('=== Pincode Status Update (AJAX Call) ===');
                console.log('Checkbox ID:', checkboxId);
                console.log('Form ID:', formId);
                console.log('Pincode ID:', pincodeId);
                console.log('Checkbox checked:', isChecked);
                console.log('Status to send:', newStatus);
                console.log('Form action:', form.attr('action'));
                
                // Verify required data
                if (!pincodeId) {
                    console.error('Pincode ID is missing!');
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Pincode ID is missing', 'Error');
                    }
                    return false;
                }
                
                if (!csrfToken) {
                    console.error('CSRF token is missing!');
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Security token is missing', 'Error');
                    }
                    return false;
                }
                
                // Prepare AJAX request
                var requestData = {
                    id: pincodeId,
                    status: newStatus,
                    _token: csrfToken
                };
                
                var url = form.attr('action');
                
                console.log('Making AJAX call to:', url);
                console.log('Request data:', requestData);
                
                // Show loading state on button
                button.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');
                
                // Make AJAX call
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: requestData,
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        console.log('=== AJAX Response ===');
                        console.log('Response:', response);
                        
                        // Restore button
                        button.prop('disabled', false).html('Ok');
                        
                        if (response && response.status === 'success') {
                            // Show success toast
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Status updated successfully', 'Success', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 3000,
                                    positionClass: 'toast-top-right'
                                });
                            }
                            
                            // Close modal
                            $('#toggle-status-modal').modal('hide');
                            
                            // Reload DataTables to show updated status
                            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#pincodeTable')) {
                                $('#pincodeTable').DataTable().ajax.reload(null, false);
                            }
                            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#liveEcommercePincodeTable')) {
                                $('#liveEcommercePincodeTable').DataTable().ajax.reload(null, false);
                            }
                            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#pendingLogisticPincodeTable')) {
                                $('#pendingLogisticPincodeTable').DataTable().ajax.reload(null, false);
                            }
                        } else {
                            // Revert checkbox on error
                            if (checkbox.length > 0) {
                                checkbox.prop('checked', !isChecked);
                            }
                            
                            var errorMsg = response && response.message ? response.message : 'Failed to update status';
                            console.error('Status update failed:', errorMsg);
                            
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMsg, 'Error', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 3000,
                                    positionClass: 'toast-top-right'
                                });
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('=== AJAX Error ===');
                        console.error('Status:', xhr.status);
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseJSON);
                        
                        // Restore button
                        button.prop('disabled', false).html('Ok');
                        
                        // Revert checkbox on error
                        if (checkbox.length > 0) {
                            checkbox.prop('checked', !isChecked);
                        }
                        
                        // Show error message
                        var errorMsg = 'Failed to update pincode status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 422) {
                            errorMsg = 'Validation error';
                        } else if (xhr.status === 404) {
                            errorMsg = 'Pincode not found';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error';
                        }
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMsg, 'Error', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-top-right'
                            });
                        }
                    }
                });
            }, 100); // Small delay to ensure common.js toggle is complete
            
            return false;
        });
        
        // Also handle form submission as fallback (in case click handler doesn't catch it)
        $(document).on('submit', 'form[id^="pincode-status-"], form[id^="live-pincode-status-"], form[id^="pending-logistic-pincode-status-"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });
        
        // Initialize liveEcommercePincodeTable only if it exists and hasn't been initialized
        if ($('#liveEcommercePincodeTable').length && !$.fn.DataTable.isDataTable('#liveEcommercePincodeTable')) {
            var liveEcommercePincodeTable = $('#liveEcommercePincodeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.logistics.pending-mapping.live-ecommerce-pincodes") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'pincode', name: 'pincode' },
                    { data: 'officename', name: 'officename' },
                    { data: 'district', name: 'district' },
                    { data: 'statename', name: 'statename' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                paging: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                searching: true,
                info: true,
                order: [[1, 'asc']],
                orderCellsTop: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description"><h5>{{translate('no_data_found')}}</h5></div>',
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    processing: "Loading pincodes...",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }
        
        // Initialize pendingLogisticPincodeTable only if it exists and hasn't been initialized
        if ($('#pendingLogisticPincodeTable').length && !$.fn.DataTable.isDataTable('#pendingLogisticPincodeTable')) {
            var pendingLogisticPincodeTable = $('#pendingLogisticPincodeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.logistics.pending-mapping.pending-logistic-pincodes") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'pincode', name: 'pincode' },
                    { data: 'officename', name: 'officename' },
                    { data: 'district', name: 'district' },
                    { data: 'statename', name: 'statename' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                paging: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                searching: true,
                info: true,
                order: [[1, 'asc']],
                orderCellsTop: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description"><h5>{{translate('no_data_found')}}</h5></div>',
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    processing: "Loading pincodes...",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }
        
        // Initialize activePincodeTable only if it exists and hasn't been initialized
        if ($('#activePincodeTable').length && !$.fn.DataTable.isDataTable('#activePincodeTable')) {
            var activePincodeTable = $('#activePincodeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.logistics.pending-mapping.active-pincodes") }}',
                    type: 'GET',
                    data: function(d) {
                        // MANDATORY: Always send current delivery type filter
                        // This ensures backend receives correct filter on every request
                        d.delivery_type = currentDeliveryType;
                        
                        // Cache busting to ensure fresh request
                        d._t = new Date().getTime();
                        
                        // Debug log
                        console.log('DataTable AJAX Request - Filter:', currentDeliveryType, 'Timestamp:', d._t);
                        
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX Error:', error, xhr);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error loading pincode data. Please try again.');
                        }
                    },
                    dataSrc: function(json) {
                        // Log response for debugging
                        console.log('DataTable Response - Records:', json.recordsFiltered, 'Filter:', currentDeliveryType);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'pincode', name: 'pincode' },
                    { data: 'officename', name: 'officename' },
                    { data: 'district', name: 'district' },
                    { data: 'statename', name: 'statename' },
                    { data: 'center_name', name: 'center_name', orderable: false },
                    { data: 'mapped_at', name: 'mapped_at', orderable: false }
                ],
                paging: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                searching: true,
                info: true,
                order: [[1, 'asc']],
                orderCellsTop: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    zeroRecords: '<div class="text-center p-4"><img class="w-7rem mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description"><h5>{{translate('no_data_found')}}</h5></div>',
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    processing: "Loading pincodes...",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }
        
        // Ensure controls are visible after initialization
        setTimeout(function() {
            $('.dataTables_length').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_filter').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_info').css({'display': 'block', 'visibility': 'visible'});
            $('.dataTables_paginate').css({'display': 'block', 'visibility': 'visible'});
        }, 200);
        
        // Handle tab switching - prevent loader from showing on tab navigation
        $('.nav-link[onclick*="pending-mapping"]').on('click', function(e) {
            // Prevent loader from showing on tab navigation
            if (typeof PageLoader !== 'undefined') {
                // Temporarily disable the beforeunload listener
                window._preventLoaderOnNavigation = true;
            }
        });
        
        // Handle tab switching - reinitialize DataTable for active tab (if using Bootstrap tabs without page reload)
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            var tableId = target.replace('#', '') + 'Table';
            
            // Redraw the table to ensure proper display
            if ($('#' + tableId).length) {
                $('#' + tableId).DataTable().columns.adjust().draw();
            }
        });
    });
</script>
@endpush

