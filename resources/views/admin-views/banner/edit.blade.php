@extends('layouts.admin.app')

@section('title', translate('messages.edit_banner'))

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
                    <i class="tio-edit"></i>
                </span>
                <span>
                    {{ translate('messages.edit_banner') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.edit_banner') }}</h5>
                    </div>
                    <form action="{{ route('admin.banner.update', $banner->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="title">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter banner title" value="{{ old('title', $banner->title) }}" required maxlength="255">
                                        @error('title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="position">Position <span class="text-danger">*</span></label>
                                        <select name="position" id="position" class="form-control" required>
                                            <option value="">Select Position</option>
                                            <option value="home_top" {{ old('position', $banner->position) == 'home_top' ? 'selected' : '' }}>Home Top</option>
                                            <option value="home_middle" {{ old('position', $banner->position) == 'home_middle' ? 'selected' : '' }}>Home Middle</option>
                                            <option value="home_bottom" {{ old('position', $banner->position) == 'home_bottom' ? 'selected' : '' }}>Home Bottom</option>
                                            <option value="dashboard" {{ old('position', $banner->position) == 'dashboard' ? 'selected' : '' }}>Dashboard</option>
                                        </select>
                                        @error('position')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter banner description">{{ old('description', $banner->description) }}</textarea>
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="image">Banner Image</label>
                                        <input type="file" name="image" id="image" class="form-control" accept="image/*" onchange="readURL(this, 'image-preview')">
                                        <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP (Max: 2MB). Leave empty to keep current image.</small>
                                        <div class="image-preview-container" id="image-preview-container">
                                            @if($banner->image_url)
                                                <img id="image-preview" src="{{ \App\CentralLogics\Helpers::get_full_url('banner', $banner->image_url, \App\CentralLogics\Helpers::getDisk()) }}" alt="Current Image">
                                            @else
                                                <img id="image-preview" src="" alt="Preview" style="display: none;">
                                            @endif
                                        </div>
                                        @error('image')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="redirect_type">Redirect Type <span class="text-danger">*</span></label>
                                        <select name="redirect_type" id="redirect_type" class="form-control" required onchange="toggleRedirectFields()">
                                            <option value="">Select Redirect Type</option>
                                            <option value="none" {{ old('redirect_type', $banner->redirect_type) == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="product" {{ old('redirect_type', $banner->redirect_type) == 'product' ? 'selected' : '' }}>Product</option>
                                            <option value="category" {{ old('redirect_type', $banner->redirect_type) == 'category' ? 'selected' : '' }}>Category</option>
                                            <option value="external" {{ old('redirect_type', $banner->redirect_type) == 'external' ? 'selected' : '' }}>External URL</option>
                                        </select>
                                        @error('redirect_type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6" id="redirect_id_field" style="display: none;">
                                    <div class="form-group">
                                        <label class="input-label" for="redirect_id">Redirect ID <span class="text-danger">*</span></label>
                                        <input type="number" name="redirect_id" id="redirect_id" class="form-control" placeholder="Enter product/category ID" value="{{ old('redirect_id', $banner->redirect_id) }}">
                                        @error('redirect_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6" id="redirect_url_field" style="display: none;">
                                    <div class="form-group">
                                        <label class="input-label" for="redirect_url">Redirect URL <span class="text-danger">*</span></label>
                                        <input type="url" name="redirect_url" id="redirect_url" class="form-control" placeholder="https://example.com" value="{{ old('redirect_url', $banner->redirect_url) }}" maxlength="500">
                                        @error('redirect_url')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="start_date">Start Date</label>
                                        <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}">
                                        @error('start_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="end_date">End Date</label>
                                        <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}">
                                        @error('end_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="sort_order">Sort Order</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control" placeholder="0" value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                                        @error('sort_order')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">Active Status</label>
                                        <div class="custom--switch">
                                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} switch="bool">
                                            <label for="is_active" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn--container justify-content-end">
                                <a href="{{ route('admin.banner.index') }}" class="btn btn--reset">{{ translate('messages.cancel') }}</a>
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
                $('#image-preview-container').show();
                $('#image-preview').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleRedirectFields() {
        var redirectType = $('#redirect_type').val();
        if (redirectType === 'product' || redirectType === 'category') {
            $('#redirect_id_field').show();
            $('#redirect_id').prop('required', true);
            $('#redirect_url_field').hide();
            $('#redirect_url').prop('required', false);
        } else if (redirectType === 'external') {
            $('#redirect_url_field').show();
            $('#redirect_url').prop('required', true);
            $('#redirect_id_field').hide();
            $('#redirect_id').prop('required', false);
        } else {
            $('#redirect_id_field').hide();
            $('#redirect_id').prop('required', false);
            $('#redirect_url_field').hide();
            $('#redirect_url').prop('required', false);
        }
    }

    $(document).ready(function() {
        // Initialize redirect fields on page load
        toggleRedirectFields();
    });
</script>
@endpush

