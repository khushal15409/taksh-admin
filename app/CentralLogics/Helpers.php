<?php

namespace App\CentralLogics;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Storage;

class Helpers
{
    public static function get_full_url($path, $data, $type, $placeholder = null)
    {
        $place_holders = [
            'default' => asset('assets/admin/img/100x100/2.jpg'),
            'business' => asset('assets/admin/img/160x160/img2.jpg'),
            'contact_us_image' => asset('assets/admin/img/160x160/img2.jpg'),
            'profile' => asset('assets/admin/img/160x160/img2.jpg'),
            'product' => asset('assets/admin/img/160x160/img2.jpg'),
            'order' => asset('assets/admin/img/160x160/img2.jpg'),
            'refund' => asset('assets/admin/img/160x160/img2.jpg'),
            'delivery-man' => asset('assets/admin/img/160x160/img2.jpg'),
            'admin' => asset('assets/admin/img/160x160/img1.jpg'),
            'conversation' => asset('assets/admin/img/160x160/img1.jpg'),
            'banner' => asset('assets/admin/img/900x400/img1.jpg'),
            'campaign' => asset('assets/admin/img/900x400/img1.jpg'),
            'notification' => asset('assets/admin/img/900x400/img1.jpg'),
            'category' => asset('assets/admin/img/100x100/2.jpg'),
            'store' => asset('assets/admin/img/160x160/img1.jpg'),
            'vendor' => asset('assets/admin/img/160x160/img1.jpg'),
            'brand' => asset('assets/admin/img/100x100/2.jpg'),
            'upload_image' => asset('assets/admin/img/upload-img.png'),
            'store/cover' => asset('assets/admin/img/100x100/2.jpg'),
            'upload_image_4' => asset('assets/admin/img/upload-4.png'),
            'promotional_banner' => asset('assets/admin/img/100x100/2.jpg'),
            'admin_feature' => asset('assets/admin/img/100x100/2.jpg'),
            'aspect_1' => asset('assets/admin/img/aspect-1.png'),
            'special_criteria' => asset('assets/admin/img/100x100/2.jpg'),
            'download_user_app_image' => asset('assets/admin/img/100x100/2.jpg'),
            'reviewer_image' => asset('assets/admin/img/100x100/2.jpg'),
            'fixed_header_image' => asset('assets/admin/img/aspect-1.png'),
            'header_icon' => asset('assets/admin/img/aspect-1.png'),
            'available_zone_image' => asset('assets/admin/img/100x100/2.jpg'),
            'why_choose' => asset('assets/admin/img/aspect-1.png'),
            'header_banner' => asset('assets/admin/img/aspect-1.png'),
            'reviewer_company_image' => asset('assets/admin/img/100x100/2.jpg'),
            'module' => asset('assets/admin/img/100x100/2.jpg'),
            'parcel_category' => asset('assets/admin/img/400x400/img2.jpg'),
            'favicon' => asset('assets/admin/img/favicon.png'),
            'seller' => asset('assets/back-end/img/160x160/img1.jpg'),
            'upload_placeholder' => asset('assets/admin/img/upload-placeholder.png'),
            'payment_modules/gateway_image' => asset('assets/admin/img/payment/placeholder.png'),
            'email_template' => asset('assets/admin/img/blank1.png'),
            'warehouse' => asset('assets/admin/img/160x160/img2.jpg'),
            'miniwarehouse' => asset('assets/admin/img/160x160/img2.jpg'),
        ];

        try {
            if ($data && $type == 's3' && Storage::disk('s3')->exists($path . '/' . $data)) {
                return Storage::disk('s3')->url($path . '/' . $data);
            }
        } catch (\Exception $e) {
            // Ignore S3 errors
        }

        if ($data && Storage::disk('public')->exists($path . '/' . $data)) {
            return asset('storage') . '/' . $path . '/' . $data;
        }

        if (request()->is('api/*')) {
            return null;
        }

        if (isset($placeholder) && array_key_exists($placeholder, $place_holders)) {
            return $place_holders[$placeholder];
        } elseif (array_key_exists($path, $place_holders)) {
            return $place_holders[$path];
        } else {
            return $place_holders['default'];
        }

        return 'def.png';
    }

    public static function get_mail_status($name)
    {
        $status = BusinessSetting::where('key', $name)->first()?->value ?? 0;
        return $status;
    }

    public static function module_permission_check($module_name)
    {
        // Simplified version - return true for now
        // You can implement actual permission checking later
        return true;
    }

    public static function get_business_settings($name)
    {
        try {
            $config = BusinessSetting::where('key', $name)->first();
            return $config ? $config->value : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getDisk()
    {
        $config = self::get_business_settings('local_storage');
        return isset($config) && $config == 0 ? 's3' : 'public';
    }

    public static function upload(string $dir, string $format, $image = null)
    {
        try {
            if ($image != null) {
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
                if (!Storage::disk(self::getDisk())->exists($dir)) {
                    Storage::disk(self::getDisk())->makeDirectory($dir);
                }
                Storage::disk(self::getDisk())->putFileAs($dir, $image, $imageName);
            } else {
                $imageName = 'def.png';
            }
        } catch (\Exception $e) {
            $imageName = 'def.png';
        }
        return $imageName;
    }
}

