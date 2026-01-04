@extends('layouts.admin.app')

@section('title', translate('Profile'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .profile-card {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 8px 32px rgba(1, 119, 205, 0.15) !important;
            border-radius: 16px !important;
        }
        .profile-header {
            background: linear-gradient(135deg, #0177cd 0%, #0e4da3 100%);
            border-radius: 16px 16px 0 0;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #ffffff;
            margin: 0 auto 20px;
            object-fit: cover;
        }
        .btn-profile {
            background: linear-gradient(135deg, #0177cd 0%, #0e4da3 100%) !important;
            border: none !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        .btn-profile:hover {
            background: linear-gradient(135deg, #0e4da3 0%, #0177cd 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(1, 119, 205, 0.3) !important;
        }
        .form-control:focus {
            border-color: #0177cd !important;
            box-shadow: 0 0 0 0.2rem rgba(1, 119, 205, 0.25) !important;
        }
        .input-label {
            color: #334155 !important;
            font-weight: 600 !important;
        }
    </style>
@endpush

@section('content')
@include('admin-views.partials._loader')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center py-2">
                <div class="col-sm mb-2 mb-sm-0">
                    <div class="d-flex align-items-center">
                        <i class="tio-user" style="font-size: 32px; color: #0177cd; margin-right: 12px;"></i>
                        <div class="w-0 flex-grow pl-2">
                            <h1 class="page-header-title mb-0">{{translate('Profile')}}</h1>
                            <p class="page-header-text m-0">{{translate('Manage your account information and settings')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row g-3">
            <!-- Profile Information Card -->
            <div class="col-lg-4">
                <div class="card profile-card h-100">
                    <div class="profile-header">
                        <div class="position-relative d-inline-block">
                            <img class="profile-avatar" id="viewer"
                                 src="{{ $user->image ? \App\CentralLogics\Helpers::get_full_url('admin', $user->image, \App\CentralLogics\Helpers::getDisk(), 'admin') : asset('assets/admin/img/160x160/img1.jpg') }}"
                                 alt="Profile Image">
                            <label for="customFileUpload" class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 cursor-pointer" style="cursor: pointer; border: 2px solid #0177cd;">
                                <i class="tio-camera" style="color: #0177cd; font-size: 18px;"></i>
                            </label>
                        </div>
                        <h3 class="mb-1">{{ $user->name ?? 'Admin' }}</h3>
                        <p class="mb-0 opacity-75">{{ $user->email ?? 'admin@example.com' }}</p>
                        @if($user->mobile)
                        <p class="mb-0 opacity-75">{{ $user->mobile }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Update Profile Form -->
            <div class="col-lg-8">
                <div class="card profile-card">
                    <div class="card-header" style="background: rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                        <h5 class="card-title mb-0">
                            <i class="tio-edit" style="color: #0177cd;"></i>
                            {{translate('Update Profile Information')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update') }}" method="post" enctype="multipart/form-data" id="profile-form">
                            @csrf
                            <input type="file" name="image" id="customFileUpload" class="d-none" accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="name">
                                            {{translate('Full Name')}} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name" class="form-control" 
                                               value="{{ old('name', $user->name) }}" 
                                               placeholder="{{translate('Enter your full name')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="email">
                                            {{translate('Email Address')}} <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" id="email" class="form-control" 
                                               value="{{ old('email', $user->email) }}" 
                                               placeholder="{{translate('Enter your email')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="mobile">
                                            {{translate('Mobile Number')}}
                                        </label>
                                        <input type="text" name="mobile" id="mobile" class="form-control" 
                                               value="{{ old('mobile', $user->mobile) }}" 
                                               placeholder="{{translate('Enter your mobile number')}}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="btn--container justify-content-end">
                                        <button type="submit" class="btn btn-profile">
                                            <i class="tio-save"></i> {{translate('Update Profile')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="card profile-card mt-3">
                    <div class="card-header" style="background: rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                        <h5 class="card-title mb-0">
                            <i class="tio-lock" style="color: #0177cd;"></i>
                            {{translate('Change Password')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update-password') }}" method="post" id="password-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label" for="current_password">
                                            {{translate('Current Password')}} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" id="current_password" 
                                                   class="form-control" placeholder="{{translate('Enter current password')}}" required>
                                            <div class="input-group-append">
                                                <a class="input-group-text" href="javascript:" onclick="togglePassword('current_password', this)">
                                                    <i class="tio-hidden-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="password">
                                            {{translate('New Password')}} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password" 
                                                   class="form-control" placeholder="{{translate('Enter new password')}}" required>
                                            <div class="input-group-append">
                                                <a class="input-group-text" href="javascript:" onclick="togglePassword('password', this)">
                                                    <i class="tio-hidden-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="password_confirmation">
                                            {{translate('Confirm Password')}} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                                   class="form-control" placeholder="{{translate('Confirm new password')}}" required>
                                            <div class="input-group-append">
                                                <a class="input-group-text" href="javascript:" onclick="togglePassword('password_confirmation', this)">
                                                    <i class="tio-hidden-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="btn--container justify-content-end">
                                        <button type="submit" class="btn btn-profile">
                                            <i class="tio-lock"></i> {{translate('Update Password')}}
                                        </button>
                                    </div>
                                </div>
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
    // Hide loader when page is fully loaded
    $(document).ready(function() {
        // Hide the page loader
        if (typeof PageLoader !== 'undefined') {
            PageLoader.hide();
        }
        
        // Also hide the main loading element if it exists
        $('#loading').addClass('initial-hidden');
    });

    // Also hide loader on window load (when all resources are loaded)
    window.addEventListener('load', function() {
        if (typeof PageLoader !== 'undefined') {
            PageLoader.hide();
        }
        $('#loading').addClass('initial-hidden');
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileUpload").change(function () {
        readURL(this);
    });

    function togglePassword(inputId, element) {
        var input = document.getElementById(inputId);
        var icon = element.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('tio-hidden-outlined');
            icon.classList.add('tio-visible-outlined');
        } else {
            input.type = "password";
            icon.classList.remove('tio-visible-outlined');
            icon.classList.add('tio-hidden-outlined');
        }
    }
</script>
@endpush

