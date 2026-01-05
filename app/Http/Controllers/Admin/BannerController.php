<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\CentralLogics\ToastrWrapper as Toastr;
use App\CentralLogics\Helpers;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $banners = Banner::latest()->paginate(25);
        
        return view('admin-views.banner.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin-views.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'redirect_type' => 'required|in:product,category,external,none',
                'redirect_id' => 'nullable|required_if:redirect_type,product,category|integer',
                'redirect_url' => 'nullable|required_if:redirect_type,external|url|max:500',
                'position' => 'required|in:home_top,home_middle,home_bottom,dashboard',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ], [
                'title.required' => translate('messages.Title is required!'),
                'image.required' => translate('messages.Image is required!'),
                'image.image' => translate('messages.Please upload image files only!'),
                'image.mimes' => translate('messages.Image must be jpeg, jpg, png, gif, or webp!'),
                'image.max' => translate('messages.Image size should be less than 2MB!'),
                'redirect_type.required' => translate('messages.Redirect type is required!'),
                'redirect_type.in' => translate('messages.Invalid redirect type!'),
                'redirect_id.required_if' => translate('messages.Redirect ID is required!'),
                'redirect_url.required_if' => translate('messages.Redirect URL is required!'),
                'redirect_url.url' => translate('messages.Please provide a valid URL!'),
                'position.required' => translate('messages.Position is required!'),
                'position.in' => translate('messages.Invalid position!'),
                'end_date.after_or_equal' => translate('messages.End date must be after or equal to start date!'),
            ]);

            $banner = new Banner();
            $banner->title = $request->title;
            $banner->description = $request->description ?? null;
            $banner->redirect_type = $request->redirect_type;
            $banner->redirect_id = $request->redirect_id ?? null;
            $banner->redirect_url = $request->redirect_url ?? null;
            $banner->position = $request->position;
            $banner->start_date = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null;
            $banner->end_date = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null;
            $banner->sort_order = $request->sort_order ?? 0;
            $banner->is_active = $request->has('is_active') ? 1 : 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    try {
                        $extension = $image->getClientOriginalExtension();
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                        
                        $imageName = Helpers::upload('banner/', $format, $image);
                        
                        if ($imageName && $imageName !== 'def.png') {
                            $banner->image_url = $imageName;
                        } else {
                            throw new \Exception('Image upload failed');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Banner image upload error: ' . $e->getMessage());
                        Toastr::error(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
                        return back()->withInput();
                    }
                }
            }

            $banner->save();

            Toastr::success(translate('messages.banner_added'));
            return redirect()->route('admin.banner.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_add_banner') . ': ' . $e->getMessage());
            \Log::error('Banner creation error: ' . $e->getMessage(), [
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
        $banner = Banner::findOrFail($id);
        return view('admin-views.banner.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'redirect_type' => 'required|in:product,category,external,none',
                'redirect_id' => 'nullable|required_if:redirect_type,product,category|integer',
                'redirect_url' => 'nullable|required_if:redirect_type,external|url|max:500',
                'position' => 'required|in:home_top,home_middle,home_bottom,dashboard',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ], [
                'title.required' => translate('messages.Title is required!'),
                'image.image' => translate('messages.Please upload image files only!'),
                'image.mimes' => translate('messages.Image must be jpeg, jpg, png, gif, or webp!'),
                'image.max' => translate('messages.Image size should be less than 2MB!'),
                'redirect_type.required' => translate('messages.Redirect type is required!'),
                'redirect_type.in' => translate('messages.Invalid redirect type!'),
                'redirect_id.required_if' => translate('messages.Redirect ID is required!'),
                'redirect_url.required_if' => translate('messages.Redirect URL is required!'),
                'redirect_url.url' => translate('messages.Please provide a valid URL!'),
                'position.required' => translate('messages.Position is required!'),
                'position.in' => translate('messages.Invalid position!'),
                'end_date.after_or_equal' => translate('messages.End date must be after or equal to start date!'),
            ]);

            $banner = Banner::findOrFail($id);
            $banner->title = $request->title;
            $banner->description = $request->description ?? null;
            $banner->redirect_type = $request->redirect_type;
            $banner->redirect_id = $request->redirect_id ?? null;
            $banner->redirect_url = $request->redirect_url ?? null;
            $banner->position = $request->position;
            $banner->start_date = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null;
            $banner->end_date = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null;
            $banner->sort_order = $request->sort_order ?? 0;
            $banner->is_active = $request->has('is_active') ? 1 : 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    try {
                        $extension = $image->getClientOriginalExtension();
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $format = in_array(strtolower($extension), $allowedExtensions) ? strtolower($extension) : 'png';
                        
                        $imageName = Helpers::upload('banner/', $format, $image);
                        
                        if ($imageName && $imageName !== 'def.png') {
                            // Delete old image if exists
                            if ($banner->image_url) {
                                try {
                                    $storageType = Helpers::getDisk();
                                    if ($storageType === 's3') {
                                        \Storage::disk('s3')->delete('banner/' . $banner->image_url);
                                    } else {
                                        \Storage::disk('public')->delete('banner/' . $banner->image_url);
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete old banner image: ' . $e->getMessage());
                                }
                            }
                            $banner->image_url = $imageName;
                        } else {
                            throw new \Exception('Image upload failed');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Banner image upload error: ' . $e->getMessage());
                        Toastr::error(translate('messages.failed_to_upload_image') . ': ' . $e->getMessage());
                        return back()->withInput();
                    }
                }
            }

            $banner->save();

            Toastr::success(translate('messages.banner_updated'));
            return redirect()->route('admin.banner.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_banner') . ': ' . $e->getMessage());
            \Log::error('Banner update error: ' . $e->getMessage(), [
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
            $banner = Banner::findOrFail($id);
            
            // Soft delete the banner (image file is kept for potential restore)
            $banner->delete();
            
            Toastr::success(translate('messages.banner_deleted'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_delete_banner') . ': ' . $e->getMessage());
            \Log::error('Banner deletion error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Toggle banner status
     */
    public function statusToggle(Request $request)
    {
        try {
            $banner = Banner::findOrFail($request->id);
            $banner->is_active = $request->status;
            $banner->save();
            Toastr::success(translate('messages.banner_status_updated'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_status') . ': ' . $e->getMessage());
            \Log::error('Banner status update error: ' . $e->getMessage());
            return back();
        }
    }
}

