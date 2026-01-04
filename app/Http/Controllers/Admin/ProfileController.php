<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\CentralLogics\Helpers;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin-views.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:users,mobile,' . $user->id,
        ], [
            'name.required' => translate('Name is required'),
            'email.required' => translate('Email is required'),
            'email.email' => translate('Please enter a valid email address'),
            'email.unique' => translate('This email is already taken'),
            'mobile.unique' => translate('This mobile number is already taken'),
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('mobile')) {
            $user->mobile = $request->mobile;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $format = $image->getClientOriginalExtension();
            $user->image = Helpers::upload('admin/', $format, $image);
        }

        $user->save();

        Toastr::success(translate('Profile updated successfully'));
        return back();
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => translate('Current password is required'),
            'password.required' => translate('New password is required'),
            'password.confirmed' => translate('Password confirmation does not match'),
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            Toastr::error(translate('Current password is incorrect'));
            return back();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Toastr::success(translate('Password updated successfully'));
        return back();
    }
}

