<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\CentralLogics\ToastrWrapper as Toastr;
use App\CentralLogics\Helpers;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Search
        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        $status = $request->input('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        // Filter by parent
        $parentId = $request->input('parent_id');
        if ($parentId !== null && $parentId !== '') {
            if ($parentId === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', (int)$parentId);
            }
        }
        
        $categories = $query->with('parent')->orderBy('name', 'asc')->paginate(25);
        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name', 'asc')->get();
        
        return view('admin-views.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->where('status', 'active')->orderBy('name', 'asc')->get();
        return view('admin-views.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'icon' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'status' => 'required|in:active,inactive',
            ], [
                'name.required' => translate('messages.Category name is required!'),
                'parent_id.exists' => translate('messages.Parent category not found!'),
                'image.image' => translate('messages.Please upload image files only!'),
                'image.mimes' => translate('messages.Image must be jpeg, jpg, png, gif, or webp!'),
                'image.max' => translate('messages.Image size should be less than 2MB!'),
                'icon.image' => translate('messages.Please upload image files only!'),
                'icon.mimes' => translate('messages.Icon must be jpeg, jpg, png, gif, or webp!'),
                'icon.max' => translate('messages.Icon size should be less than 2MB!'),
                'status.required' => translate('messages.Status is required!'),
                'status.in' => translate('messages.Invalid status!'),
            ]);

            $category = new Category();
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            $category->parent_id = $request->parent_id ?? null;
            $category->status = $request->status;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    try {
                        $extension = $image->getClientOriginalExtension();
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                        
                        $imageName = Helpers::upload('category/', $format, $image);
                        
                        if ($imageName && $imageName !== 'def.png') {
                            $category->image_url = $imageName;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Category image upload error: ' . $e->getMessage());
                        Toastr::error(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
                        return back()->withInput();
                    }
                }
            }

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                if ($icon && $icon->isValid()) {
                    try {
                        $extension = $icon->getClientOriginalExtension();
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                        
                        $iconName = Helpers::upload('category/', $format, $icon);
                        
                        if ($iconName && $iconName !== 'def.png') {
                            $category->icon_url = $iconName;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Category icon upload error: ' . $e->getMessage());
                        Toastr::error(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
                        return back()->withInput();
                    }
                }
            }

            $category->save();

            Toastr::success(translate('messages.category_added'));
            return redirect()->route('admin.categories.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_add_category') . ': ' . $e->getMessage());
            \Log::error('Category creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 'active')
            ->where('id', '!=', $id)
            ->orderBy('name', 'asc')
            ->get();
        return view('admin-views.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'icon' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'status' => 'required|in:active,inactive',
            ], [
                'name.required' => translate('messages.Category name is required!'),
                'parent_id.exists' => translate('messages.Parent category not found!'),
                'parent_id.not_in' => translate('messages.Category cannot be its own parent!'),
                'image.image' => translate('messages.Please upload image files only!'),
                'image.mimes' => translate('messages.Image must be jpeg, jpg, png, gif, or webp!'),
                'image.max' => translate('messages.Image size should be less than 2MB!'),
                'icon.image' => translate('messages.Please upload image files only!'),
                'icon.mimes' => translate('messages.Icon must be jpeg, jpg, png, gif, or webp!'),
                'icon.max' => translate('messages.Icon size should be less than 2MB!'),
                'status.required' => translate('messages.Status is required!'),
                'status.in' => translate('messages.Invalid status!'),
            ]);

            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            $category->parent_id = $request->parent_id ?? null;
            $category->status = $request->status;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    try {
                        $extension = $image->getClientOriginalExtension();
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                        
                        $imageName = Helpers::upload('category/', $format, $image);
                        
                        if ($imageName && $imageName !== 'def.png') {
                            // Delete old image if exists
                            if ($category->image_url) {
                                try {
                                    $storageType = Helpers::getDisk();
                                    if ($storageType === 's3') {
                                        \Storage::disk('s3')->delete('category/' . $category->image_url);
                                    } else {
                                        \Storage::disk('public')->delete('category/' . $category->image_url);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete old category image: ' . $e->getMessage());
                                }
                            }
                            $category->image_url = $imageName;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Category image upload error: ' . $e->getMessage());
                        Toastr::error(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
                        return back()->withInput();
                    }
                }
            }

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                if ($icon && $icon->isValid()) {
                    try {
                        $extension = $icon->getClientOriginalExtension();
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                        
                        $iconName = Helpers::upload('category/', $format, $icon);
                        
                        if ($iconName && $iconName !== 'def.png') {
                            // Delete old icon if exists
                            if ($category->icon_url) {
                                try {
                                    $storageType = Helpers::getDisk();
                                    if ($storageType === 's3') {
                                        \Storage::disk('s3')->delete('category/' . $category->icon_url);
                                    } else {
                                        \Storage::disk('public')->delete('category/' . $category->icon_url);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete old category icon: ' . $e->getMessage());
                                }
                            }
                            $category->icon_url = $iconName;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Category icon upload error: ' . $e->getMessage());
                        Toastr::error(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
                        return back()->withInput();
                    }
                }
            }

            $category->save();

            Toastr::success(translate('messages.category_updated'));
            return redirect()->route('admin.categories.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_category') . ': ' . $e->getMessage());
            \Log::error('Category update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
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
            $category = Category::findOrFail($id);
            
            // Check if category has products
            if ($category->products()->count() > 0) {
                Toastr::error(translate('messages.Cannot delete category with products!'));
                return back();
            }
            
            // Check if category has children
            if ($category->children()->count() > 0) {
                Toastr::error(translate('messages.Cannot delete category with subcategories!'));
                return back();
            }
            
            // Delete images
            if ($category->image_url) {
                try {
                    $storageType = Helpers::getDisk();
                    if ($storageType === 's3') {
                        \Storage::disk('s3')->delete('category/' . $category->image_url);
                    } else {
                        \Storage::disk('public')->delete('category/' . $category->image_url);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete category image: ' . $e->getMessage());
                }
            }
            
            if ($category->icon_url) {
                try {
                    $storageType = Helpers::getDisk();
                    if ($storageType === 's3') {
                        \Storage::disk('s3')->delete('category/' . $category->icon_url);
                    } else {
                        \Storage::disk('public')->delete('category/' . $category->icon_url);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete category icon: ' . $e->getMessage());
                }
            }
            
            $category->delete();
            
            Toastr::success(translate('messages.category_deleted'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_delete_category') . ': ' . $e->getMessage());
            \Log::error('Category deletion error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Toggle category status
     */
    public function statusToggle(Request $request)
    {
        try {
            $category = Category::findOrFail($request->id);
            $category->status = $request->status;
            $category->save();
            Toastr::success(translate('messages.category_status_updated'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_status') . ': ' . $e->getMessage());
            \Log::error('Category status update error: ' . $e->getMessage());
            return back();
        }
    }
}

