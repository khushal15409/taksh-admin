@extends('layouts.admin.app')
@section('title',translate('Add Geography'))

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
            <span>{{translate('messages.add_new_geography')}}</span>
        </h1>
    </div>
    <form action="{{route('admin.access-control.geography.store')}}" method="post">
        @csrf
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon"><i class="tio-map"></i></span>
                    <span>{{translate('messages.geography_information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="input-label" for="level">{{translate('messages.level')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <select class="form-control js-select2-custom w-100" name="level" id="level" required>
                            <option value="" selected disabled>{{translate('messages.select_level')}}</option>
                            @foreach($levels as $level)
                                <option value="{{$level}}" {{old('level') == $level ? 'selected' : ''}}>{{ucfirst($level)}}</option>
                            @endforeach
                        </select>
                        @error('level')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="parent_id">{{translate('messages.parent_geography')}}</label>
                        <select class="form-control js-select2-custom w-100" name="parent_id" id="parent_id">
                            <option value="">{{translate('messages.no_parent')}}</option>
                            @foreach($parentGeographies as $parent)
                                <option value="{{$parent->id}}" {{old('parent_id') == $parent->id ? 'selected' : ''}}>
                                    {{$parent->name}} ({{ucfirst($parent->level)}})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{translate('messages.select_parent_for_hierarchy')}}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="name">{{translate('messages.name')}}<span class="form-label-secondary text-danger"> *</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="{{translate('messages.Ex:')}} Maharashtra" value="{{old('name')}}" required>
                        @error('name')
                            <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="input-label" for="code">{{translate('messages.code')}}</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="{{translate('messages.Ex:')}} maharashtra" value="{{old('code')}}" maxlength="50">
                        <small class="text-muted">{{translate('messages.auto_generated_if_empty')}}</small>
                    </div>
                    <div class="col-md-6" id="state_field" style="display:none;">
                        <label class="input-label" for="state_id">{{translate('messages.state')}}</label>
                        <select class="form-control js-select2-custom w-100" name="state_id" id="state_id">
                            <option value="">{{translate('messages.select_state')}}</option>
                            @foreach($states as $state)
                                <option value="{{$state->id}}" {{old('state_id') == $state->id ? 'selected' : ''}}>{{$state->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" id="zone_field" style="display:none;">
                        <label class="input-label" for="zone_id">{{translate('messages.zone')}}</label>
                        <select class="form-control js-select2-custom w-100" name="zone_id" id="zone_id">
                            <option value="">{{translate('messages.select_zone')}}</option>
                            @foreach($zones as $zone)
                                <option value="{{$zone->id}}" {{old('zone_id') == $zone->id ? 'selected' : ''}}>{{$zone->name ?? $zone->display_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" id="area_field" style="display:none;">
                        <label class="input-label" for="area_id">{{translate('messages.area')}}</label>
                        <select class="form-control js-select2-custom w-100" name="area_id" id="area_id">
                            <option value="">{{translate('messages.select_area')}}</option>
                            @foreach($areas as $area)
                                <option value="{{$area->id}}" {{old('area_id') == $area->id ? 'selected' : ''}}>{{$area->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" id="pincode_field" style="display:none;">
                        <label class="input-label" for="pincode_id">{{translate('messages.pincode')}}</label>
                        <select class="form-control js-select2-custom w-100" name="pincode_id" id="pincode_id">
                            <option value="">{{translate('messages.select_pincode')}}</option>
                            @foreach($pincodes as $pincode)
                                <option value="{{$pincode->id}}" {{old('pincode_id') == $pincode->id ? 'selected' : ''}}>{{$pincode->pincode}} - {{$pincode->officename}}</option>
                            @endforeach
                        </select>
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
        // Show/hide fields based on level
        $('#level').on('change', function() {
            let level = $(this).val();
            $('#state_field, #zone_field, #area_field, #pincode_field').hide();
            
            if (level === 'state' || level === 'zone' || level === 'area' || level === 'pincode') {
                $('#state_field').show();
            }
            if (level === 'zone' || level === 'area' || level === 'pincode') {
                $('#zone_field').show();
            }
            if (level === 'area' || level === 'pincode') {
                $('#area_field').show();
            }
            if (level === 'pincode') {
                $('#pincode_field').show();
            }
        });
        
        // Trigger on load if value exists
        if ($('#level').val()) {
            $('#level').trigger('change');
        }
    });
</script>
@endpush

