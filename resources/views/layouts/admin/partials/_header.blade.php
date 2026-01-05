<div id="headerMain" class="d-none">
    <header id="header"
            class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered pr-0">
        <div class="navbar-nav-wrap">

            <div class="navbar-nav-wrap-content-left d-xl-none">
                <!-- Navbar Vertical Toggle -->
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                       data-placement="right" title="Collapse"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                       data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                       data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
                <!-- End Navbar Vertical Toggle -->
            </div>

            <!-- Secondary Content -->
            <div class="navbar-nav-wrap-content-right flex-grow-1 w-0">
                <!-- Navbar -->
                <ul class="navbar-nav align-items-center flex-row flex-grow-1 __navbar-nav">

                    <li class="nav-item __nav-item">
                        <a href="{{ route('admin.dashboard')}}" id="tourb-6"
                           class="__nav-link navbar-round-btn {{ Request::is('admin/users*') ? 'active' : '' }}">
                            <img src="{{asset('assets/admin/img/new-img/user.svg')}}" alt="public/img">
                            <span>{{ translate('Admin Users')}}</span>
                        </a>
                    </li>

                    <li class="nav-item __nav-item">
                        <a href="{{ route('admin.dashboard')}}" id="tourb-6"
                           class="__nav-link navbar-round-btn {{ Request::is('admin/users*') ? 'active' : '' }}">
                            <img src="{{asset('assets/admin/img/new-img/user.svg')}}" alt="public/img">
                            <span>{{ translate('Ecommerce Users')}}</span>
                        </a>
                    </li>

                    <li class="nav-item __nav-item">
                        <a href="{{ route('admin.dashboard')}}" id="tourb-8"
                           class="__nav-link navbar-round-btn {{ Request::is('admin/salesman-near-by-user*') ? 'active' : '' }}">
                            <img src="{{asset('assets/admin/img/new-img/user.svg')}}" alt="public/img">
                            <span>{{ translate('Salesman Users')}}</span>
                        </a>
                    </li>

                    <li class="nav-item __nav-item">
                        <a href="{{ route('admin.dashboard')}}" id="tourb-7"
                           class="__nav-link navbar-round-btn {{ Request::is('admin/transactions*') ? 'active' : '' }}">
                            <img src="{{asset('assets/admin/img/new-img/transaction-and-report.svg')}}"
                                 alt="public/img">
                            <span>{{ translate('Vandor Users')}}</span>
                        </a>
                    </li>

                    <!-- <li class="nav-item __nav-item">
                        <a href="{{ route('admin.dashboard') }}" id="tourb-3"
                           class="__nav-link navbar-round-btn {{ Request::is('admin/business-settings*') ? 'active' : '' }}">
                            <img src="{{asset('assets/admin/img/new-img/setting-icon.svg')}}" alt="public/img">
                            <span>{{ translate('messages.Settings') }}</span>
                            <svg width="14" viewBox="0 0 14 14" fill="none">
                                <path d="M2.33325 5.25L6.99992 9.91667L11.6666 5.25" stroke="#ffffff" stroke-width="1.5"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <div class="__nav-module" id="tourb-4">
                            <div class="__nav-module-header">
                                <div class="inner">
                                    <h4>{{translate('Settings')}}</h4>
                                    <p>
                                        {{translate('Monitor your business general settings from here')}}
                                    </p>
                                </div>
                            </div>
                            <div class="__nav-module-body">
                                <ul>
                                    @if (\App\CentralLogics\Helpers::module_permission_check('module'))
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}"
                                               class="next-tour">
                                                <img
                                                    src="{{asset('assets/admin/img/navbar-setting-icon/module.svg')}}"
                                                    alt="">
                                                <span>{{translate('System Module Setup')}}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (\App\CentralLogics\Helpers::module_permission_check('zone'))
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}"
                                               class="next-tour">
                                                <img
                                                    src="{{asset('assets/admin/img/navbar-setting-icon/location.svg')}}"
                                                    alt="">
                                                <span>{{translate('Zone Setup')}}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if (\App\CentralLogics\Helpers::module_permission_check('settings'))
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}"
                                               class="next-tour">
                                                <img
                                                    src="{{asset('assets/admin/img/navbar-setting-icon/business.svg')}}"
                                                    alt="">
                                                <span>{{translate('Business Settings')}}</span>
                                            </a>
                                        </li>
                                    @endif

                                    @if (\App\CentralLogics\Helpers::module_permission_check('settings'))
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}"
                                               class="next-tour">
                                                <img
                                                    src="{{asset('assets/admin/img/navbar-setting-icon/third-party.svg')}}"
                                                    alt="">
                                                <span>{{translate('3rd Party')}}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{route('admin.dashboard')}}"
                                               class="next-tour">
                                                <img
                                                    src="{{asset('assets/admin/img/navbar-setting-icon/social.svg')}}"
                                                    alt="">
                                                <span>{{translate('Social Media and Page Setup')}}</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                <div class="text-center mt-2">
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="next-tour">{{translate('View All')}}</a>
                                </div>
                            </div>
                        </div>
                    </li> -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('order'))
                        <li class="nav-item __nav-item">
                            <a href="{{ route('admin.dashboard')}}" id="tourb-8"
                               class="__nav-link navbar-round-btn {{ Request::is('admin/dispatch*') ? 'active' : '' }}">
                                <img src="{{asset('assets/admin/img/new-img/dispatch.svg')}}" alt="public/img">
                                <span>{{ translate('Delivery Users')}}</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item max-sm-m-0">
                        <div class="hs-unfold">
                            <div>
                                @php( $local = session()->has('local')?session('local'): null)
                                @php($lang = \App\Models\BusinessSetting::where('key', 'system_language')->first())
                                @if ($lang)
                                    <div
                                        class="topbar-text dropdown disable-autohide text-capitalize d-flex">
                                        <a class="topbar-link dropdown-toggle d-flex align-items-center title-color"
                                           href="#" data-toggle="dropdown">
                                            @foreach(json_decode($lang['value'],true) as $data)
                                                @if($data['code']==$local)
                                                    <i class="tio-globe"></i> {{$data['code']}}

                                                @elseif(!$local &&  $data['default'] == true)
                                                    <i class="tio-globe"></i> {{$data['code']}}
                                                @endif
                                            @endforeach
                                        </a>
                                        <ul class="dropdown-menu lang-menu">
                                            @foreach(json_decode($lang['value'],true) as $key =>$data)
                                                @if($data['status']==1)
                                                    <li>
                                                        <a class="dropdown-item py-1"
                                                           href="{{route('admin.dashboard')}}">
                                                            <span class="text-capitalize">{{$data['code']}}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                    @php($mod = null)
                    @php($is_logistics = false)
                    <li class="nav-item __nav-item ml-auto">
                        <a href="javascript:void(0)" class="__nav-link navbar-round-btn" id="tourb-0">
                            <img src="{{asset('assets/admin/img/new-img/setting-icon.svg')}}"
                                 alt="public/img">
                            <span>{{ translate('messages.Settings') }}</span>
                            <svg width="14" viewBox="0 0 14 14" fill="none">
                                <path d="M2.33325 5.25L6.99992 9.91667L11.6666 5.25" stroke="#ffffff" stroke-width="1.5"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <div class="__nav-module style-2" id="tourb-1">
                            <div class="__nav-module-header">
                                <div class="inner">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-12">
                                            <h5>{{translate('messages.Settings')}}</h5>
                                            <p class="m-0">
                                                {{translate('Manage your account and preferences')}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="__nav-module-body">
                                <div class="__nav-module-items" style="display: flex; flex-direction: column; gap: 10px; padding: 15px;">
                                    <a href="{{route('admin.profile')}}" class="settings-dropdown-btn" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: linear-gradient(135deg, #0177cd 0%, #0e4da3 100%); color: #ffffff; border-radius: 8px; text-decoration: none; transition: all 0.3s ease; border: none; width: 100%;">
                                        <i class="tio-user" style="font-size: 18px;"></i>
                                        <span style="font-weight: 500;">{{translate('Profile')}}</span>
                                    </a>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();" class="settings-dropdown-btn" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: linear-gradient(135deg, #0177cd 0%, #0e4da3 100%); color: #ffffff; border-radius: 8px; text-decoration: none; transition: all 0.3s ease; border: none; width: 100%;">
                                        <i class="tio-sign-out" style="font-size: 18px;"></i>
                                        <span style="font-weight: 500;">{{translate('messages.sign_out')}}</span>
                                    </a>
                                    <form id="header-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- End Navbar -->
            </div>
            <!-- End Secondary Content -->
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>


<div class="toggle-tour">
    <a href="https://youtube.com/playlist?list=PLLFMbDpKMZBxgtX3n3rKJvO5tlU8-ae2Y" target="_blank"
       class="d-flex align-items-center gap-10px">
        <img src="{{ asset('assets/admin/img/tutorial.svg') }}" alt="">
        <span>
            <span class="text-capitalize">{{ translate('Turotial') }}</span>
        </span>
    </a>
    <div class="d-flex align-items-center gap-10px restart-Tour">
        <img src="{{ asset('assets/admin/img/tour.svg') }}" alt="">
        <span>
            <span class="text-capitalize">{{ translate('Tour') }}</span>
        </span>
    </div>
</div>

