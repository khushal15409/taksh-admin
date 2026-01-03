<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponseTrait;

    /**
     * Add address
     */
    public function store(Request $request)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'pincode' => 'required|string',
            'landmark' => 'nullable|string',
            'type' => 'nullable|in:home,work,other',
            'is_default' => 'nullable|boolean',
        ]);

        $user = $request->user();

        // If this is set as default, unset other defaults
        if ($request->input('is_default', false)) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => $user->id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'pincode' => $request->pincode,
            'landmark' => $request->landmark,
            'type' => $request->type ?? 'home',
            'is_default' => $request->input('is_default', false),
        ]);

        return $this->success($address->load(['state', 'city', 'area']), 'api.address_added');
    }

    /**
     * Get user addresses
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $addresses = Address::where('user_id', $user->id)
            ->with(['state', 'city', 'area'])
            ->get();

        return $this->success($addresses, 'api.addresses_fetched');
    }
}

