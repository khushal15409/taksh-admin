<?php

namespace App\Http\Requests\Api\Salesman;

use Illuminate\Foundation\Http\FormRequest;

class VerifyVendorRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shop_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:15360', // 15MB max
            'license_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'remarks' => 'nullable|string|max:1000',
        ];
    }
}
