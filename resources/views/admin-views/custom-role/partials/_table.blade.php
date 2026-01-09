@foreach($roles as $k=>$role)
    <tr>
        <td scope="row">{{$k+1}}</td>
        <td>{{$role->name}}</td>
        <td class="text-capitalize">
            @if($role->permissions && $role->permissions->count() > 0)
                @foreach($role->permissions as $permission)
                    @php
                        // Extract module name from permission (e.g., 'access-logistics' -> 'logistics')
                        $moduleName = str_replace('access-', '', $permission->name);
                    @endphp
                    {{translate(str_replace('_',' ',$moduleName))}}
                    {{  !$loop->last ? ',' : ''}}
                @endforeach
            @else
                <span class="text-muted">No permissions</span>
            @endif
        </td>
        <td>
            @if(isset($role->created_at))
                @if($role->created_at instanceof \Carbon\Carbon)
                    {{$role->created_at->format('d-M-y')}}
                @else
                    {{date('d-M-y',strtotime($role->created_at))}}
                @endif
            @else
                N/A
            @endif
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
