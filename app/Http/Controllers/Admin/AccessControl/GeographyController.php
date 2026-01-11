<?php

namespace App\Http\Controllers\Admin\AccessControl;

use App\Enums\ViewPaths\Admin\AccessControl;
use App\Http\Controllers\Controller;
use App\Models\Geography;
use App\Models\State;
use App\Models\Zone;
use App\Models\Area;
use App\Models\Pincode;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeographyController extends Controller
{
    public function index(Request $request): View
    {
        $geographies = Geography::with('parent')
            ->when($request->get('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('code', 'like', '%' . $request->get('search') . '%');
            })
            ->when($request->get('level'), function ($query) use ($request) {
                $query->where('level', $request->get('level'));
            })
            ->orderByRaw("FIELD(level, 'india', 'state', 'zone', 'area', 'pincode')")
            ->orderBy('name')
            ->paginate(25);

        return view(AccessControl::GEOGRAPHY_INDEX['VIEW'], compact('geographies'));
    }

    public function create(): View
    {
        $levels = ['india', 'state', 'zone', 'area', 'pincode'];
        $parentGeographies = Geography::active()->orderByRaw("FIELD(level, 'india', 'state', 'zone', 'area')")->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $zones = Zone::active()->orderBy('name')->get();
        $areas = Area::with('city.state')->orderBy('name')->get();
        $pincodes = Pincode::where('status', 1)->orderBy('pincode')->get();

        return view(AccessControl::GEOGRAPHY_ADD['VIEW'], compact('levels', 'parentGeographies', 'states', 'zones', 'areas', 'pincodes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'parent_id' => 'nullable|exists:geographies,id',
            'level' => 'required|in:india,state,zone,area,pincode',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'state_id' => 'nullable|exists:states,id',
            'zone_id' => 'nullable|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'pincode_id' => 'nullable|exists:pincodes,id',
            'is_active' => 'boolean',
        ]);

        // Build path
        $path = $request->code ?? strtolower(str_replace(' ', '-', $request->name));
        if ($request->parent_id) {
            $parent = Geography::find($request->parent_id);
            $path = ($parent->path ?? '') . '/' . $path;
        } else {
            $path = $path;
        }

        $geography = Geography::create([
            'parent_id' => $request->parent_id,
            'level' => $request->level,
            'name' => $request->name,
            'code' => $request->code ?? strtolower(str_replace(' ', '-', $request->name)),
            'state_id' => $request->state_id,
            'zone_id' => $request->zone_id,
            'area_id' => $request->area_id,
            'pincode_id' => $request->pincode_id,
            'path' => $path,
            'is_active' => $request->has('is_active'),
        ]);

        // Update path to include full hierarchy
        $geography->path = Geography::buildPath($geography);
        $geography->save();

        Toastr::success(translate('messages.geography_added_successfully'));
        return redirect()->route('admin.access-control.geography.index');
    }

    public function edit($id): View
    {
        $geography = Geography::with('parent')->findOrFail($id);
        $levels = ['india', 'state', 'zone', 'area', 'pincode'];
        $parentGeographies = Geography::where('id', '!=', $id)
            ->active()
            ->orderByRaw("FIELD(level, 'india', 'state', 'zone', 'area')")
            ->orderBy('name')
            ->get();
        $states = State::orderBy('name')->get();
        $zones = Zone::active()->orderBy('name')->get();
        $areas = Area::with('city.state')->orderBy('name')->get();
        $pincodes = Pincode::where('status', 1)->orderBy('pincode')->get();

        return view(AccessControl::GEOGRAPHY_UPDATE['VIEW'], compact('geography', 'levels', 'parentGeographies', 'states', 'zones', 'areas', 'pincodes'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $geography = Geography::findOrFail($id);

        $request->validate([
            'parent_id' => 'nullable|exists:geographies,id',
            'level' => 'required|in:india,state,zone,area,pincode',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'state_id' => 'nullable|exists:states,id',
            'zone_id' => 'nullable|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'pincode_id' => 'nullable|exists:pincodes,id',
            'is_active' => 'boolean',
        ]);

        // Build new path
        $code = $request->code ?? strtolower(str_replace(' ', '-', $request->name));
        $path = Geography::buildPath($geography);

        $geography->update([
            'parent_id' => $request->parent_id,
            'level' => $request->level,
            'name' => $request->name,
            'code' => $code,
            'state_id' => $request->state_id,
            'zone_id' => $request->zone_id,
            'area_id' => $request->area_id,
            'pincode_id' => $request->pincode_id,
            'is_active' => $request->has('is_active'),
        ]);

        // Rebuild path
        $geography->refresh();
        $geography->path = Geography::buildPath($geography);
        $geography->save();

        Toastr::success(translate('messages.geography_updated_successfully'));
        return redirect()->route('admin.access-control.geography.index');
    }

    public function destroy($id): RedirectResponse
    {
        $geography = Geography::findOrFail($id);
        
        // Check if has children
        if ($geography->children()->count() > 0) {
            Toastr::error(translate('messages.cannot_delete_geography_with_children'));
            return back();
        }

        // Check if has assignments
        if ($geography->assignments()->count() > 0) {
            Toastr::error(translate('messages.cannot_delete_geography_with_assignments'));
            return back();
        }

        $geography->delete();
        Toastr::success(translate('messages.geography_deleted_successfully'));
        return redirect()->route('admin.access-control.geography.index');
    }

    public function status(Request $request, $id)
    {
        $geography = Geography::findOrFail($id);
        $geography->is_active = $request->status;
        $geography->save();

        return response()->json([
            'status' => true,
            'message' => translate('messages.geography_status_updated_successfully')
        ]);
    }
}
