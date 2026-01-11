@extends('layouts.admin.app')
@section('title',translate('User Assignment Management'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h1 class="page-header-title mb-3 mr-1">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/role.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.User_Assignment_Management')}}</span>
            </h1>
            <a href="{{route('admin.access-control.user-assignment.create')}}" class="btn btn--primary mb-3">
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
                        <h5 class="card-title">{{translate('messages.User_Assignments')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$assignments->total()}}</span></h5>
                        <form class="search-form min--200" action="{{route('admin.access-control.user-assignment.index')}}" method="GET">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" value="{{ request()->get('search') }}" class="form-control" placeholder="{{translate('messages.ex_:_search_name')}}" aria-label="Search">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>
                        @if(request()->get('search'))
                        <a href="{{route('admin.access-control.user-assignment.index')}}" class="btn btn--primary ml-2">{{translate('messages.reset')}}</a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.user')}}</th>
                                <th class="border-0">{{translate('messages.department')}}</th>
                                <th class="border-0">{{translate('messages.unit')}}</th>
                                <th class="border-0">{{translate('messages.geography')}}</th>
                                <th class="border-0">{{translate('messages.role')}}</th>
                                <th class="border-0">{{translate('messages.effective_period')}}</th>
                                <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($assignments as $k=>$assignment)
                                <tr>
                                    <th scope="row">{{$k+$assignments->firstItem()}}</th>
                                    <td>{{$assignment->user->full_name ?? ($assignment->user->name ?? '-')}}<br><small class="text-muted">{{$assignment->user->email ?? '-'}}</small></td>
                                    <td><span class="badge badge-soft-info">{{$assignment->department->code ?? '-'}}</span><br>{{$assignment->department->name ?? '-'}}</td>
                                    <td>{{$assignment->departmentUnit->name ?? '<span class="text-muted">All Units</span>'}}</td>
                                    <td>{{$assignment->geography ? $assignment->geography->name . ' (' . ucfirst($assignment->geography->level) . ')' : '<span class="text-muted">All Geographies</span>'}}</td>
                                    <td><span class="badge badge-soft-primary">{{$assignment->role->name ?? '-'}}</span></td>
                                    <td>
                                        <small>
                                            {{$assignment->effective_from ? $assignment->effective_from->format('d/m/Y') : 'Immediate'}}<br>
                                            {{$assignment->effective_to ? 'To: ' . $assignment->effective_to->format('d/m/Y') : 'Indefinite'}}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input" 
                                                {{$assignment->is_active && $assignment->isCurrentlyEffective() ? 'checked' : ''}} disabled>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" 
                                                href="{{route('admin.access-control.user-assignment.edit',[$assignment->id])}}" 
                                                title="{{translate('messages.edit')}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" 
                                                href="javascript:" 
                                                data-id="assignment-{{$assignment->id}}" 
                                                data-message="{{translate('messages.Want_to_delete_this_assignment')}}" 
                                                title="{{translate('messages.delete')}}">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.access-control.user-assignment.destroy',[$assignment->id])}}" method="post" id="assignment-{{$assignment->id}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($assignments) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $assignments->links() !!}
                </div>
                @if(count($assignments) === 0)
                <div class="empty--data">
                    <img src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>{{translate('messages.no_data_available')}}</h5>
                    <p>{{translate('messages.there_is_no_assignment')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

