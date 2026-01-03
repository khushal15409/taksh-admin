<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BannerController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get banners list
     */
    public function index(Request $request)
    {
        $query = Banner::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            });

        // Filter by position if provided
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        // Order by sort_order ASC, then by latest first
        $banners = $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($banners->isEmpty()) {
            return $this->success([], 'api.banners.not_found');
        }

        return $this->success($banners, 'api.banners.fetched');
    }
}
