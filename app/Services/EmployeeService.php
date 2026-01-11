<?php

namespace App\Services;

use App\Models\Pincode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeService
{
    public function getAddData(object $request): array
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = $this->uploadImage($request->file('image'));
            } catch (\Exception $e) {
                \Log::error('Image upload failed', ['error' => $e->getMessage()]);
                throw new \Exception(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
            }
        }

        // Find pincode by pincode value
        $pincodeId = null;
        if ($request->has('pincode') && !empty($request->pincode)) {
            $pincode = Pincode::where('pincode', $request->pincode)
                ->where('status', 1)
                ->first();
            if ($pincode) {
                $pincodeId = $pincode->id;
            }
        }

        return [
            'name' => trim(($request->f_name ?? '') . ' ' . ($request->l_name ?? '')),
            'f_name' => $request->f_name,
            'l_name' => $request->l_name ?? '',
            'mobile' => $request->phone, // Use phone as mobile for users table
            'phone' => $request->phone,
            'pincode_id' => $pincodeId,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'image' => $imagePath,
            'user_type' => 'employee', // Mark as employee
            'is_verified' => true,
            'is_active' => true,
            'status' => 'active',
        ];
    }

    public function getUpdateData(object $request, object $employee): array
    {
        // Find pincode by pincode value
        $pincodeId = $employee->pincode_id; // Keep existing if not changed
        if ($request->has('pincode') && !empty($request->pincode)) {
            $pincode = Pincode::where('pincode', $request->pincode)
                ->where('status', 1)
                ->first();
            if ($pincode) {
                $pincodeId = $pincode->id;
            } else {
                $pincodeId = null; // Clear if pincode not found
            }
        }

        $data = [
            'name' => trim(($request->f_name ?? '') . ' ' . ($request->l_name ?? '')),
            'f_name' => $request->f_name,
            'l_name' => $request->l_name ?? '',
            'mobile' => $request->phone, // Use phone as mobile for users table
            'phone' => $request->phone,
            'pincode_id' => $pincodeId,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'updated_at' => now(),
            'is_logged_in' => 0,
        ];

        // Handle password update
        if ($request->has('password') && !empty($request->password)) {
            $data['password'] = bcrypt($request->password);
            $employee->remember_token = null;
        } else {
            $data['password'] = $employee->password;
        }

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image
            if ($employee->image) {
                $this->deleteImage($employee->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        } else {
            $data['image'] = $employee->image;
        }

        return $data;
    }

    public function adminCheck(object $employee): array
    {
        // Check if current user is not the employee being edited (allow editing others)
        if (auth()->check() && auth()->id() != $employee->id) {
            return ['flag' => 'authorized'];
        }
        // If trying to edit self, still allow but show warning
        return ['flag' => 'authorized'];
    }

    private function uploadImage($file): string
    {
        $filename = 'employee_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('admin', $filename, 'public');
        return basename($path);
    }

    private function deleteImage($imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists('admin/' . $imagePath)) {
            Storage::disk('public')->delete('admin/' . $imagePath);
        }
    }
}

