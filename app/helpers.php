<?php

if (! function_exists('translate')) {
    function translate($key, $replace = [])
    {
        // Simple translate function - returns the key with underscores replaced by spaces
        // You can enhance this later with actual translation files
        $key = strpos($key, 'messages.') === 0 ? substr($key, 9) : $key;
        $processed_key = ucfirst(str_replace('_', ' ', $key));
        
        // Try to use Laravel's trans function if translation exists
        try {
            $result = trans('messages.' . $key, $replace);
            if ($result !== 'messages.' . $key) {
                return $result;
            }
        } catch (\Exception $exception) {
            // Fall through to processed key
        }
        
        return $processed_key;
    }
}

if (!function_exists('addon_published_status')) {
    function addon_published_status($module_name)
    {
        // Simple function - return 0 for now, can be enhanced later
        return 0;
    }
}

