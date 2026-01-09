<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class EmployeeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        
        return [
            'f_name' => 'required|max:100',
            'l_name' => 'required|max:100',
            'role_id' => 'required|not_in:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|size:10|regex:/^[0-9]{10}$/|unique:users,phone,' . $id,
            'password' => ['nullable', Password::min(8)->mixedCase()->letters()->numbers()->symbols()],
            'pincode' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'f_name.required' => translate('messages.first_name_is_required'),
            'l_name.required' => translate('Last name is required'),
            'role_id.not_in' => translate('messages.unauthorized'),
            'email.unique' => translate('messages.email_already_exists'),
            'phone.unique' => translate('messages.phone_already_exists'),
            'phone.size' => translate('Phone number must be exactly 10 digits'),
            'phone.regex' => translate('Phone number must contain only digits'),
            'pincode.required' => translate('Pincode is required'),
            'pincode.size' => translate('Pincode must be exactly 6 digits'),
            'pincode.regex' => translate('Pincode must contain only digits'),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->all();
        if (!empty($errors)) {
            session()->flash('validation_error', $errors[0]);
        }
        parent::failedValidation($validator);
    }
}

