@extends('layouts.admin.app')
@section('title',translate('Add Department Unit'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/role.png')}}" class="w--26" alt="">
            </span>
            <span>{{translate('messages.add_new_department_unit')}}</span>
        </h1>
    </div>
    <form action="{{route('admin.access-control.department-unit.store')}}" method="post">
        @csrf
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon"><i class="tio-building"></i></span>
                    <span>{{translate('messages.department_unit_information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
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
                        <label class="input-label" for="code">{{translate('messages.code')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="{{translate('messages.Ex:')}} warehouse-mumbai" value="{{old('code')}}" maxlength="50" required>
                        <small class="text-muted">{{translate('messages.unique_code_within_department')}}</small>
                        @error('code')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="name">{{translate('messages.name')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="{{translate('messages.Ex:')}} Mumbai Warehouse" value="{{old('name')}}" required>
                        @error('name')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="sort_order">{{translate('messages.sort_order')}}</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{old('sort_order', 0)}}" min="0">
                    </div>
                    <div class="col-md-12">
                        <label class="input-label" for="description">{{translate('messages.description')}}</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="{{translate('messages.description_placeholder')}}">{{old('description')}}</textarea>
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

