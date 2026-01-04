@extends('layouts.admin.app')

@section('title', translate('messages.add_category'))

@push('css_or_js')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075), 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.5rem;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 1rem 1.25rem;
    }
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid rgba(0, 0, 0, 0.125);
        padding: 1rem 1.25rem;
    }
    .image-preview-container {
        margin-top: 15px;
        display: none;
    }
    .image-preview-container img {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        border: 2px solid #e4e6ef;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-add-circle"></i>
                </span>
                <span>
                    {{ translate('messages.add_category') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.add_category') }}</h5>
                    </div>
                    <form action="{{ route('admin.categories.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="name">Category Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" value="{{ old('name') }}" required maxlength="255">
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="parent_id">Parent Category</label>
                                        <select name="parent_id" id="parent_id" class="form-control">
                                            <option value="">None (Main Category)</option>
                                            @foreach($parentCategories as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="image">Category Image</label>
                                        <input type="file" name="image" id="image" class="form-control" accept="image/*" onchange="readURL(this, 'image-preview')">
                                        <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP (Max: 2MB)</small>
                                        <div class="image-preview-container" id="image-preview-container">
                                            <img id="image-preview" src="" alt="Preview">
                                        </div>
                                        @error('image')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="icon">Category Icon</label>
                                        <input type="file" name="icon" id="icon" class="form-control" accept="image/*" onchange="readURL(this, 'icon-preview')">
                                        <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP (Max: 2MB)</small>
                                        <div class="image-preview-container" id="icon-preview-container">
                                            <img id="icon-preview" src="" alt="Preview">
                                        </div>
                                        @error('icon')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn--container justify-content-end">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn--reset">{{ translate('messages.cancel') }}</a>
                                <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    function readURL(input, viewer) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + viewer).attr('src', e.target.result);
                $('#' + viewer + '-container').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush

