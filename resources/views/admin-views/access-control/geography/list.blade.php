@extends('layouts.admin.app')
@section('title',translate('Geography Management'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h1 class="page-header-title mb-3 mr-1">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/role.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.Geography_Management')}}</span>
            </h1>
            <a href="{{route('admin.access-control.geography.create')}}" class="btn btn--primary mb-3">
                <i class="tio-add-circle"></i>
                <span class="text">{{translate('messages.add_new')}}</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-2 border-0">
                    <div class="search--button-wrapper">
                        <h5 class="card-title">{{translate('messages.Geography_list')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$geographies->total()}}</span></h5>
                        <form class="search-form min--200" action="{{route('admin.access-control.geography.index')}}" method="GET">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" value="{{ request()->get('search') }}" class="form-control" placeholder="{{translate('messages.ex_:_search_name')}}" aria-label="Search">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>
                        @if(request()->get('search'))
                        <a href="{{route('admin.access-control.geography.index')}}" class="btn btn--primary ml-2">{{translate('messages.reset')}}</a>
                        @endif
                    </div>
                    <div class="mt-2">
                        <select class="form-control w-auto d-inline-block" name="level_filter" id="level_filter" onchange="window.location.href='{{route('admin.access-control.geography.index')}}?level='+this.value">
                            <option value="">{{translate('messages.all_levels')}}</option>
                            <option value="india" {{request()->get('level') == 'india' ? 'selected' : ''}}>India</option>
                            <option value="state" {{request()->get('level') == 'state' ? 'selected' : ''}}>State</option>
                            <option value="zone" {{request()->get('level') == 'zone' ? 'selected' : ''}}>Zone</option>
                            <option value="area" {{request()->get('level') == 'area' ? 'selected' : ''}}>Area</option>
                            <option value="pincode" {{request()->get('level') == 'pincode' ? 'selected' : ''}}>Pincode</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.level')}}</th>
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.code')}}</th>
                                <th class="border-0">{{translate('messages.parent')}}</th>
                                <th class="border-0">{{translate('messages.path')}}</th>
                                <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($geographies as $k=>$geography)
                                <tr>
                                    <th scope="row">{{$k+$geographies->firstItem()}}</th>
                                    <td><span class="badge badge-soft-{{$geography->level == 'india' ? 'danger' : ($geography->level == 'state' ? 'warning' : ($geography->level == 'zone' ? 'info' : ($geography->level == 'area' ? 'primary' : 'secondary')))}}">{{ucfirst($geography->level)}}</span></td>
                                    <td class="text-capitalize">{{$geography->name}}</td>
                                    <td><span class="badge badge-soft-secondary">{{$geography->code ?? '-'}}</span></td>
                                    <td>{{$geography->parent->name ?? '-'}}</td>
                                    <td><small class="text-muted">{{$geography->path ?? '-'}}</small></td>
                                    <td class="text-center">
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input status-switch" 
                                                data-id="{{$geography->id}}" 
                                                {{$geography->is_active ? 'checked' : ''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" 
                                                href="{{route('admin.access-control.geography.edit',[$geography->id])}}" 
                                                title="{{translate('messages.edit')}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" 
                                                href="javascript:" 
                                                data-id="geography-{{$geography->id}}" 
                                                data-message="{{translate('messages.Want_to_delete_this_geography')}}" 
                                                title="{{translate('messages.delete')}}">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.access-control.geography.destroy',[$geography->id])}}" method="post" id="geography-{{$geography->id}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($geographies) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $geographies->links() !!}
                </div>
                @if(count($geographies) === 0)
                <div class="empty--data">
                    <img src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>{{translate('messages.no_data_available')}}</h5>
                    <p>{{translate('messages.there_is_no_geography')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    $(document).on('change', '.status-switch', function() {
        let id = $(this).data('id');
        let status = $(this).prop('checked') ? 1 : 0;
        let baseUrl = "{{ url('/admin/access-control/geography/status') }}";
        let url = baseUrl + '/' + id;
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                status: status
            },
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message || '{{translate('messages.failed_to_update_status')}}');
                    location.reload();
                }
            },
            error: function(xhr) {
                toastr.error('{{translate('messages.failed_to_update_status')}}');
                location.reload();
            }
        });
    });
</script>
@endpush

