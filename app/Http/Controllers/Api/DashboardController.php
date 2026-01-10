<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\FulfillmentCenter;
use App\Models\AppDashboardSection;
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
        $nearestFulfillmentCenter = null;
        if ($request->has('latitude') && $request->has('longitude')) {
            $nearestFulfillmentCenter = $this->getNearestFulfillmentCenter(
                $request->input('latitude'),
                $request->input('longitude')
            );
        }

        // Get active dashboard sections
        $activeSections = AppDashboardSection::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('section_key')
            ->toArray();

        $data = [];

        // Build response conditionally based on active sections
        if (in_array('banners', $activeSections)) {
            $data['banners'] = $this->getBanners();
        }

        if (in_array('trending_products', $activeSections)) {
            $data['trending_products'] = $this->getTrendingProducts($nearestFulfillmentCenter);
        }

        if (in_array('latest_products', $activeSections)) {
            $data['latest_products'] = $this->getLatestProducts($nearestFulfillmentCenter);
        }

        if (in_array('categories', $activeSections)) {
            $data['categories'] = $this->getCategories();
        }

        if (in_array('express_30_products', $activeSections)) {
            $data['express_30_products'] = $this->getExpress30Products($nearestFulfillmentCenter);
        }

        // If no fulfillment center found and location was provided, add message
        if ($request->has('latitude') && $request->has('longitude') && !$nearestFulfillmentCenter) {
            $data['message'] = 'No fulfillment center found nearby. Showing all available products.';
        }

        return $this->success($data, 'api.dashboard.loaded');
    }

    /**
     * Get banners
     */
    private function getBanners()
    {
        return Banner::where('is_active', true)
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
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'image_url' => $banner->image_url,
                    'position' => $banner->position,
                    'redirect_type' => $banner->redirect_type,
                    'redirect_id' => $banner->redirect_id,
                ];
            });
    }

    /**
     * Get trending products
     */
    private function getTrendingProducts($fulfillmentCenter)
    {
        $query = Product::with(['brand', 'category', 'variants' => function ($query) {
            $query->where('status', 'active')
                ->orderBy('sale_price', 'asc')
                ->limit(1);
        }, 'images' => function ($query) {
            $query->where('is_primary', true)
                ->limit(1);
        }])
            ->where('status', 'active')
            ->where('is_trending', true);

        // Filter by fulfillment center if provided
        if ($fulfillmentCenter) {
            $query->where('fulfillment_center_id', $fulfillmentCenter->id);
        }

        return $query->orderBy('created_at', 'desc')
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
    }

    /**
     * Get latest products
     */
    private function getLatestProducts($fulfillmentCenter)
    {
        $query = Product::with(['brand', 'category', 'variants' => function ($query) {
            $query->where('status', 'active')
                ->orderBy('sale_price', 'asc')
                ->limit(1);
        }, 'images' => function ($query) {
            $query->where('is_primary', true)
                ->limit(1);
        }])
            ->where('status', 'active')
            ->where('is_latest', true);

        // Filter by fulfillment center if provided
        if ($fulfillmentCenter) {
            $query->where('fulfillment_center_id', $fulfillmentCenter->id);
        }

        return $query->orderBy('created_at', 'desc')
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
    }

    /**
     * Get categories with image collage
     */
    private function getCategories()
    {
        return Category::where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($category) {
                // Get category image or generate collage from product images
                $imageUrl = $category->image_url;
                
                // If no category image, try to get first product image as collage
                if (!$imageUrl) {
                    $firstProduct = $category->products()
                        ->where('status', 'active')
                        ->with(['images' => function ($query) {
                            $query->where('is_primary', true)->limit(1);
                        }])
                        ->first();
                    
                    if ($firstProduct && $firstProduct->images->first()) {
                        $imageUrl = $firstProduct->images->first()->image_url;
                    }
                }

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image_url' => $imageUrl,
                    'icon_url' => $category->icon_url,
                ];
            });
    }

    /**
     * Get express 30 products
     */
    private function getExpress30Products($fulfillmentCenter)
    {
        // Express 30 products must come from fulfillment centers that support it
        $query = Product::with(['brand', 'category', 'variants' => function ($query) {
            $query->where('status', 'active')
                ->orderBy('sale_price', 'asc')
                ->limit(1);
        }, 'images' => function ($query) {
            $query->where('is_primary', true)
                ->limit(1);
        }])
            ->where('status', 'active')
            ->where('is_express_30', true);

        // Filter by fulfillment center if provided
        if ($fulfillmentCenter) {
            // Only show express 30 products from centers that support it
            if ($fulfillmentCenter->supports_express_30) {
                $query->where('fulfillment_center_id', $fulfillmentCenter->id);
            } else {
                // Return empty if nearest center doesn't support express 30
                return collect([]);
            }
        } else {
            // If no center provided, only show products from centers that support express 30
            $query->whereHas('fulfillmentCenter', function ($q) {
                $q->where('supports_express_30', true)
                    ->where('status', 'active');
            });
        }

        return $query->orderBy('created_at', 'desc')
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
    }

    /**
     * Get nearest fulfillment center using Haversine formula
     * 
     * @param float $latitude
     * @param float $longitude
     * @return FulfillmentCenter|null
     */
    private function getNearestFulfillmentCenter($latitude, $longitude)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return null;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        // Radius: 15 km (configurable)
        $radius = config('app.fulfillment_center_radius', 15);

        return FulfillmentCenter::nearest($latitude, $longitude, $radius)->first();
    }
}
