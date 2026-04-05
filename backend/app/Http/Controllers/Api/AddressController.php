<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Get all addresses for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderBy('is_default', 'desc')->get();
        return ApiResponse::success($addresses, 'Addresses fetched successfully.');
    }

    /**
     * Store a newly created address in storage
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'phone'     => 'required|string|max:15',
            'line1'     => 'required|string|max:255',
            'city'      => 'required|string|max:100',
            'pincode'   => 'required|string|max:10',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        // If setting as default, unset others
        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($validated);

        return ApiResponse::success($address, 'Address created successfully.', 201);
    }

    /**
     * Get address from coordinates (reverse geolocation)
     */
    public function fromCoordinates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // For now, return a response template that the frontend can use
        // In production, you would integrate with a geolocation API like Google Maps
        return ApiResponse::success([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_name' => 'Use your map API to get address details'
        ], 'Location fetched. Please complete the address details.');
    }

    /**
     * Update the specified address in storage
     */
    public function update(Request $request, Address $address): JsonResponse
    {
        // Check ownership
        if ($address->user_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'phone'     => 'required|string|max:15',
            'line1'     => 'required|string|max:255',
            'city'      => 'required|string|max:100',
            'pincode'   => 'required|string|max:10',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        // If setting as default, unset others
        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return ApiResponse::success($address, 'Address updated successfully.');
    }

    /**
     * Remove the specified address from storage
     */
    public function destroy(Request $request, Address $address): JsonResponse
    {
        // Check ownership
        if ($address->user_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $address->delete();

        return ApiResponse::success(null, 'Address deleted successfully.');
    }

    /**
     * Set address as default
     */
    public function setDefault(Request $request, Address $address): JsonResponse
    {
        // Check ownership
        if ($address->user_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        // Unset all others
        $request->user()->addresses()->update(['is_default' => false]);

        // Set this as default
        $address->update(['is_default' => true]);

        return ApiResponse::success($address, 'Address set as default.');
    }
}
