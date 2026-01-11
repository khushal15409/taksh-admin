@extends('layouts.admin.app')
@section('title',translate('Add User Assignment'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/role.png')}}" class="w--26" alt="">
            </span>
            <span>{{translate('messages.add_new_user_assignment')}}</span>
        </h1>
    </div>
    <form action="{{route('admin.access-control.user-assignment.store')}}" method="post">
        @csrf
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon"><i class="tio-user-add"></i></span>
                    <span>{{translate('messages.user_assignment_information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="input-label" for="user_id">{{translate('messages.user')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <select class="form-control js-select2-custom w-100" name="user_id" id="user_id" required>
                            <option value="" selected disabled>{{translate('messages.select_user')}}</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" {{(old('user_id') ?? ($selectedUserId ?? '')) == $user->id ? 'selected' : ''}}>
                                    {{$user->full_name ?? $user->name}} ({{$user->email}})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="department_id">{{translate('messages.department')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <select class="form-control js-select2-custom w-100" name="department_id" id="department_id" required>
                            <option value="" selected disabled>{{translate('messages.select_department')}}</option>
                            @foreach($departments as $department)
                                <option value="{{$department->id}}" {{old('department_id') == $department->id ? 'selected' : ''}}>
                                    {{$department->name}} ({{$department->code}})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="department_unit_id">{{translate('messages.department_unit')}}</label>
                        <select class="form-control js-select2-custom w-100" name="department_unit_id" id="department_unit_id">
                            <option value="">{{translate('messages.all_units')}}</option>
                        </select>
                        <small class="text-muted">{{translate('messages.leave_empty_for_all_units')}}</small>
                        @error('department_unit_id')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="geography_id">{{translate('messages.geography')}}</label>
                        <select class="form-control js-select2-custom w-100" name="geography_id" id="geography_id">
                            <option value="">{{translate('messages.all_geographies')}}</option>
                            @foreach($geographies as $geography)
                                <option value="{{$geography->id}}" {{old('geography_id') == $geography->id ? 'selected' : ''}}>
                                    {{$geography->name}} ({{ucfirst($geography->level)}})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{translate('messages.leave_empty_for_all_geographies')}}</small>
                        @error('geography_id')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="role_id">{{translate('messages.role')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <select class="form-control js-select2-custom w-100" name="role_id" id="role_id" required>
                            <option value="" selected disabled>{{translate('messages.select_role')}}</option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}" {{old('role_id') == $role->id ? 'selected' : ''}}>
                                    {{$role->name}}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="input-label" for="effective_from">{{translate('messages.effective_from')}}</label>
                        <input type="date" name="effective_from" id="effective_from" class="form-control" value="{{old('effective_from')}}">
                        <small class="text-muted">{{translate('messages.leave_empty_for_immediate')}}</small>
                    </div>
                    <div class="col-md-3">
                        <label class="input-label" for="effective_to">{{translate('messages.effective_to')}}</label>
                        <input type="date" name="effective_to" id="effective_to" class="form-control" value="{{old('effective_to')}}">
                        <small class="text-muted">{{translate('messages.leave_empty_for_indefinite')}}</small>
                    </div>
                    <div class="col-md-12">
                        <label class="input-label" for="notes">{{translate('messages.notes')}}</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="{{translate('messages.optional_notes')}}">{{old('notes')}}</textarea>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form--check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{old('is_active', true) ? 'checked' : ''}}>
                                <label class="form-check-label" for="is_active">{{translate('messages.active')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn--container justify-content-end">
            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
            <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
        </div>
    </form>
</div>
@endsection

@push('script_2')
<script>
    $(document).ready(function() {
        // Load units when department is selected
        $('#department_id').on('change', function() {
            let departmentId = $(this).val();
            let unitSelect = $('#department_unit_id');
            
            unitSelect.html('<option value="">{{translate('messages.loading')}}...</option>');
            
            if (departmentId) {
                $.ajax({
                    url: "{{route('admin.access-control.user-assignment.get-units-by-department')}}",
                    method: 'GET',
                    data: { department_id: departmentId },
                    success: function(response) {
                        unitSelect.html('<option value="">{{translate('messages.all_units')}}</option>');
                        if (response.units && response.units.length > 0) {
                            $.each(response.units, function(index, unit) {
                                unitSelect.append('<option value="' + unit.id + '">' + unit.name + ' (' + unit.code + ')</option>');
                            });
                        }
                    },
                    error: function() {
                        unitSelect.html('<option value="">{{translate('messages.error_loading_units')}}</option>');
                    }
                });
            } else {
                unitSelect.html('<option value="">{{translate('messages.all_units')}}</option>');
            }
        });
    });
</script>
@endpush

