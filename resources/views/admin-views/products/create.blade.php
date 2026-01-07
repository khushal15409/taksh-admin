@extends('layouts.admin.app')

@section('title', translate('messages.add_product'))

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
                    <i class="tio-add-circle"></i>
                </span>
                <span>
                    {{ translate('messages.add_product') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('messages.add_product') }}</h5>
                    </div>
                    <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data" id="product-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Info -->
                                <div class="col-md-12">
                                    <h5 class="mb-3">Basic Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="name">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" value="{{ old('name') }}" required maxlength="255">
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
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="short_description">Short Description</label>
                                        <textarea name="short_description" id="short_description" class="form-control" rows="2" placeholder="Enter short description (max 500 characters)" maxlength="500">{{ old('short_description') }}</textarea>
                                        @error('short_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter product description">{{ old('description') }}</textarea>
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
                                                <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
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
                                        <select name="city_id" id="city_id" class="form-control" required disabled>
                                            <option value="">{{ translate('messages.select_city') }}</option>
                                        </select>
                                        @error('city_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label" for="fulfillment_center_id">Fulfillment Center <span class="text-danger">*</span></label>
                                        <select name="fulfillment_center_id" id="fulfillment_center_id" class="form-control" required disabled>
                                            <option value="">{{ translate('messages.select_fulfillment_center') }}</option>
                                        </select>
                                        @error('fulfillment_center_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label" for="stock_qty">{{ translate('messages.stock_quantity') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="stock_qty" id="stock_qty" class="form-control" placeholder="0" value="{{ old('stock_qty', 0) }}" min="0" required>
                                        @error('stock_qty')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Product Attributes & Variants -->
                                <div class="col-md-12 mt-3">
                                    <h5 class="mb-3">{{ translate('messages.product_attributes') }} & {{ translate('messages.product_variants') }}</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label">{{ translate('messages.select_attributes') }}</label>
                                        <div class="row" id="attributes-container">
                                            @foreach($attributes as $attribute)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input attribute-checkbox" type="checkbox" 
                                                                   name="selected_attributes[]" 
                                                                   value="{{ $attribute->id }}" 
                                                                   id="attr_{{ $attribute->id }}"
                                                                   data-attribute-id="{{ $attribute->id }}">
                                                            <label class="form-check-label font-weight-bold" for="attr_{{ $attribute->id }}">
                                                                {{ $attribute->name }}
                                                            </label>
                                                        </div>
                                                        <div class="attribute-values mt-2" id="attr_values_{{ $attribute->id }}" style="display: none;">
                                                            @foreach($attribute->values as $value)
                                                                <div class="form-check">
                                                                    <input class="form-check-input attribute-value-checkbox" 
                                                                           type="checkbox" 
                                                                           name="attribute_values[{{ $attribute->id }}][]" 
                                                                           value="{{ $value->id }}" 
                                                                           id="attr_val_{{ $value->id }}"
                                                                           data-attribute-id="{{ $attribute->id }}">
                                                                    <label class="form-check-label" for="attr_val_{{ $value->id }}">
                                                                        {{ $value->value }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" id="generate-variants-btn" style="display: none;">
                                            {{ translate('messages.generate_variants') }}
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Generated Variants -->
                                <div class="col-md-12 mt-3" id="variants-section" style="display: none;">
                                    <h5 class="mb-3">{{ translate('messages.product_variants') }}</h5>
                                    <div id="variants-container"></div>
                                </div>

                                <!-- Pricing (Default Variant - Keep for backward compatibility) -->
                                <div class="col-md-12 mt-3">
                                    <h5 class="mb-3">Default Variant (Optional - if no attributes selected)</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="variant_sku">SKU</label>
                                        <input type="text" name="variant_sku" id="variant_sku" class="form-control" placeholder="Enter SKU" value="{{ old('variant_sku') }}" maxlength="255">
                                        @error('variant_sku')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="variant_price">Price</label>
                                        <input type="number" name="variant_price" id="variant_price" class="form-control" placeholder="0.00" value="{{ old('variant_price') }}" step="0.01" min="0">
                                        @error('variant_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="variant_sale_price">Sale Price</label>
                                        <input type="number" name="variant_sale_price" id="variant_sale_price" class="form-control" placeholder="0.00" value="{{ old('variant_sale_price') }}" step="0.01" min="0">
                                        @error('variant_sale_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="variant_status">Variant Status</label>
                                        <select name="variant_status" id="variant_status" class="form-control">
                                            <option value="active" {{ old('variant_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('variant_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('variant_status')
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
                                        <label class="input-label" for="images">Images</label>
                                        <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple onchange="previewImages(this)">
                                        <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP (Max: 15MB each). You can select multiple images.</small>
                                        <div class="image-preview-container" id="image-preview-container"></div>
                                        @error('images.*')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
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
                                            <input type="checkbox" name="is_trending" id="is_trending" value="1" {{ old('is_trending') ? 'checked' : '' }} switch="bool">
                                            <label for="is_trending" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                        <small class="text-muted">Controls trending_products in dashboard API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">Latest</label>
                                        <div class="custom--switch">
                                            <input type="checkbox" name="is_latest" id="is_latest" value="1" {{ old('is_latest') ? 'checked' : '' }} switch="bool">
                                            <label for="is_latest" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                        <small class="text-muted">Controls latest_products in dashboard API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">30 mins Delivery</label>
                                        <div class="custom--switch">
                                            <input type="checkbox" name="is_express_30" id="is_express_30" value="1" {{ old('is_express_30') ? 'checked' : '' }} switch="bool">
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
    var imageFiles = [];
    var maxFileSize = 15 * 1024 * 1024; // 15MB in bytes
    
    // Variant Generation Logic
    var selectedAttributes = {};
    var generatedVariants = [];
    
    // Show/hide attribute values when attribute is selected
    $(document).on('change', '.attribute-checkbox', function() {
        var attributeId = $(this).data('attribute-id');
        var valuesContainer = $('#attr_values_' + attributeId);
        
        if ($(this).is(':checked')) {
            valuesContainer.show();
            selectedAttributes[attributeId] = [];
        } else {
            valuesContainer.hide();
            delete selectedAttributes[attributeId];
            // Uncheck all values
            valuesContainer.find('.attribute-value-checkbox').prop('checked', false);
            updateGenerateButton();
        }
    });
    
    // Track selected attribute values
    $(document).on('change', '.attribute-value-checkbox', function() {
        var attributeId = $(this).data('attribute-id');
        var valueId = $(this).val();
        
        if (!selectedAttributes[attributeId]) {
            selectedAttributes[attributeId] = [];
        }
        
        if ($(this).is(':checked')) {
            if (selectedAttributes[attributeId].indexOf(valueId) === -1) {
                selectedAttributes[attributeId].push(valueId);
            }
        } else {
            selectedAttributes[attributeId] = selectedAttributes[attributeId].filter(id => id != valueId);
        }
        
        updateGenerateButton();
    });
    
    function updateGenerateButton() {
        var hasSelectedAttributes = Object.keys(selectedAttributes).length > 0;
        var hasSelectedValues = Object.values(selectedAttributes).some(values => values.length > 0);
        
        if (hasSelectedAttributes && hasSelectedValues) {
            $('#generate-variants-btn').show();
        } else {
            $('#generate-variants-btn').hide();
            $('#variants-section').hide();
            generatedVariants = [];
        }
    }
    
    // Generate variants from attribute combinations
    $('#generate-variants-btn').on('click', function() {
        generateVariants();
    });
    
    function generateVariants() {
        // Get all attribute-value combinations
        var attributeData = {};
        $('.attribute-checkbox:checked').each(function() {
            var attrId = $(this).data('attribute-id');
            var attrName = $(this).closest('.card').find('label').text().trim();
            var values = [];
            
            $('#attr_values_' + attrId + ' .attribute-value-checkbox:checked').each(function() {
                var valueId = $(this).val();
                var valueText = $(this).closest('.form-check').find('label').text().trim();
                values.push({id: valueId, text: valueText});
            });
            
            if (values.length > 0) {
                attributeData[attrId] = {name: attrName, values: values};
            }
        });
        
        if (Object.keys(attributeData).length === 0) {
            alert('{{ translate('messages.select_at_least_one_attribute_value') }}');
            return;
        }
        
        // Generate all combinations
        var combinations = generateCombinations(attributeData);
        
        // Clear existing variants
        generatedVariants = [];
        $('#variants-container').empty();
        $('#variants-section').show();
        
        // Create variant rows
        combinations.forEach(function(combo, index) {
            var variantId = 'variant_' + index;
            var variantName = combo.map(c => c.value).join(' / ');
            var variantData = {
                id: variantId,
                attributes: combo,
                name: variantName
            };
            generatedVariants.push(variantData);
            
            var variantHtml = createVariantRow(variantId, variantName, combo, index);
            $('#variants-container').append(variantHtml);
        });
    }
    
    function generateCombinations(attributeData) {
        var attributeIds = Object.keys(attributeData);
        var combinations = [];
        
        function combine(index, currentCombo) {
            if (index === attributeIds.length) {
                combinations.push([...currentCombo]);
                return;
            }
            
            var attrId = attributeIds[index];
            var values = attributeData[attrId].values;
            
            values.forEach(function(value) {
                currentCombo.push({
                    attributeId: attrId,
                    attributeName: attributeData[attrId].name,
                    valueId: value.id,
                    value: value.text
                });
                combine(index + 1, currentCombo);
                currentCombo.pop();
            });
        }
        
        combine(0, []);
        return combinations;
    }
    
    function createVariantRow(variantId, variantName, attributes, index) {
        var attributesHtml = attributes.map(function(attr) {
            return '<input type="hidden" name="variants[' + index + '][attributes][' + attr.attributeId + ']" value="' + attr.valueId + '">';
        }).join('');
        
        return `
            <div class="card mb-3 variant-row" data-variant-id="${variantId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">${variantName}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-variant" data-variant-id="${variantId}">
                            <i class="tio-delete-outlined"></i> Remove
                        </button>
                    </div>
                    ${attributesHtml}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="variants[${index}][sku]" class="form-control variant-sku" required maxlength="255" placeholder="Enter SKU">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="input-label">Price <span class="text-danger">*</span></label>
                                <input type="number" name="variants[${index}][price]" class="form-control variant-price" required step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="input-label">Sale Price</label>
                                <input type="number" name="variants[${index}][sale_price]" class="form-control variant-sale-price" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="input-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="variants[${index}][stock_qty]" class="form-control variant-stock" required min="0" placeholder="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="input-label">Status <span class="text-danger">*</span></label>
                                <select name="variants[${index}][status]" class="form-control variant-status" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Remove variant
    $(document).on('click', '.remove-variant', function() {
        var variantId = $(this).data('variant-id');
        $(this).closest('.variant-row').remove();
        generatedVariants = generatedVariants.filter(v => v.id !== variantId);
        
        // Re-index variants
        $('#variants-container .variant-row').each(function(index) {
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    name = name.replace(/variants\[\d+\]/, 'variants[' + index + ']');
                    $(this).attr('name', name);
                }
            });
        });
        
        if ($('#variants-container .variant-row').length === 0) {
            $('#variants-section').hide();
        }
    });
    
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
        
        // Restore old values if form was submitted with errors
        @if(old('state_id'))
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
        container.innerHTML = '';
        imageFiles = [];
        
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
                imageFiles.push(file);
                
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

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('product-form');
        if (!form) return;

        // Helper function to show error message
        function showError(fieldId, message) {
            var field = document.getElementById(fieldId);
            if (!field) return;

            // Remove existing error message
            var existingError = field.parentElement.querySelector('.validation-error');
            if (existingError) {
                existingError.remove();
            }

            // Add error class to field
            field.classList.add('is-invalid');

            // Create and show error message
            var errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error text-danger mt-1';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.textContent = message;
            field.parentElement.appendChild(errorDiv);
        }

        // Helper function to clear error
        function clearError(fieldId) {
            var field = document.getElementById(fieldId);
            if (!field) return;

            field.classList.remove('is-invalid');
            var existingError = field.parentElement.querySelector('.validation-error');
            if (existingError) {
                existingError.remove();
            }
        }

        // Real-time validation on blur
        var requiredFields = ['name', 'category_id', 'status', 'state_id', 'city_id', 'fulfillment_center_id', 'stock_qty'];
        requiredFields.forEach(function(fieldId) {
            var field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', function() {
                    validateField(fieldId);
                });
                field.addEventListener('input', function() {
                    if (field.classList.contains('is-invalid')) {
                        validateField(fieldId);
                    }
                });
            }
        });

        // Validate individual field
        function validateField(fieldId) {
            var field = document.getElementById(fieldId);
            if (!field) return true;

            var value = field.value.trim();
            var isValid = true;
            var errorMessage = '';

            switch(fieldId) {
                case 'name':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Product name is required';
                    } else if (value.length > 255) {
                        isValid = false;
                        errorMessage = 'Product name must not exceed 255 characters';
                    }
                    break;
                case 'category_id':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Category is required';
                    }
                    break;
                case 'status':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Status is required';
                    }
                    break;
                case 'variant_sku':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'SKU is required';
                    } else if (value.length > 255) {
                        isValid = false;
                        errorMessage = 'SKU must not exceed 255 characters';
                    }
                    break;
                case 'variant_price':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Price is required';
                    } else {
                        var price = parseFloat(value);
                        if (isNaN(price)) {
                            isValid = false;
                            errorMessage = 'Price must be a valid number';
                        } else if (price < 0) {
                            isValid = false;
                            errorMessage = 'Price must be greater than or equal to 0';
                        }
                    }
                    break;
                case 'variant_status':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Variant status is required';
                    }
                    break;
                case 'state_id':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'State is required';
                    }
                    break;
                case 'city_id':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'City is required';
                    }
                    break;
                case 'fulfillment_center_id':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Fulfillment center is required';
                    }
                    break;
                case 'stock_qty':
                    if (value === '' || value === null) {
                        isValid = false;
                        errorMessage = 'Stock quantity is required';
                    } else {
                        var stockQty = parseInt(value);
                        if (isNaN(stockQty)) {
                            isValid = false;
                            errorMessage = 'Stock quantity must be a number';
                        } else if (stockQty < 0) {
                            isValid = false;
                            errorMessage = 'Stock quantity must be greater than or equal to 0';
                        }
                    }
                    break;
            }

            if (isValid) {
                clearError(fieldId);
            } else {
                showError(fieldId, errorMessage);
            }

            return isValid;
        }

        // Validate sale price
        var salePriceField = document.getElementById('variant_sale_price');
        if (salePriceField) {
            salePriceField.addEventListener('blur', function() {
                var salePrice = salePriceField.value.trim();
                if (salePrice) {
                    var salePriceNum = parseFloat(salePrice);
                    var priceField = document.getElementById('variant_price');
                    var price = priceField ? parseFloat(priceField.value) : 0;

                    if (isNaN(salePriceNum)) {
                        showError('variant_sale_price', 'Sale price must be a valid number');
                    } else if (salePriceNum < 0) {
                        showError('variant_sale_price', 'Sale price must be greater than or equal to 0');
                    } else if (price > 0 && salePriceNum > price) {
                        showError('variant_sale_price', 'Sale price must be less than or equal to regular price');
                    } else {
                        clearError('variant_sale_price');
                    }
                } else {
                    clearError('variant_sale_price');
                }
            });
        }

        // Validate short description length
        var shortDescField = document.getElementById('short_description');
        if (shortDescField) {
            shortDescField.addEventListener('input', function() {
                if (this.value.length > 500) {
                    showError('short_description', 'Short description must not exceed 500 characters');
                } else {
                    clearError('short_description');
                }
            });
        }

        // Validate images
        var imagesField = document.getElementById('images');
        if (imagesField) {
            imagesField.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    var hasInvalidFile = false;

                    for (var i = 0; i < this.files.length; i++) {
                        var file = this.files[i];
                        if (!allowedTypes.includes(file.type)) {
                            hasInvalidFile = true;
                            showError('images', 'Only JPG, PNG, GIF, and WEBP image formats are allowed');
                            break;
                        }
                    }

                    if (!hasInvalidFile) {
                        clearError('images');
                    }
                }
            });
        }

        // Form submit validation
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var isValid = true;
            var firstErrorField = null;

            // Validate all required fields
            requiredFields.forEach(function(fieldId) {
                if (!validateField(fieldId)) {
                    isValid = false;
                    if (!firstErrorField) {
                        firstErrorField = document.getElementById(fieldId);
                    }
                }
            });
            
            // Validate variants if generated
            if ($('#variants-section').is(':visible') && $('#variants-container .variant-row').length > 0) {
                var variantSkus = [];
                $('#variants-container .variant-row').each(function() {
                    var skuInput = $(this).find('.variant-sku');
                    var priceInput = $(this).find('.variant-price');
                    var stockInput = $(this).find('.variant-stock');
                    
                    var sku = skuInput.val().trim();
                    var price = parseFloat(priceInput.val());
                    var stock = parseInt(stockInput.val());
                    
                    if (!sku) {
                        isValid = false;
                        if (!firstErrorField) {
                            firstErrorField = skuInput[0];
                        }
                        showError(skuInput.attr('id') || 'variant-sku', 'Variant SKU is required');
                    } else if (variantSkus.indexOf(sku) !== -1) {
                        isValid = false;
                        if (!firstErrorField) {
                            firstErrorField = skuInput[0];
                        }
                        showError(skuInput.attr('id') || 'variant-sku', 'Duplicate SKU: ' + sku);
                    } else {
                        variantSkus.push(sku);
                    }
                    
                    if (!price || isNaN(price) || price < 0) {
                        isValid = false;
                        if (!firstErrorField) {
                            firstErrorField = priceInput[0];
                        }
                        showError(priceInput.attr('id') || 'variant-price', 'Variant price must be a valid number >= 0');
                    }
                    
                    if (stock === '' || isNaN(stock) || stock < 0) {
                        isValid = false;
                        if (!firstErrorField) {
                            firstErrorField = stockInput[0];
                        }
                        showError(stockInput.attr('id') || 'variant-stock', 'Variant stock must be a valid number >= 0');
                    }
                });
            } else if (!$('#variant_sku').val() || !$('#variant_price').val()) {
                // Validate default variant if no generated variants
                if (!$('#variant_sku').val()) {
                    isValid = false;
                    if (!firstErrorField) {
                        firstErrorField = document.getElementById('variant_sku');
                    }
                    showError('variant_sku', 'SKU is required');
                }
                if (!$('#variant_price').val()) {
                    isValid = false;
                    if (!firstErrorField) {
                        firstErrorField = document.getElementById('variant_price');
                    }
                    showError('variant_price', 'Price is required');
                }
            }

            // Validate sale price if provided
            if (salePriceField && salePriceField.value.trim()) {
                var salePrice = parseFloat(salePriceField.value);
                var price = parseFloat(document.getElementById('variant_price').value);
                
                if (isNaN(salePrice) || salePrice < 0 || (price > 0 && salePrice > price)) {
                    isValid = false;
                    if (!firstErrorField) {
                        firstErrorField = salePriceField;
                    }
                    if (isNaN(salePrice) || salePrice < 0) {
                        showError('variant_sale_price', 'Sale price must be a valid number greater than or equal to 0');
                    } else {
                        showError('variant_sale_price', 'Sale price must be less than or equal to regular price');
                    }
                }
            }

            // Validate short description length
            if (shortDescField && shortDescField.value.length > 500) {
                isValid = false;
                if (!firstErrorField) {
                    firstErrorField = shortDescField;
                }
                showError('short_description', 'Short description must not exceed 500 characters');
            }

            // Validate images if provided
            if (imagesField && imagesField.files && imagesField.files.length > 0) {
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                for (var i = 0; i < imagesField.files.length; i++) {
                    var file = imagesField.files[i];
                    if (!allowedTypes.includes(file.type)) {
                        isValid = false;
                        if (!firstErrorField) {
                            firstErrorField = imagesField;
                        }
                        showError('images', 'Only JPG, PNG, GIF, and WEBP image formats are allowed');
                        break;
                    }
                    if (file.size > maxFileSize) {
                        isValid = false;
                        if (!firstErrorField) {
                            firstErrorField = imagesField;
                        }
                        showError('images', 'File "' + file.name + '" exceeds the maximum file size of 15MB');
                        break;
                    }
                }
            }

            if (!isValid) {
                // Scroll to first error field
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorField.focus();
                }
                return false;
            }

            // If validation passes, submit the form
            form.submit();
        });
    });
</script>
@endpush

