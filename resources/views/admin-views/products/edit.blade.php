@extends('layouts.admin.app')

@section('title', translate('messages.edit_product'))

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
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .image-preview-item {
        position: relative;
        width: 120px;
        height: 120px;
    }
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e4e6ef;
    }
    .image-preview-item .remove-image {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
    }
    
    /* Validation styles */
    .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4L6 4.4l-.2-.4-.4-.4zm0 4.8L6 8.8l.2.4.4.4-.4-.4z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .validation-error {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
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
                    {{ translate('messages.edit_product') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.edit_product') }}</h5>
                    </div>
                    <form action="{{ route('admin.products.update', $product->id) }}" method="post" enctype="multipart/form-data" id="product-form">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Info -->
                                <div class="col-md-12">
                                    <h5 class="mb-3">Basic Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="name">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" value="{{ old('name', $product->name) }}" required maxlength="255">
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="category_id">Category <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="brand_id">Brand</label>
                                        <select name="brand_id" id="brand_id" class="form-control">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="short_description">Short Description</label>
                                        <textarea name="short_description" id="short_description" class="form-control" rows="2" placeholder="Enter short description (max 500 characters)" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                                        @error('short_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Location & Fulfillment Center -->
                                <div class="col-md-12 mt-3">
                                    <h5 class="mb-3">Location & Fulfillment Center</h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label" for="state_id">State <span class="text-danger">*</span></label>
                                        <select name="state_id" id="state_id" class="form-control" required>
                                            <option value="">{{ translate('messages.select_state') }}</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}" {{ old('state_id', $selectedStateId) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label" for="city_id">City <span class="text-danger">*</span></label>
                                        <select name="city_id" id="city_id" class="form-control" required {{ $selectedCityId ? '' : 'disabled' }}>
                                            <option value="">{{ translate('messages.select_city') }}</option>
                                            @if($selectedCityId)
                                                @php
                                                    $cities = \App\Models\City::where('state_id', $selectedStateId)->orderBy('name', 'asc')->get();
                                                @endphp
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id', $selectedCityId) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('city_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label" for="fulfillment_center_id">Fulfillment Center <span class="text-danger">*</span></label>
                                        <select name="fulfillment_center_id" id="fulfillment_center_id" class="form-control" required {{ $selectedFulfillmentCenterId ? '' : 'disabled' }}>
                                            <option value="">{{ translate('messages.select_fulfillment_center') }}</option>
                                            @if($selectedFulfillmentCenterId)
                                                @php
                                                    $fulfillmentCenters = \App\Models\FulfillmentCenter::where('city_id', $selectedCityId)->where('status', 'active')->orderBy('name', 'asc')->get();
                                                @endphp
                                                @foreach($fulfillmentCenters as $fc)
                                                    <option value="{{ $fc->id }}" {{ old('fulfillment_center_id', $selectedFulfillmentCenterId) == $fc->id ? 'selected' : '' }}>{{ $fc->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('fulfillment_center_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label" for="stock_qty">{{ translate('messages.stock_quantity') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="stock_qty" id="stock_qty" class="form-control" placeholder="0" value="{{ old('stock_qty', $stockQty) }}" min="0" required>
                                        @error('stock_qty')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="col-md-12 mt-3">
                                    <h5 class="mb-3">Product Images</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="images">Add New Images</label>
                                        <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple onchange="previewImages(this)">
                                        <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP (Max: 15MB each). You can select multiple images.</small>
                                        <div class="image-preview-container" id="image-preview-container"></div>
                                        @error('images.*')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label">Existing Images</label>
                                        <div class="image-preview-container">
                                            @foreach($product->images as $image)
                                                <div class="image-preview-item">
                                                    <img src="{{ \App\CentralLogics\Helpers::get_full_url('product', $image->image_url, \App\CentralLogics\Helpers::getDisk()) }}" alt="Product Image">
                                                    <a href="{{ route('admin.products.delete-image', $image->id) }}" 
                                                       class="remove-image" 
                                                       onclick="return confirm('Are you sure you want to delete this image?')"
                                                       title="Delete Image">
                                                        <i class="tio-delete-outlined"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Flags -->
                                <div class="col-md-12 mt-3">
                                    <h5 class="mb-3">Product Flags</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">Trending</label>
                                        <div class="custom--switch">
                                            <input type="checkbox" name="is_trending" id="is_trending" value="1" {{ old('is_trending', $product->is_trending) ? 'checked' : '' }} switch="bool">
                                            <label for="is_trending" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                        <small class="text-muted">Controls trending_products in dashboard API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">Latest</label>
                                        <div class="custom--switch">
                                            <input type="checkbox" name="is_latest" id="is_latest" value="1" {{ old('is_latest', $product->is_latest) ? 'checked' : '' }} switch="bool">
                                            <label for="is_latest" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                        <small class="text-muted">Controls latest_products in dashboard API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">Express-30</label>
                                        <div class="custom--switch">
                                            <input type="checkbox" name="is_express_30" id="is_express_30" value="1" {{ old('is_express_30', $product->is_express_30) ? 'checked' : '' }} switch="bool">
                                            <label for="is_express_30" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                        <small class="text-muted">Controls /api/express-30/products</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn--container justify-content-end">
                                <a href="{{ route('admin.products.index') }}" class="btn btn--reset">{{ translate('messages.cancel') }}</a>
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
    var maxFileSize = 15 * 1024 * 1024; // 15MB in bytes
    
    // Dependent dropdowns for State, City, Fulfillment Center
    $(document).ready(function() {
        // State change - load cities
        $('#state_id').on('change', function() {
            var stateId = $(this).val();
            var citySelect = $('#city_id');
            var fulfillmentCenterSelect = $('#fulfillment_center_id');
            
            // Reset dependent dropdowns
            citySelect.html('<option value="">{{ translate('messages.select_city') }}</option>').prop('disabled', true);
            fulfillmentCenterSelect.html('<option value="">{{ translate('messages.select_fulfillment_center') }}</option>').prop('disabled', true);
            
            if (stateId) {
                $.ajax({
                    url: '{{ route('admin.get-cities') }}',
                    type: 'GET',
                    data: { state_id: stateId },
                    success: function(response) {
                        if (response.cities && response.cities.length > 0) {
                            citySelect.prop('disabled', false);
                            response.cities.forEach(function(city) {
                                citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                            });
                            
                            // Restore old city value if exists
                            var oldCityId = '{{ old('city_id', $selectedCityId) }}';
                            if (oldCityId) {
                                citySelect.val(oldCityId).trigger('change');
                            }
                        } else {
                            citySelect.append('<option value="">{{ translate('messages.no_cities_found') }}</option>');
                        }
                    },
                    error: function() {
                        alert('{{ translate('messages.failed_to_load_cities') }}');
                    }
                });
            }
        });
        
        // City change - load fulfillment centers
        $('#city_id').on('change', function() {
            var cityId = $(this).val();
            var fulfillmentCenterSelect = $('#fulfillment_center_id');
            
            // Reset fulfillment center dropdown
            fulfillmentCenterSelect.html('<option value="">{{ translate('messages.select_fulfillment_center') }}</option>').prop('disabled', true);
            
            if (cityId) {
                $.ajax({
                    url: '{{ route('admin.get-fulfillment-centers') }}',
                    type: 'GET',
                    data: { city_id: cityId },
                    success: function(response) {
                        if (response.fulfillment_centers && response.fulfillment_centers.length > 0) {
                            fulfillmentCenterSelect.prop('disabled', false);
                            response.fulfillment_centers.forEach(function(fc) {
                                fulfillmentCenterSelect.append('<option value="' + fc.id + '">' + fc.name + '</option>');
                            });
                            
                            // Restore old fulfillment center value if exists
                            var oldFulfillmentCenterId = '{{ old('fulfillment_center_id', $selectedFulfillmentCenterId) }}';
                            if (oldFulfillmentCenterId) {
                                fulfillmentCenterSelect.val(oldFulfillmentCenterId);
                            }
                        } else {
                            fulfillmentCenterSelect.append('<option value="">{{ translate('messages.no_fulfillment_centers_found') }}</option>');
                        }
                    },
                    error: function() {
                        alert('{{ translate('messages.failed_to_load_fulfillment_centers') }}');
                    }
                });
            }
        });
        
        // Initialize dropdowns if state is already selected
        @if($selectedStateId)
            $('#state_id').trigger('change');
        @elseif(old('state_id'))
            var oldStateId = '{{ old('state_id') }}';
            var oldCityId = '{{ old('city_id') }}';
            var oldFulfillmentCenterId = '{{ old('fulfillment_center_id') }}';
            
            $('#state_id').val(oldStateId).trigger('change');
            setTimeout(function() {
                if (oldCityId) {
                    $('#city_id').val(oldCityId).trigger('change');
                    setTimeout(function() {
                        if (oldFulfillmentCenterId) {
                            $('#fulfillment_center_id').val(oldFulfillmentCenterId);
                        }
                    }, 500);
                }
            }, 500);
        @endif
    });
    
    function previewImages(input) {
        var container = document.getElementById('image-preview-container');
        
        if (input.files && input.files.length > 0) {
            var validFiles = [];
            var hasError = false;
            
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                
                // Check file size
                if (file.size > maxFileSize) {
                    alert('File "' + file.name + '" exceeds the maximum file size of 15MB. Please choose a smaller file.');
                    hasError = true;
                    continue;
                }
                
                validFiles.push(file);
                
                var reader = new FileReader();
                reader.onload = function(e) {
                    var div = document.createElement('div');
                    div.className = 'image-preview-item';
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
            
            // Reset input if there were invalid files
            if (hasError) {
                var dataTransfer = new DataTransfer();
                validFiles.forEach(function(file) {
                    dataTransfer.items.add(file);
                });
                input.files = dataTransfer.files;
            }
        }
    }
</script>
@endpush

