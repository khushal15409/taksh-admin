<?php

namespace App\CentralLogics;

/**
 * Toastr Wrapper - Provides Toastr functionality even if package is not installed
 * Uses session flash messages as fallback
 */
class ToastrWrapper
{
    public static function success($message)
    {
        if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
            return \Brian2694\Toastr\Facades\Toastr::success($message);
        }
        session()->flash('success', $message);
    }

    public static function error($message)
    {
        if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
            return \Brian2694\Toastr\Facades\Toastr::error($message);
        }
        session()->flash('error', $message);
    }

    public static function info($message)
    {
        if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
            return \Brian2694\Toastr\Facades\Toastr::info($message);
        }
        session()->flash('info', $message);
    }

    public static function warning($message)
    {
        if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
            return \Brian2694\Toastr\Facades\Toastr::warning($message);
        }
        session()->flash('warning', $message);
    }
}

