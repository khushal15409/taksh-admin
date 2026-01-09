@extends('layouts.admin.app')
@section('title',translate('Employee Edit'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        label.error {
            color: #dc3545 !important;
            font-size: 0.875rem !important;
            margin-top: 0.25rem !important;
            display: block !important;
            font-weight: normal !important;
            width: 100% !important;
            clear: both !important;
        }
        .form-control.error, 
        .form-control.error:focus,
        input.error,
        select.error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .select2-container.error .select2-selection {
            border-color: #dc3545 !important;
        }
        .error-message {
            color: #dc3545 !important;
            font-size: 0.875rem !important;
            margin-top: 0.25rem !important;
            display: block !important;
        }
        /* Ensure error labels are visible */
        .js-validate label.error {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/edit.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('messages.Employee_update')}}
            </span>
        </h1>
    </div>
    <form action="{{route('admin.users.employee.update',[$employee['id']])}}" method="post" enctype="multipart/form-data" class="js-validate">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-user"></i>
                    </span>
                    <span>{{translate('messages.general_information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="input-label qcont" for="f_name">{{translate('messages.first_name')}} <span class="form-label-secondary text-danger"> *</span></label>
                                <input type="text" name="f_name" value="{{$employee['f_name']}}" class="form-control" id="f_name" placeholder="{{translate('messages.first_name')}}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="input-label qcont" for="l_name">{{translate('messages.last_name')}} <span class="form-label-secondary text-danger"> *</span></label>
                                <input type="text" name="l_name" value="{{$employee['l_name']}}" class="form-control" id="l_name" placeholder="{{translate('messages.last_name')}}">
                            </div>
                            <div class="col-sm-6">
                                <label class="input-label" for="pincode">{{translate('messages.pincode')}} <span class="form-label-secondary text-danger"> *</span></label>
                                <input type="text" name="pincode" id="pincode" value="{{$employee->pincode ? $employee->pincode->pincode : old('pincode')}}" class="form-control" placeholder="{{translate('messages.Ex:')}} 110001" maxlength="6" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="input-label qcont" for="role_id">{{translate('messages.Role')}} <span class="form-label-secondary text-danger"> *</span></label>
                                <select class="form-control js-select2-custom w-100" name="role_id" id="role_id">
                                    <option value="" selected disabled>{{translate('messages.select_Role')}}</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}" {{$role['id']==$employee['role_id']?'selected':''}}>{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="input-label qcont" for="phone">{{translate('messages.phone')}} <span class="form-label-secondary text-danger"> *</span></label>
                                <input type="text" value="{{$employee['phone']}}" required name="phone" class="form-control" id="phone" placeholder="{{ translate('messages.Ex:') }} 9876543210" maxlength="10">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="h-100 d-flex flex-column">
                            <div class="text-center input-label qcont py-3 my-auto">
                                {{ translate('messages.Employee_image') }} <small class="text-danger"> ( {{ translate('messages.ratio') }} 1:1 )</small>
                            </div>
                            <div class="text-center py-3 my-auto">
                                <img class="img--100 onerror-image" id="viewer" data-onerror-image="{{asset('assets/admin/img/admin.png')}}" src="{{ $employee['image_full_url'] }}" alt="Employee thumbnail"/>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileUpload" class="custom-file-input" accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <span class="custom-file-label">{{translate('messages.choose_file')}}</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-user"></i>
                    </span>
                    <span>{{translate('messages.account_information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="input-label qcont" for="email">{{translate('messages.email')}} <span class="form-label-secondary text-danger"> *</span></label>
                        <input type="email" value="{{$employee['email']}}" name="email" class="form-control" id="email" placeholder="{{ translate('messages.Ex:') }} ex@gmail.com">
                    </div>
                    <div class="col-md-4">
                        <label class="input-label qcont" for="signupSrPassword">{{translate('messages.password')}}</label>
                        <input type="text" class="form-control" name="password" id="signupSrPassword" placeholder="{{ translate('messages.password_length_placeholder', ['length' => '8+']) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="btn--container justify-content-end mt-4">
            <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
            <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
        </div>
    </form>
</div>
@endsection

@push('script_2')
<!-- Load jQuery plugins after jQuery is available -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="{{asset('assets/admin')}}/js/view-pages/employee.js"></script>
<script>
    "use strict";
    // Wait for jQuery and plugins to be loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. Please ensure jQuery is loaded before these scripts.');
    } else {
        // Ensure jQuery is available as $ as well
        if (typeof $ === 'undefined') {
            window.$ = jQuery;
        }
    }
    
    // Wait for jQuery mask plugin
    if (typeof $.fn.mask === 'undefined') {
        console.error('jQuery Mask plugin is not loaded');
    }
    
    // Wait for jQuery validate plugin
    if (typeof $.validator === 'undefined') {
        console.error('jQuery Validate plugin is not loaded');
    }
    
    // Wait for jQuery to be fully loaded
    (function($) {
        'use strict';
        
        $(document).ready(function () {
            // Check if mask plugin is available
            if (typeof $.fn.mask !== 'undefined') {
                // jQuery Mask for Pincode (6 digits)
                $('#pincode').mask('000000', {
                    placeholder: '000000',
                    clearIfNotMatch: true
                });
                
                // jQuery Mask for Mobile Number (10 digits)
                $('#phone').mask('0000000000', {
                    placeholder: '0000000000',
                    clearIfNotMatch: true
                });
            } else {
                console.error('jQuery Mask plugin is not available');
            }
            
            // Wait for jQuery Validation to be loaded
            if (typeof $.validator === 'undefined') {
                console.error('jQuery Validation plugin is not loaded');
                return;
            }
            
            // jQuery Validation
            var validator = $('.js-validate').validate({
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            rules: {
                f_name: {
                    required: true,
                    maxlength: 100
                },
                l_name: {
                    required: true,
                    maxlength: 100
                },
                pincode: {
                    required: true,
                    minlength: 6,
                    maxlength: 6,
                    digits: true
                },
                role_id: {
                    required: true,
                    notEqual: ""
                },
                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    minlength: 8,
                    pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/
                },
                image: {
                    accept: "image/jpeg,image/jpg,image/png,image/gif,image/webp"
                }
            },
            messages: {
                f_name: {
                    required: "{{ translate('messages.first_name_is_required') }}",
                    maxlength: "{{ translate('First name must not exceed 100 characters') }}"
                },
                l_name: {
                    maxlength: "{{ translate('Last name must not exceed 100 characters') }}"
                },
                pincode: {
                    required: "{{ translate('Pincode is required') }}",
                    minlength: "{{ translate('Pincode must be exactly 6 digits') }}",
                    maxlength: "{{ translate('Pincode must be exactly 6 digits') }}",
                    digits: "{{ translate('Pincode must contain only digits') }}"
                },
                role_id: {
                    required: "{{ translate('messages.select_Role') }}",
                    notEqual: "{{ translate('messages.select_Role') }}"
                },
                phone: {
                    required: "{{ translate('Phone number is required') }}",
                    minlength: "{{ translate('Phone number must be exactly 10 digits') }}",
                    maxlength: "{{ translate('Phone number must be exactly 10 digits') }}",
                    digits: "{{ translate('Phone number must contain only digits') }}"
                },
                email: {
                    required: "{{ translate('Email is required') }}",
                    email: "{{ translate('Please enter a valid email address') }}"
                },
                password: {
                    minlength: "{{ translate('Password must be at least 8 characters long') }}",
                    pattern: "{{ translate('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character') }}"
                },
                image: {
                    accept: "{{ translate('Please upload a valid image file (jpeg, jpg, png, gif, webp)') }}"
                }
            },
            errorPlacement: function(error, element) {
                // Remove any existing error for this field
                element.next('label.error').remove();
                element.parent().find('label.error').remove();
                
                // Make error visible with inline styles
                error.css({
                    'display': 'block !important',
                    'color': '#dc3545 !important',
                    'font-size': '0.875rem !important',
                    'margin-top': '0.25rem !important',
                    'font-weight': 'normal !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important'
                });
                
                if (element.attr("name") == "role_id") {
                    // For select2 dropdowns, place error after the container
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.parent().hasClass('input-group')) {
                    // For input groups (like password fields)
                    error.insertAfter(element.parent());
                } else {
                    // For regular inputs - place directly after the input
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('error').removeClass(validClass);
                if ($(element).hasClass('js-select2-custom')) {
                    $(element).next('.select2-container').addClass('error');
                }
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('error').addClass(validClass);
                $(element).next('label.error').remove();
                if ($(element).hasClass('js-select2-custom')) {
                    $(element).next('.select2-container').removeClass('error');
                }
            },
            invalidHandler: function(event, validator) {
                // Show first error in console for debugging
                console.log('Validation errors:', validator.errorList);
                
                // Force show all error messages
                validator.errorList.forEach(function(error) {
                    var $element = $(error.element);
                    var $error = $element.next('label.error');
                    if ($error.length) {
                        $error.css({
                            'display': 'block !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important'
                        });
                    }
                });
            },
            submitHandler: function(form) {
                // Remove any existing error messages
                $('.error').removeClass('error');
                
                // Show loading state
                var submitBtn = $(form).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="tio-loading"></i> {{ translate("messages.processing") }}...');
                
                form.submit();
            }
        });
        
        // Custom validation method for select dropdown
        $.validator.addMethod("notEqual", function(value, element, param) {
            return this.optional(element) || value != param;
        }, "{{ translate('messages.select_Role') }}");
        
        // Custom validation method for password pattern
        $.validator.addMethod("pattern", function(value, element, param) {
            if (!value) return true; // Allow empty for edit form
            return param.test(value);
        }, "{{ translate('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character') }}");
        
        // Test validation on form submit
        $('.js-validate').on('submit', function(e) {
            if (!validator.form()) {
                e.preventDefault();
                e.stopPropagation();
                // Scroll to first error
                var firstError = $('.js-validate label.error:first');
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);
                }
                return false;
            }
        });
        
        // Reset button handler
        $('#reset_btn').click(function(){
            $('#viewer').attr('src', "{{ $employee['image_full_url'] }}");
            $('#customFileUpload').val(null);
            $('#pincode').val("{{ $employee->pincode ? $employee->pincode->pincode : '' }}").trigger('input');
            $('#phone').val("{{ $employee['phone'] }}").trigger('input');
            $('#role_id').val("{{ $employee['role_id'] }}").trigger('change');
            
            // Reset validation
            $('.js-validate').validate().resetForm();
            $('.error').removeClass('error');
            $('.form-control').removeClass('error');
        });
        }); // End of document.ready
    })(jQuery || window.jQuery || window.$); // End of IIFE
</script>
@endpush

