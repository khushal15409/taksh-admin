<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{translate('messages.login')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="auth-wrapper">
        <div class="auth-wrapper-left">
            <div class="auth-left-cont">
                @php($store_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first())
                @php($storage_type = (isset($store_logo) && isset($store_logo->storage) && is_array($store_logo->storage) && isset($store_logo->storage[0])) ? $store_logo->storage[0]->value : 'public')
                <img class="onerror-image" data-onerror-image="{{asset('assets/admin/img/favicon.png')}}"
                src="{{\App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value ?? '', $storage_type, 'favicon')}}" alt="Logo">
                <h2 class="title">{{translate('Your')}} <span class="d-block">{{translate('All Service')}}</span> <strong class="text--039D55">{{translate('in one field')}}....</strong></h2>
            </div>
        </div>
        <div class="auth-wrapper-right">
            <label class="badge badge-soft-success __login-badge">
                {{translate('messages.software_version')}} : {{env('SOFTWARE_VERSION', '1.0.0')}}
            </label>

            <!-- Card -->
            <div class="auth-wrapper-form" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 8px 25px rgba(0, 0, 0, 0.1); padding: 50px 45px; margin: 20px 0; transition: all 0.3s ease; max-width: 500px; width: 100%; position: relative; overflow: hidden;">
                <!-- Gradient overlay decoration -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(1, 119, 205, 0.1) 0%, rgba(14, 77, 163, 0.05) 100%); border-radius: 50%; z-index: 0;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(1, 119, 205, 0.08) 0%, rgba(14, 77, 163, 0.03) 100%); border-radius: 50%; z-index: 0;"></div>
                <div style="position: relative; z-index: 1;">
                <!-- Form -->
                <form class="" action="{{route('login.post')}}" method="post" id="form-id">
                    @csrf
                    <div class="auth-header" style="margin-bottom: 35px; text-align: center;">
                        <div class="mb-4">
                            <h2 class="title" style="font-size: 32px; font-weight: 700; margin-bottom: 10px; background: linear-gradient(135deg, #0177cd 0%, #0e4da3 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Admin {{translate('messages.login')}}</h2>
                            <div style="color: #64748b; font-size: 15px; line-height: 1.6;">{{translate('messages.welcome_back_login_to_your_panel')}}.</div>
                        </div>
                    </div>

                    <!-- Form Group -->
                    <div class="js-form-message form-group" style="margin-bottom: 24px;">
                        <label class="input-label text-capitalize" for="signinSrEmail" style="font-weight: 600; color: #334155; margin-bottom: 10px; font-size: 14px; display: block;">{{translate('messages.your_email')}}</label>

                        <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                                tabindex="1" placeholder="email@address.com" value="{{ old('email') }}" aria-label="email@address.com"
                                required data-msg="{{ translate('Please_enter_a_valid_email_address.') }}"
                                style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 14px 18px; transition: all 0.3s ease; font-size: 15px; width: 100%;"
                                onfocus="this.style.borderColor='#0177cd'; this.style.boxShadow='0 0 0 4px rgba(1, 119, 205, 0.12)'; this.style.background='#ffffff'"
                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#ffffff'">
                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    <div class="js-form-message form-group mb-2" style="margin-bottom: 24px;">
                        <label class="input-label" for="signupSrPassword" tabindex="0" style="font-weight: 600; color: #334155; margin-bottom: 10px; font-size: 14px; display: block;">
                            <span class="d-flex justify-content-between align-items-center">
                                {{translate('messages.password')}}
                            </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                    name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" value="{{ old('password') }}"
                                    aria-label="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" required
                                    data-msg="{{translate('messages.invalid_password_warning')}}"
                                    style="border-radius: 10px 0 0 10px; border: 2px solid #e2e8f0; border-right: none; padding: 14px 18px; transition: all 0.3s ease; font-size: 15px;"
                                    onfocus="this.style.borderColor='#0177cd'; this.style.boxShadow='0 0 0 4px rgba(1, 119, 205, 0.12)'; this.style.background='#ffffff'"
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#ffffff'"
                                    data-hs-toggle-password-options='{
                                                "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                    }'>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:" style="border-radius: 0 10px 10px 0; border: 2px solid #e2e8f0; border-left: none; background: #ffffff; padding: 14px 18px; transition: all 0.3s ease;"
                                   onmouseover="this.style.backgroundColor='#f8f9fa'; this.style.borderColor='#0177cd'"
                                   onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#e2e8f0'">
                                    <i id="changePassIcon" class="tio-visible-outlined" style="color: #64748b; font-size: 18px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End Form Group -->

                    <div class="d-flex justify-content-between mt-4" style="margin-top: 24px;">
                        <!-- Checkbox -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsCheckbox" {{ old('remember') ? 'checked' : '' }}
                                        name="remember" style="cursor: pointer;">
                                <label class="custom-control-label text-muted" for="termsCheckbox" style="cursor: pointer; color: #64748b; font-size: 14px;">
                                    {{translate('messages.remember_me')}}
                                </label>
                            </div>
                        </div>
                        <!-- End Checkbox -->
                        <!-- forget password -->
                        <div class="form-group" id="forget-password" style="margin-bottom: 0;">
                            <div class="custom-control">
                                <span type="button" data-toggle="modal" class="text-primary" data-target="#forgetPassModal" style="cursor: pointer; font-size: 14px; font-weight: 500; color: #0177cd; transition: color 0.3s ease;"
                                      onmouseover="this.style.color='#0e4da3'"
                                      onmouseout="this.style.color='#0177cd'">{{ translate('Forget Password') }}?</span>
                            </div>
                        </div>
                        <!-- End forget password -->
                    </div>

                    @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0">
                        <div class="row p-2 d-none" id="reload-captcha" style="margin-top: 20px;">
                            <div class="col-6 pr-0">
                                <input type="text" class="form-control form-control-lg border-0" name="custome_recaptcha"
                                        id="custome_recaptcha" required placeholder="{{translate('Enter recaptcha value')}}" autocomplete="off" value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}"
                                        style="border-radius: 8px 0 0 8px; border: 1px solid #e2e8f0 !important;">
                            </div>
                            <div class="col-6 bg-white rounded d-flex" style="border-radius: 0 8px 8px 0; border: 1px solid #e2e8f0; border-left: none; overflow: hidden;">
                                <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                                <div class="p-3 pr-0 capcha-spin reloadCaptcha" style="cursor: pointer; background: #f8f9fa; transition: background 0.3s ease;"
                                     onmouseover="this.style.background='#e9ecef'"
                                     onmouseout="this.style.background='#f8f9fa'">
                                    <i class="tio-cached"></i>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row p-2" id="reload-captcha" style="margin-top: 20px;">
                            <div class="col-6 pr-0">
                                <input type="text" class="form-control form-control-lg border-0" name="custome_recaptcha"
                                        id="custome_recaptcha" required placeholder="{{translate('Enter recaptcha value')}}" autocomplete="off" value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}"
                                        style="border-radius: 8px 0 0 8px; border: 1px solid #e2e8f0 !important;">
                            </div>
                            <div class="col-6 bg-white rounded d-flex" style="border-radius: 0 8px 8px 0; border: 1px solid #e2e8f0; border-left: none; overflow: hidden;">
                                <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                                <div class="p-3 pr-0 capcha-spin reloadCaptcha" style="cursor: pointer; background: #f8f9fa; transition: background 0.3s ease;"
                                     onmouseover="this.style.background='#e9ecef'"
                                     onmouseout="this.style.background='#f8f9fa'">
                                    <i class="tio-cached"></i>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-lg btn-block btn--primary mt-xxl-3" id="signInBtn" style="border-radius: 10px; padding: 14px 20px; font-weight: 600; font-size: 16px; background: linear-gradient(135deg, #0177cd 0%, #0e4da3 100%); border: none; box-shadow: 0 6px 20px rgba(1, 119, 205, 0.3), 0 2px 8px rgba(1, 119, 205, 0.2); transition: all 0.3s ease; margin-top: 28px; text-transform: uppercase; letter-spacing: 0.5px;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(1, 119, 205, 0.4), 0 4px 12px rgba(1, 119, 205, 0.25)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(1, 119, 205, 0.3), 0 2px 8px rgba(1, 119, 205, 0.2)'">{{translate('messages.login')}}</button>
                </form>
                <!-- End Form -->
                @if(env('APP_MODE') == 'demo')
                <div class="auto-fill-data-copy" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <span class="d-block" style="font-size: 13px; color: #475569;"><strong style="color: #1e293b;">Email</strong> : admin@taksh.com</span>
                            <span class="d-block" style="font-size: 13px; color: #475569;"><strong style="color: #1e293b;">Password</strong> : admin123</span>
                        </div>
                        <div>
                            <button class="btn action-btn btn--primary m-0 copy_cred" style="border-radius: 6px; padding: 8px 12px;"><i class="tio-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <!-- End Card -->

        </div>
    </div>
</main>
<!-- ========== END MAIN CONTENT ========== -->
<div class="modal fade" id="forgetPassModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-end">
        <span type="button" class="close-modal-icon" data-dismiss="modal">
            <i class="tio-clear"></i>
        </span>
      </div>
      <div class="modal-body">
        <div class="forget-pass-content">
            <img src="{{asset('assets/admin/img/send-mail.svg')}}" alt="">
            <h4>
                {{ translate('Send_Mail_to_Your_Email') }} ?
            </h4>
            <p>
                {{ translate('A mail will be send to your registered email with a link to change password') }}
            </p>
            <a class="btn btn-lg btn-block btn--primary mt-3" href="{{route('reset-password')}}">
                {{ translate('Send Mail') }}
            </a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- JS Implementing Plugins -->
<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
@if(session('success'))
    <script>
        toastr.success('{{ session('success') }}', 'Success!', {
            CloseButton: true,
            ProgressBar: true
        });
    </script>
@endif
@if(session('error'))
    <script>
        toastr.error('{{ session('error') }}', 'Error!', {
            CloseButton: true,
            ProgressBar: true
        });
    </script>
@endif
@if(session('info'))
    <script>
        toastr.info('{{ session('info') }}', 'Info!', {
            CloseButton: true,
            ProgressBar: true
        });
    </script>
@endif
@if(session('warning'))
    <script>
        toastr.warning('{{ session('warning') }}', 'Warning!', {
            CloseButton: true,
            ProgressBar: true
        });
    </script>
@endif

@if ($errors->any())
    <script>
        "use strict";
        @foreach($errors->all() as $error)
        toastr.error('{{translate($error)}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script>
    "use strict";
    $(document).on('ready', function () {
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });

$(document).on('click','.reloadCaptcha', function(){
    $.ajax({
        url: "{{ route('reload-captcha') }}",
        type: "GET",
        dataType: 'json',
        beforeSend: function () {
            $('#loading').show()
            $('.capcha-spin').addClass('active')
        },
        success: function(data) {
            $('#reload-captcha').html(data.view);
        },
        complete: function () {
            $('#loading').hide()
            $('.capcha-spin').removeClass('active')
        }
    });
});

    $(document).ready(function() {
        $('.onerror-image').on('error', function() {
            let img = $(this).data('onerror-image')
            $(this).attr('src', img);
        });
    });
</script>
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
@endif
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script>
        $(document).ready(function() {
            $('#signInBtn').click(function (e) {
                if( $('#set_default_captcha_value').val() == 1){
                    $('#form-id').submit();
                    return true;
                }
                e.preventDefault();
                if (typeof grecaptcha === 'undefined') {
                    toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');
                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');
                    return;
                }
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                        $('#g-recaptcha-response').value = token;
                        $('#form-id').submit();
                    });
                });
                window.onerror = function (message) {
                    var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                    if (message.includes('Invalid site key')) {
                        errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                    } else if (message.includes('not loaded in api.js')) {
                        errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                    }
                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');
                    toastr.error(errorMessage)
                    return true;
                };
            });
        });
    </script>
@endif

@if(env('APP_MODE')=='demo')
    <script>
        "use strict";
        $('.copy_cred').on('click', function () {
            $('#signinSrEmail').val('admin@taksh.com');
            $('#signupSrPassword').val('admin123');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        })
    </script>
@endif

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>

