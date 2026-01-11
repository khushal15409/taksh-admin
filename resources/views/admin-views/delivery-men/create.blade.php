@extends('layouts.admin.app')

@section('title', translate('messages.add_delivery_man'))

@include('admin-views.partials._loader')

@push('css_or_js')
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">{{ translate('messages.add_delivery_man') }}</h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.delivery-men.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">Delivery Man Information</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form action="{{ route('admin.delivery-men.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile_number" class="form-control" 
                                           value="{{ old('mobile_number') }}" 
                                           pattern="[0-9]{10}" maxlength="10" required>
                                    <small class="form-text text-muted">10 digits only</small>
                                    @error('mobile_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                    @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fulfillment Center <span class="text-danger">*</span></label>
                                    <select name="fulfillment_center_id" class="form-control" required>
                                        <option value="">-- Select Fulfillment Center --</option>
                                        @foreach($fulfillmentCenters as $fc)
                                        <option value="{{ $fc->id }}" {{ old('fulfillment_center_id') == $fc->id ? 'selected' : '' }}>
                                            {{ $fc->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('fulfillment_center_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Enter complete address">{{ old('address') }}</textarea>
                                    @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input type="text" name="pincode" class="form-control" 
                                           value="{{ old('pincode') }}" 
                                           pattern="[0-9]{6}" maxlength="6" placeholder="Enter 6-digit pincode">
                                    @error('pincode')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="state_id" class="form-control" id="state_id">
                                        <option value="">-- Select State --</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="city_id" class="form-control" id="city_id">
                                        <option value="">-- Select City --</option>
                                        @if(old('city_id'))
                                        @foreach($cities->where('state_id', old('state_id')) as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('city_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Vehicle Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vehicle Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_type" class="form-control" required>
                                        <option value="">-- Select Vehicle Type --</option>
                                        <option value="bike" {{ old('vehicle_type') == 'bike' ? 'selected' : '' }}>Bike</option>
                                        <option value="cycle" {{ old('vehicle_type') == 'cycle' ? 'selected' : '' }}>Cycle</option>
                                        <option value="scooter" {{ old('vehicle_type') == 'scooter' ? 'selected' : '' }}>Scooter</option>
                                    </select>
                                    @error('vehicle_type')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vehicle Number</label>
                                    <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number') }}">
                                    @error('vehicle_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Document Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Aadhaar Number <span class="text-danger">*</span></label>
                                    <input type="text" name="aadhaar_number" class="form-control" 
                                           value="{{ old('aadhaar_number') }}" 
                                           pattern="[0-9]{12}" maxlength="12" required>
                                    @error('aadhaar_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Driving License Number <span class="text-danger">*</span></label>
                                    <input type="text" name="driving_license_number" class="form-control" 
                                           value="{{ old('driving_license_number') }}" required>
                                    @error('driving_license_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Profile Photo</label>
                                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                                    @error('profile_photo')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Aadhaar Front <span class="text-danger">*</span></label>
                                    <input type="file" name="aadhaar_front" class="form-control" accept="image/*" required>
                                    @error('aadhaar_front')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Aadhaar Back <span class="text-danger">*</span></label>
                                    <input type="file" name="aadhaar_back" class="form-control" accept="image/*" required>
                                    @error('aadhaar_back')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Driving License Photo <span class="text-danger">*</span></label>
                                    <input type="file" name="driving_license_photo" class="form-control" accept="image/*" required>
                                    @error('driving_license_photo')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Delivery Man</button>
                            <a href="{{ route('admin.delivery-men.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    function hideLoader() {
        if (typeof PageLoader !== 'undefined' && PageLoader.hide) {
            PageLoader.hide();
        }
        var loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hide');
            loader.style.display = 'none';
            loader.style.visibility = 'hidden';
            loader.style.opacity = '0';
        }
        $('#page-loader').addClass('hide').hide().css({
            'display': 'none',
            'visibility': 'hidden',
            'opacity': '0'
        });
    }
    
    $(document).ready(function() {
        hideLoader();
        
        // Filter cities based on state
        $('#state_id').on('change', function() {
            var stateId = $(this).val();
            var citySelect = $('#city_id');
            
            citySelect.html('<option value="">-- Select City --</option>');
            
            if (stateId) {
                @foreach($cities as $city)
                if ({{ $city->state_id }} == stateId) {
                    citySelect.append('<option value="{{ $city->id }}">{{ $city->name }}</option>');
                }
                @endforeach
            }
        });
    });
    
    $(window).on('load', function() {
        hideLoader();
    });
    
    if (document.readyState === 'complete') {
        hideLoader();
    }
    
    setTimeout(function() {
        hideLoader();
    }, 1000);
</script>
@endpush

