<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Gregwar\Captcha\CaptchaBuilder;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        // Apply guest middleware to all methods except logout
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        // Check if user is already logged in
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());

        return view('auth.login', compact('custome_recaptcha'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1 && !$request->set_default_captcha) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'] ?? null;
                        if (!$secret_key) {
                            $fail(translate('ReCaptcha configuration missing'));
                            return;
                        }
                        $gResponse = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret' => $secret_key,
                            'response' => $value,
                            'remoteip' => \request()->ip(),
                        ]);

                        if (!$gResponse->successful() || !$gResponse->json()['success']) {
                            $fail(translate('ReCaptcha Failed'));
                        }
                    },
                ],
            ]);
        } else if (strtolower(session('six_captcha')) != strtolower($request->custome_recaptcha)) {
            Toastr::error(translate('messages.ReCAPTCHA Failed'));
            return back()->withInput();
        }

        $ip = $request->ip();
        $key = 'login-attempts:' . $ip;
        $maxAttempts = 5;
        $decayMinutes = 2;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $time = $seconds > 60
                ? ceil($seconds / 60) . ' minutes'
                : $seconds . ' seconds';

            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['Too many login attempts. Try again in ' . $time . '.']);
        }

        // Check if user exists and has super-admin role
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            RateLimiter::hit($key, $decayMinutes * 60);
            return redirect()->back()->withInput($request->only('email', 'remember'))
                ->withErrors(['Email does not match.']);
        }

        // Check if user has super-admin role
        if (!$user->hasRole('super-admin')) {
            RateLimiter::hit($key, $decayMinutes * 60);
            return redirect()->back()->withInput($request->only('email', 'remember'))
                ->withErrors(['You do not have permission to access admin panel.']);
        }

        // Attempt authentication
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            RateLimiter::clear($key);
            return redirect()->route('admin.dashboard');
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Password does not match.']);
    }

    public function reloadCaptcha()
    {
        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());

        return response()->json([
            'view' => view('auth.custom-captcha', compact('custome_recaptcha'))->render()
        ], 200);
    }

    public function reset_password(Request $request)
    {
        // Simplified reset password request - just show a message for now
        // You can implement full password reset functionality later
        $user = User::where('email', 'admin@taksh.com')->first();
        
        if ($user) {
            // For now, just redirect back with a message
            Toastr::info(translate('Password reset functionality will be implemented soon.'));
            return redirect()->route('login');
        }
        
        Toastr::error(translate('User not found.'));
        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }
}

