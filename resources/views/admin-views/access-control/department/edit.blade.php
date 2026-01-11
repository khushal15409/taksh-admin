@extends('layouts.admin.app')
@section('title',translate('Edit Department'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/edit.png')}}" class="w--26" alt="">
            </span>
            <span>{{translate('messages.edit_department')}}</span>
        </h1>
    </div>
    <form action="{{route('admin.access-control.department.update',[$department->id])}}" method="post">
        @csrf
        @method('PUT')
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon"><i class="tio-building"></i></span>
                    <span>{{translate('messages.department_information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="input-label" for="name">{{translate('messages.name')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{old('name', $department->name)}}" required>
                        @error('name')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="code">{{translate('messages.code')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <input type="text" name="code" id="code" class="form-control" value="{{old('code', $department->code)}}" maxlength="20" required>
                        <small class="text-muted">{{translate('messages.short_code_for_department')}}</small>
                        @error('code')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="input-label" for="description">{{translate('messages.description')}}</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{old('description', $department->description)}}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="sort_order">{{translate('messages.sort_order')}}</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{old('sort_order', $department->sort_order)}}" min="0">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check form--check mt-4">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{old('is_active', $department->is_active) ? 'checked' : ''}}>
                                <label class="form-check-label" for="is_active">{{translate('messages.active')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn--container justify-content-end">
            <a href="{{route('admin.access-control.department.index')}}" class="btn btn--reset">{{translate('messages.cancel')}}</a>
            <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
        </div>
    </form>
</div>
@endsection

