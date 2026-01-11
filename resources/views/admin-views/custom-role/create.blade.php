@extends('layouts.admin.app')
@section('title',translate('messages.custom_role'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/role.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('messages.employee_Role')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.users.custom-role.create')}}" method="post">
                        @csrf
                        @if ($language && count($language) > 0)
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link lang_link active"
                                href="#"
                                id="default-link">{{translate('messages.default')}}</a>
                            </li>
                            @foreach ($language as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link"
                                        href="#"
                                        id="{{ $lang }}-link">{{ strtoupper($lang) }}</a>
                                </li>
                            @endforeach
                        </ul>
                            <div class="form-group lang_form" id="default-form">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.role_name')}} ({{ translate('messages.default') }}) <span class="form-label-secondary text-danger"
                                    data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('messages.Required.')}}"> *
                                    </span>
                                </label>
                                <input type="text" name="name[]" class="form-control" placeholder="{{translate('role_name_example')}}" maxlength="191">
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                                @foreach($language as $lang)
                                    <div class="form-group d-none lang_form" id="{{$lang}}-form">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.role_name')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{translate('role_name_example')}}" maxlength="191">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.role_name')}} <span class="form-label-secondary text-danger"> *</span></label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{translate('role_name_example')}}" value="{{old('name.0')}}" maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif

                        <div class="d-flex flex-wrap select--all-checkes">
                            <h5 class="input-label m-0 text-capitalize">{{translate('messages.Set_permission')}} : </h5>
                            <div class="check-item pb-0 w-auto">
                                <div class="form-group form-check form--check m-0 ml-2">
                                    <input type="checkbox" class="form-check-input" id="select-all">
                                    <label class="form-check-label ml-2" for="select-all">{{ translate('select_all') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="check--item-wrapper">
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="logistics" class="form-check-input"
                                           id="logistics">
                                    <label class="form-check-label qcont text-dark" for="logistics">{{translate('messages.logistics')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="banner" class="form-check-input"
                                           id="banner">
                                    <label class="form-check-label qcont text-dark" for="banner">{{translate('messages.banner')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="category" class="form-check-input"
                                           id="category">
                                    <label class="form-check-label qcont text-dark" for="category">{{translate('messages.category')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="product" class="form-check-input"
                                           id="product">
                                    <label class="form-check-label qcont text-dark" for="product">{{translate('messages.product')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="order" class="form-check-input"
                                           id="order">
                                    <label class="form-check-label qcont text-dark" for="order">{{translate('messages.order')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="customer" class="form-check-input"
                                           id="customer">
                                    <label class="form-check-label qcont text-dark" for="customer">{{translate('messages.customer')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="vendor" class="form-check-input"
                                           id="vendor">
                                    <label class="form-check-label qcont text-dark" for="vendor">{{translate('messages.vendor')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="salesmen" class="form-check-input"
                                           id="salesmen">
                                    <label class="form-check-label qcont text-dark" for="salesmen">{{translate('messages.salesmen')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="custom_role" class="form-check-input"
                                           id="custom_role">
                                    <label class="form-check-label qcont text-dark" for="custom_role">{{translate('messages.custom_role')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-4">
                            <button type="reset" id="reset-btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-0 py-2">
                    <div class="search--button-wrapper">
                        <h5 class="card-title">
                            {{translate('messages.roles_table')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$roles->total()}}</span>
                        </h5>
                        <form class="search-form min--200">
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search"  value="{{request()?->search}}" class="form-control" placeholder="{{translate('ex_:_search_role_name')}}" aria-label="Search">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>
                        @if(request()->get('search'))
                        <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="role--table table table-borderless table-thead-bordered table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" class="border-0">{{translate('sl')}}</th>
                                <th scope="col" class="border-0">{{translate('messages.role_name')}}</th>
                                <th scope="col" class="border-0">{{translate('messages.Permissions')}}</th>
                                <th scope="col" class="border-0">{{translate('messages.created_at')}}</th>
                                <th scope="col" class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody  id="set-rows">
                            @foreach($roles as $k=>$role)
                                <tr>
                                    <td scope="row">{{$roles->firstItem() ? ($k+$roles->firstItem()) : ($k+1)}}</td>
                                    <td title="{{ $role->name }}" >{{Str::limit($role->name,25,'...')}}</td>
                                    <td class="text-capitalize">
                                        @if($role->permissions && $role->permissions->count() > 0)
                                            @foreach($role->permissions as $permission)
                                                @php
                                                    // Extract module name from permission (e.g., 'access-logistics' -> 'logistics')
                                                    $moduleName = str_replace('access-', '', $permission->name);
                                                @endphp
                                                {{translate(str_replace('_',' ',$moduleName))}}
                                                {{  !$loop->last ? ',' : '.'}}
                                            @endforeach
                                        @else
                                            <span class="text-muted">No permissions</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="create-date">
                                            @if(isset($role->created_at))
                                                @if($role->created_at instanceof \Carbon\Carbon)
                                                    {{$role->created_at->format('d-M-Y')}}
                                                @else
                                                    {{date('d-M-Y', strtotime($role->created_at))}}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary"
                                                href="{{route('admin.users.custom-role.edit',[$role->id])}}" title="{{translate('messages.edit_role')}}"><i class="tio-edit"></i>
                                            </a>
                                            @if($role->name !== 'super-admin')
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="role-{{$role->id}}" data-message="{{translate('messages.Want_to_delete_this_role')}}"
                                               title="{{translate('messages.delete_role')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            @endif
                                        </div>
                                        @if($role->name !== 'super-admin')
                                        <form action="{{route('admin.users.custom-role.delete',[$role->id])}}"
                                                method="post" id="role-{{$role->id}}">
                                            @csrf @method('delete')
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($roles) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $roles->links() !!}
                    </div>
                    @if(count($roles) === 0)
                    <div class="empty--data">
                        <img src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src="{{asset('assets/admin')}}/js/view-pages/custom-role-index.js"></script>
@endpush

