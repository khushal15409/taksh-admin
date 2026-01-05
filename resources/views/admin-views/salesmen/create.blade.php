@extends('layouts.admin.app')

@section('title', 'Add Salesman')

@push('css_or_js')
@endpush

@section('content')
@include('admin-views.partials._loader')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Add Salesman</h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.salesmen.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">Salesman Information</h5>
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

                    <form action="{{ route('admin.salesmen.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="mobile_number" 
                                   class="form-control" 
                                   value="{{ old('mobile_number') }}" 
                                   pattern="[0-9]{10}" 
                                   maxlength="10"
                                   required>
                            <small class="form-text text-muted">10 digits only</small>
                            @error('mobile_number')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="{{ old('email') }}">
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

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

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       id="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Salesman</button>
                            <a href="{{ route('admin.salesmen.index') }}" class="btn btn-secondary">Cancel</a>
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

