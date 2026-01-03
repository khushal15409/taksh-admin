<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all categories
     */
    public function categories()
    {
        $categories = Category::where('status', 'active')
            ->with(['children' => function ($query) {
                $query->where('status', 'active');
            }])
            ->whereNull('parent_id')
            ->get();

        return $this->success($categories, 'api.categories_fetched');
    }

    /**
     * Get products list with filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'variants', 'images'])
            ->where('status', 'active');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price filter
        if ($request->has('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->has('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        $perPage = $request->get('limit', 15);
        $products = $query->paginate($perPage);

        return $this->success($products, 'api.products_fetched');
    }

    /**
     * Get product details
     */
    public function show($id)
    {
        $product = Product::with([
            'brand',
            'category',
            'variants' => function ($query) {
                $query->where('status', 'active')
                    ->with(['variantAttributes.attribute', 'variantAttributes.attributeValue', 'images']);
            },
            'images' => function ($query) {
                $query->orderBy('sort_order')->orderBy('is_primary', 'desc');
            },
        ])
            ->where('status', 'active')
            ->find($id);

        if (!$product) {
            return $this->error('api.product_not_found', 404);
        }

        return $this->success($product, 'api.products_fetched');
    }
}

