<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomRoleAddRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.0' => 'required|string|max:191',
            'modules'=>'required|array|min:1',
            'modules.*' => 'required|string|distinct'
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check uniqueness of role name using Spatie Role model
            if ($this->has('name.0')) {
                $name = $this->input('name.0');
                $exists = \Spatie\Permission\Models\Role::where('name', $name)
                    ->where('guard_name', 'web')
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('name.0', translate('messages.Role name already exists!'));
                }
            }
            
            // Ensure modules array has at least one non-empty value
            if ($this->has('modules')) {
                $modules = array_filter($this->input('modules', []), function($module) {
                    return !empty($module) && trim($module) !== '';
                });
                if (empty($modules)) {
                    $validator->errors()->add('modules', translate('messages.Please select atleast one module'));
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.0.required'=>translate('default_data_is_required'),
            'name.required'=>translate('messages.Role name is required!'),
            'modules.required'=>translate('messages.Please select atleast one module')
        ];
    }
    
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // Store only the first validation error in session for Toastr display (single Toastr)
        $errors = $validator->errors()->all();
        if (!empty($errors)) {
            session()->flash('validation_error', $errors[0]); // Store only first error
        }
        
        // Continue with default Laravel validation behavior
        parent::failedValidation($validator);
    }
}

