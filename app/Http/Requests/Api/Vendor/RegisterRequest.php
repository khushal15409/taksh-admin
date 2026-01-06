<?php

namespace App\Http\Requests\Api\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Category;

class RegisterRequest extends FormRequest
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
            // Shop Details (Required for new registration)
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string',
            'shop_pincode' => 'required|string|max:10',
            'shop_latitude' => 'nullable|numeric|between:-90,90',
            'shop_longitude' => 'nullable|numeric|between:-180,180',
            'category_id' => 'nullable|string', // Accept comma-separated category IDs as string
            'shop_images' => 'nullable|array',
            'shop_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:15360', // 15MB max
            
            // Owner Details (Required for new registration)
            'owner_name' => 'required|string|max:255',
            'owner_address' => 'required|string',
            'owner_pincode' => 'required|string|max:10',
            'owner_latitude' => 'nullable|numeric|between:-90,90',
            'owner_longitude' => 'nullable|numeric|between:-180,180',
            'owner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            
            // Contact (Required)
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/|unique:users,mobile',
            'email' => 'required|email|max:255|unique:vendors,email',
            
            // Documents (Required)
            'aadhaar_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:15360',
            'aadhaar_number' => 'required|string|max:12',
            'pan_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:15360',
            'pan_number' => 'required|string|max:10',
            'bank_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:15360',
            'bank_account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:11',
            
            // Documents (Conditional - GST OR Non-GST required, not both)
            'gst_file' => 'required_without:non_gst_file|nullable|file|mimes:jpeg,png,jpg,pdf|max:15360',
            'gst_number' => 'required_with:gst_file|nullable|string|max:15',
            'non_gst_file' => 'required_without:gst_file|nullable|file|mimes:jpeg,png,jpg,pdf|max:15360',
            
            // Documents (Optional)
            'msme_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:15360',
            'fssai_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:15360',
            'shop_agreement_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:15360',
            
            // Backward compatibility fields (optional - for old API calls)
            'vendor_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'account_number' => 'nullable|string|max:50',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate comma-separated category IDs
            if ($this->has('category_id') && !empty($this->category_id)) {
                $categoryIds = array_filter(array_map('trim', explode(',', $this->category_id)));
                
                if (!empty($categoryIds)) {
                    // Check if all category IDs exist
                    $existingCategories = Category::whereIn('id', $categoryIds)->pluck('id')->toArray();
                    $invalidIds = array_diff($categoryIds, $existingCategories);
                    
                    if (!empty($invalidIds)) {
                        $validator->errors()->add(
                            'category_id',
                            'The following category IDs do not exist: ' . implode(', ', $invalidIds)
                        );
                    }
                }
            }
        });
    }
}
