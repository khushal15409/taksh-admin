<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\State;
use App\Models\City;
use App\Models\FulfillmentCenter;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use App\CentralLogics\ToastrWrapper as Toastr;
use App\CentralLogics\Helpers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants', 'images']);
        
        // Search
        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        $status = $request->input('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        // Filter by category
        $categoryId = $request->input('category_id');
        if (!empty($categoryId) && is_numeric($categoryId) && (int)$categoryId > 0) {
            $query->where('category_id', (int)$categoryId);
        }
        
        // Filter by trending
        $isTrending = $request->input('is_trending');
        if ($isTrending !== null && $isTrending !== '') {
            $query->where('is_trending', (bool)$isTrending);
        }
        
        // Filter by latest
        $isLatest = $request->input('is_latest');
        if ($isLatest !== null && $isLatest !== '') {
            $query->where('is_latest', (bool)$isLatest);
        }
        
        // Filter by express_30
        $isExpress30 = $request->input('is_express_30');
        if ($isExpress30 !== null && $isExpress30 !== '') {
            $query->where('is_express_30', (bool)$isExpress30);
        }
        
        $products = $query->orderBy('created_at', 'desc')->paginate(25);
        $categories = Category::where('status', 'active')->orderBy('name', 'asc')->get();
        
        return view('admin-views.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->orderBy('name', 'asc')->get();
        $brands = Brand::where('status', 'active')->orderBy('name', 'asc')->get();
        $states = State::orderBy('name', 'asc')->get();
        return view('admin-views.products.create', compact('categories', 'brands', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'status' => 'required|in:active,inactive',
                'is_trending' => 'nullable|boolean',
                'is_latest' => 'nullable|boolean',
                'is_express_30' => 'nullable|boolean',
                // Variant fields
                'variant_sku' => 'required|string|max:255',
                'variant_price' => 'required|numeric|min:0',
                'variant_sale_price' => 'nullable|numeric|min:0',
                'variant_status' => 'required|in:active,inactive',
                // Location & Fulfillment Center
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'fulfillment_center_id' => 'required|exists:fulfillment_centers,id',
                'stock_qty' => 'required|integer|min:0',
                // Images
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:15360',
            ], [
                'name.required' => translate('messages.Product name is required!'),
                'category_id.required' => translate('messages.Category is required!'),
                'category_id.exists' => translate('messages.Category not found!'),
                'brand_id.exists' => translate('messages.Brand not found!'),
                'variant_sku.required' => translate('messages.SKU is required!'),
                'variant_price.required' => translate('messages.Price is required!'),
                'variant_price.numeric' => translate('messages.Price must be a number!'),
                'variant_price.min' => translate('messages.Price must be greater than or equal to 0!'),
                'variant_sale_price.numeric' => translate('messages.Sale price must be a number!'),
                'variant_sale_price.min' => translate('messages.Sale price must be greater than or equal to 0!'),
                'state_id.required' => translate('messages.State is required!'),
                'state_id.exists' => translate('messages.State not found!'),
                'city_id.required' => translate('messages.City is required!'),
                'city_id.exists' => translate('messages.City not found!'),
                'fulfillment_center_id.required' => translate('messages.Fulfillment center is required!'),
                'fulfillment_center_id.exists' => translate('messages.Fulfillment center not found!'),
                'stock_qty.required' => translate('messages.Stock quantity is required!'),
                'stock_qty.integer' => translate('messages.Stock quantity must be a number!'),
                'stock_qty.min' => translate('messages.Stock quantity must be greater than or equal to 0!'),
                'images.*.image' => translate('messages.Please upload image files only!'),
                'images.*.mimes' => translate('messages.Image must be jpeg, jpg, png, gif, or webp!'),
                'images.*.max' => translate('messages.Image size should be less than 15MB!'),
            ]);

            // Check if SKU already exists
            if (ProductVariant::where('sku', $request->variant_sku)->exists()) {
                DB::rollBack();
                Toastr::error(translate('messages.SKU already exists!'));
                return back()->withInput();
            }

            // Generate unique slug
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . uniqid() . '-' . $counter;
                $counter++;
            }

            // Create product
            $product = new Product();
            $product->name = $request->name;
            $product->slug = $slug;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id ?? null;
            $product->description = $request->description ?? null;
            $product->short_description = $request->short_description ?? null;
            $product->status = $request->status;
            $product->is_trending = $request->has('is_trending') ? 1 : 0;
            $product->is_latest = $request->has('is_latest') ? 1 : 0;
            $product->is_express_30 = $request->has('is_express_30') ? 1 : 0;
            
            if (!$product->save()) {
                throw new \Exception('Failed to save product');
            }

            // Create default variant
            $variant = new ProductVariant();
            $variant->product_id = $product->id;
            $variant->sku = $request->variant_sku;
            $variant->price = $request->variant_price;
            $variant->sale_price = $request->variant_sale_price ?? null;
            $variant->status = $request->variant_status;
            
            if (!$variant->save()) {
                throw new \Exception('Failed to save product variant');
            }

            // Create warehouse product mapping
            if ($request->fulfillment_center_id && $request->stock_qty !== null) {
                $warehouseProduct = new WarehouseProduct();
                $warehouseProduct->warehouse_id = $request->fulfillment_center_id;
                $warehouseProduct->product_variant_id = $variant->id;
                $warehouseProduct->stock_qty = $request->stock_qty ?? 0;
                $warehouseProduct->reserved_qty = 0;
                
                if (!$warehouseProduct->save()) {
                    throw new \Exception('Failed to save warehouse product mapping');
                }
            }

            // Handle images
            if ($request->hasFile('images')) {
                $sortOrder = 0;
                foreach ($request->file('images') as $image) {
                    if ($image && $image->isValid()) {
                        try {
                            $extension = $image->getClientOriginalExtension();
                            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                            
                            $imageName = Helpers::upload('product/', $format, $image);
                            
                            if ($imageName && $imageName !== 'def.png') {
                                $productImage = new ProductImage();
                                $productImage->product_id = $product->id;
                                $productImage->product_variant_id = $variant->id;
                                $productImage->image_url = $imageName;
                                $productImage->is_primary = ($sortOrder === 0) ? true : false;
                                $productImage->sort_order = $sortOrder;
                                
                                if (!$productImage->save()) {
                                    \Log::warning('Failed to save product image: ' . $imageName);
                                } else {
                                    $sortOrder++;
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Product image upload error: ' . $e->getMessage());
                            // Continue with other images even if one fails
                        }
                    }
                }
            }

            DB::commit();
            
            Toastr::success(translate('messages.product_added'));
            return redirect()->route('admin.products.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(translate('messages.failed_to_add_product') . ': ' . $e->getMessage());
            \Log::error('Product creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['images'])
            ]);
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::with(['category', 'brand', 'variants.warehouseProducts.warehouse', 'images'])->findOrFail($id);
        $categories = Category::where('status', 'active')->orderBy('name', 'asc')->get();
        $brands = Brand::where('status', 'active')->orderBy('name', 'asc')->get();
        $states = State::orderBy('name', 'asc')->get();
        
        // Get warehouse product mapping for the first variant (default variant)
        $warehouseProduct = null;
        $selectedStateId = null;
        $selectedCityId = null;
        $selectedFulfillmentCenterId = null;
        $stockQty = 0;
        
        if ($product->variants->isNotEmpty()) {
            $firstVariant = $product->variants->first();
            $warehouseProduct = WarehouseProduct::where('product_variant_id', $firstVariant->id)
                ->with('warehouse')
                ->first();
            
            if ($warehouseProduct && $warehouseProduct->warehouse) {
                $selectedFulfillmentCenterId = $warehouseProduct->warehouse_id;
                $selectedCityId = $warehouseProduct->warehouse->city_id;
                $selectedStateId = $warehouseProduct->warehouse->state_id;
                $stockQty = $warehouseProduct->stock_qty;
            }
        }
        
        return view('admin-views.products.edit', compact('product', 'categories', 'brands', 'states', 'warehouseProduct', 'selectedStateId', 'selectedCityId', 'selectedFulfillmentCenterId', 'stockQty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'status' => 'required|in:active,inactive',
                'is_trending' => 'nullable|boolean',
                'is_latest' => 'nullable|boolean',
                'is_express_30' => 'nullable|boolean',
                // Location & Fulfillment Center
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'fulfillment_center_id' => 'required|exists:fulfillment_centers,id',
                'stock_qty' => 'required|integer|min:0',
                // Images
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:15360',
            ], [
                'name.required' => translate('messages.Product name is required!'),
                'category_id.required' => translate('messages.Category is required!'),
                'category_id.exists' => translate('messages.Category not found!'),
                'brand_id.exists' => translate('messages.Brand not found!'),
                'state_id.required' => translate('messages.State is required!'),
                'state_id.exists' => translate('messages.State not found!'),
                'city_id.required' => translate('messages.City is required!'),
                'city_id.exists' => translate('messages.City not found!'),
                'fulfillment_center_id.required' => translate('messages.Fulfillment center is required!'),
                'fulfillment_center_id.exists' => translate('messages.Fulfillment center not found!'),
                'stock_qty.required' => translate('messages.Stock quantity is required!'),
                'stock_qty.integer' => translate('messages.Stock quantity must be a number!'),
                'stock_qty.min' => translate('messages.Stock quantity must be greater than or equal to 0!'),
                'images.*.image' => translate('messages.Please upload image files only!'),
                'images.*.mimes' => translate('messages.Image must be jpeg, jpg, png, gif, or webp!'),
                'images.*.max' => translate('messages.Image size should be less than 15MB!'),
            ]);

            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id ?? null;
            $product->description = $request->description ?? null;
            $product->short_description = $request->short_description ?? null;
            $product->status = $request->status;
            $product->is_trending = $request->has('is_trending') ? 1 : 0;
            $product->is_latest = $request->has('is_latest') ? 1 : 0;
            $product->is_express_30 = $request->has('is_express_30') ? 1 : 0;
            
            if (!$product->save()) {
                throw new \Exception('Failed to update product');
            }

            // Update or create warehouse product mapping for the first variant
            $firstVariant = $product->variants->first();
            if ($firstVariant && $request->fulfillment_center_id && $request->stock_qty !== null) {
                $warehouseProduct = WarehouseProduct::where('product_variant_id', $firstVariant->id)->first();
                
                if ($warehouseProduct) {
                    // Update existing mapping
                    $warehouseProduct->warehouse_id = $request->fulfillment_center_id;
                    $warehouseProduct->stock_qty = $request->stock_qty ?? 0;
                    // Keep reserved_qty as is
                    if (!$warehouseProduct->save()) {
                        throw new \Exception('Failed to update warehouse product mapping');
                    }
                } else {
                    // Create new mapping
                    $warehouseProduct = new WarehouseProduct();
                    $warehouseProduct->warehouse_id = $request->fulfillment_center_id;
                    $warehouseProduct->product_variant_id = $firstVariant->id;
                    $warehouseProduct->stock_qty = $request->stock_qty ?? 0;
                    $warehouseProduct->reserved_qty = 0;
                    
                    if (!$warehouseProduct->save()) {
                        throw new \Exception('Failed to create warehouse product mapping');
                    }
                }
            }

            // Handle new images
            if ($request->hasFile('images')) {
                $existingImages = ProductImage::where('product_id', $product->id)->count();
                $sortOrder = $existingImages;
                
                foreach ($request->file('images') as $image) {
                    if ($image && $image->isValid()) {
                        try {
                            $extension = $image->getClientOriginalExtension();
                            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                            
                            $imageName = Helpers::upload('product/', $format, $image);
                            
                            if ($imageName && $imageName !== 'def.png') {
                                $variant = $product->variants->first();
                                $productImage = new ProductImage();
                                $productImage->product_id = $product->id;
                                $productImage->product_variant_id = $variant ? $variant->id : null;
                                $productImage->image_url = $imageName;
                                $productImage->is_primary = false;
                                $productImage->sort_order = $sortOrder;
                                $productImage->save();
                                $sortOrder++;
                            }
                        } catch (\Exception $e) {
                            \Log::error('Product image upload error: ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();
            
            Toastr::success(translate('messages.product_updated'));
            return redirect()->route('admin.products.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(translate('messages.failed_to_update_product') . ': ' . $e->getMessage());
            \Log::error('Product update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['images'])
            ]);
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete product images
            foreach ($product->images as $image) {
                if ($image->image_url) {
                    try {
                        $storageType = Helpers::getDisk();
                        if ($storageType === 's3') {
                            \Storage::disk('s3')->delete('product/' . $image->image_url);
                        } else {
                            \Storage::disk('public')->delete('product/' . $image->image_url);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete product image: ' . $e->getMessage());
                    }
                }
                $image->delete();
            }
            
            // Delete variants (this will cascade delete related data)
            $product->variants()->delete();
            
            $product->delete();
            
            Toastr::success(translate('messages.product_deleted'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_delete_product') . ': ' . $e->getMessage());
            \Log::error('Product deletion error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Toggle product status
     */
    public function statusToggle(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $product->status = $request->status;
            $product->save();
            Toastr::success(translate('messages.product_status_updated'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_status') . ': ' . $e->getMessage());
            \Log::error('Product status update error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Toggle product flag (trending, latest, express_30)
     */
    public function flagToggle(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $flag = $request->flag; // is_trending, is_latest, is_express_30
            
            if (!in_array($flag, ['is_trending', 'is_latest', 'is_express_30'])) {
                Toastr::error(translate('messages.Invalid flag!'));
                return back();
            }
            
            $product->$flag = $request->value;
            $product->save();
            
            Toastr::success(translate('messages.product_flag_updated'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_flag') . ': ' . $e->getMessage());
            \Log::error('Product flag update error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Delete product image
     */
    public function deleteImage($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            
            // Delete file
            if ($image->image_url) {
                try {
                    $storageType = Helpers::getDisk();
                    if ($storageType === 's3') {
                        \Storage::disk('s3')->delete('product/' . $image->image_url);
                    } else {
                        \Storage::disk('public')->delete('product/' . $image->image_url);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete product image file: ' . $e->getMessage());
                }
            }
            
            $image->delete();
            
            Toastr::success(translate('messages.image_deleted'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_delete_image') . ': ' . $e->getMessage());
            \Log::error('Image deletion error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Get cities by state (AJAX)
     */
    public function getCities(Request $request)
    {
        $stateId = $request->input('state_id');
        
        if (!$stateId) {
            return response()->json(['cities' => []]);
        }

        $cities = City::where('state_id', $stateId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json(['cities' => $cities]);
    }

    /**
     * Get fulfillment centers by city (AJAX)
     */
    public function getFulfillmentCenters(Request $request)
    {
        $cityId = $request->input('city_id');
        
        if (!$cityId) {
            return response()->json(['fulfillment_centers' => []]);
        }

        $fulfillmentCenters = FulfillmentCenter::where('city_id', $cityId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json(['fulfillment_centers' => $fulfillmentCenters]);
    }
}

