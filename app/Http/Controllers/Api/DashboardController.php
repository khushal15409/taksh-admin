<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\FulfillmentCenter;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get dashboard data
     */
    public function index(Request $request)
    {
        // Get nearest fulfillment center if latitude and longitude provided
        $nearestFulfillmentCenterId = null;
        if ($request->has('latitude') && $request->has('longitude')) {
            $nearestFulfillmentCenterId = $this->getNearestFulfillmentCenter(
                $request->input('latitude'),
                $request->input('longitude')
            );
        }
        // Fetch banners (home_top and home_middle positions)
        $banners = Banner::where('is_active', true)
            ->whereIn('position', ['home_top', 'home_middle'])
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch trending products (limit 10)
        $trendingProductsQuery = Product::with(['brand', 'category', 'variants' => function ($query) {
            $query->where('status', 'active')
                ->orderBy('sale_price', 'asc')
                ->limit(1);
        }, 'images' => function ($query) {
            $query->where('is_primary', true)
                ->limit(1);
        }])
            ->where('status', 'active')
            ->where('is_trending', true);

        // Filter by nearest fulfillment center if provided
        if ($nearestFulfillmentCenterId) {
            $trendingProductsQuery->whereHas('variants.warehouseProducts', function ($query) use ($nearestFulfillmentCenterId) {
                $query->where('warehouse_id', $nearestFulfillmentCenterId)
                    ->where('stock_qty', '>', 0);
            });
        }

        $trendingProducts = $trendingProductsQuery
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $variant = $product->variants->first();
                $image = $product->images->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'short_description' => $product->short_description,
                    'price' => $variant ? (float) ($variant->sale_price ?? $variant->price) : 0,
                    'image_url' => $image ? $image->image_url : null,
                    'brand' => $product->brand ? $product->brand->name : null,
                ];
            });

        // Fetch latest products (limit 10)
        $latestProductsQuery = Product::with(['brand', 'category', 'variants' => function ($query) {
            $query->where('status', 'active')
                ->orderBy('sale_price', 'asc')
                ->limit(1);
        }, 'images' => function ($query) {
            $query->where('is_primary', true)
                ->limit(1);
        }])
            ->where('status', 'active');

        // Filter by nearest fulfillment center if provided
        if ($nearestFulfillmentCenterId) {
            $latestProductsQuery->whereHas('variants.warehouseProducts', function ($query) use ($nearestFulfillmentCenterId) {
                $query->where('warehouse_id', $nearestFulfillmentCenterId)
                    ->where('stock_qty', '>', 0);
            });
        }

        $latestProducts = $latestProductsQuery
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $variant = $product->variants->first();
                $image = $product->images->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'short_description' => $product->short_description,
                    'price' => $variant ? (float) ($variant->sale_price ?? $variant->price) : 0,
                    'image_url' => $image ? $image->image_url : null,
                    'brand' => $product->brand ? $product->brand->name : null,
                ];
            });

        // Fetch categories with images
        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image_url' => $category->image_url,
                    'icon_url' => $category->icon_url,
                ];
            });

        $data = [
            'banners' => $banners,
            'trending_products' => $trendingProducts,
            'latest_products' => $latestProducts,
            'categories' => $categories,
        ];

        return $this->success($data, 'api.dashboard.loaded');
    }

    /**
     * Get nearest fulfillment center using Haversine formula
     * 
     * @param float $latitude
     * @param float $longitude
     * @return int|null
     */
    private function getNearestFulfillmentCenter($latitude, $longitude)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return null;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        // Haversine formula to calculate distance (in km)
        // Radius: 30 km
        $radius = 30;

        $nearestFulfillmentCenter = FulfillmentCenter::select('fulfillment_centers.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance', 'asc')
            ->first();

        return $nearestFulfillmentCenter ? $nearestFulfillmentCenter->id : null;
    }
}
