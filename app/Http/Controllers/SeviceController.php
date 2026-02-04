<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeviceController extends Controller
{
    /**
     * Display the list of service cards.
     * Returns JSON response
     */
    public function index()
    {
        // Load services with their features
        $services = Service::with('features')->get();

        return response()->json([
            'success' => true,
            'data' => $services,
            'message' => 'Services retrieved successfully'
        ], 200);
    }

    /**
     * Store a new service with its image and features.
     * Returns JSON response
     */
    public function store(Request $request)
    {
        try {
            // 1. Validate the incoming request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
                'features' => 'required|array',
                'features.*' => 'required|string|max:255',
                'link_url' => 'nullable|url|max:255',
            ]);

            // 2. Handle the Image Upload
            $path = $request->file('image')->store('services', 'public');

            // 3. Create the Service Card
            $service = Service::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $path,
                'link_url' => $request->link_url ?? '#',
            ]);

            // 4. Save the features
            foreach ($request->features as $featureName) {
                $service->features()->create([
                    'name' => $featureName
                ]);
            }

            // Load features for response
            $service->load('features');

            return response()->json([
                'success' => true,
                'data' => $service,
                'message' => 'Service created successfully!'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service.
     * Returns JSON response
     */
    public function show(Service $service)
    {
        $service->load('features');

        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Service retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified service in storage.
     * Returns JSON response
     */
    public function update(Request $request, Service $service)
    {
        try {
            // 1. Validate the incoming request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
                'features' => 'required|array',
                'features.*' => 'required|string|max:255',
                'link_url' => 'nullable|url|max:255',
            ]);

            // 2. Handle Image Upload if new image is provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }

                // Store new image
                $path = $request->file('image')->store('services', 'public');
                $service->image = $path;
            }

            // 3. Update the Service
            $service->update([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $service->image,
                'link_url' => $request->link_url ?? '#',
            ]);

            // 4. Update features - delete old ones and create new ones
            $service->features()->delete();

            foreach ($request->features as $featureName) {
                $service->features()->create([
                    'name' => $featureName
                ]);
            }

            // Load features for response
            $service->load('features');

            return response()->json([
                'success' => true,
                'data' => $service,
                'message' => 'Service updated successfully!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a service and its image from storage.
     * Returns JSON response
     */
    public function destroy(Service $service)
    {
        try {
            // Delete the image file from storage
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            // Delete the service
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
