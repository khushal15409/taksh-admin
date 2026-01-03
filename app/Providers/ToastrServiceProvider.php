<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Simple Toastr wrapper if package is not installed
if (!class_exists('\Brian2694\Toastr\Facades\Toastr')) {
    class Toastr
    {
        public static function success($message)
        {
            session()->flash('success', $message);
        }

        public static function error($message)
        {
            session()->flash('error', $message);
        }

        public static function info($message)
        {
            session()->flash('info', $message);
        }

        public static function warning($message)
        {
            session()->flash('warning', $message);
        }
    }
}

