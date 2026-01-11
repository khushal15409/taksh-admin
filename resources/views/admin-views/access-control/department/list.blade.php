@extends('layouts.admin.app')
@section('title',translate('Department Management'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h1 class="page-header-title mb-3 mr-1">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/role.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.Department_Management')}}</span>
            </h1>
            <a href="{{route('admin.access-control.department.create')}}" class="btn btn--primary mb-3">
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
                        <h5 class="card-title">{{translate('messages.Department_list')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$departments->total()}}</span></h5>
                        <form class="search-form min--200" action="{{route('admin.access-control.department.index')}}" method="GET">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" value="{{ request()->get('search') }}" class="form-control" placeholder="{{translate('messages.ex_:_search_name')}}" aria-label="Search">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>
                        @if(request()->get('search'))
                        <a href="{{route('admin.access-control.department.index')}}" class="btn btn--primary ml-2">{{translate('messages.reset')}}</a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.code')}}</th>
                                <th class="border-0">{{translate('messages.description')}}</th>
                                <th class="border-0">{{translate('messages.units')}}</th>
                                <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($departments as $k=>$department)
                                <tr>
                                    <th scope="row">{{$k+$departments->firstItem()}}</th>
                                    <td class="text-capitalize">{{$department->name}}</td>
                                    <td><span class="badge badge-soft-info">{{$department->code}}</span></td>
                                    <td>{{ Str::limit($department->description ?? '-', 50) }}</td>
                                    <td><span class="badge badge-soft-primary">{{$department->units->count()}}</span></td>
                                    <td class="text-center">
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input status-switch" 
                                                data-id="{{$department->id}}" 
                                                {{$department->is_active ? 'checked' : ''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" 
                                                href="{{route('admin.access-control.department.edit',[$department->id])}}" 
                                                title="{{translate('messages.edit')}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" 
                                                href="javascript:" 
                                                data-id="department-{{$department->id}}" 
                                                data-message="{{translate('messages.Want_to_delete_this_department')}}" 
                                                title="{{translate('messages.delete')}}">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.access-control.department.destroy',[$department->id])}}" method="post" id="department-{{$department->id}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($departments) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $departments->links() !!}
                </div>
                @if(count($departments) === 0)
                <div class="empty--data">
                    <img src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('messages.no_data_available')}}
                    </h5>
                    <p>
                        {{translate('messages.there_is_no_department')}}
                    </p>
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
        let baseUrl = "{{ url('/admin/access-control/department/status') }}";
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

