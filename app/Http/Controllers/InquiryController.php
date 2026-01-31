<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // 1. Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'company' => 'nullable|string|max:255',
            'message' => 'required|string',
            'service_interested_in' => 'nullable|string'
        ]);

        // 2. Create the record in the database
        $inquiry = Inquiry::create($validated);

        // 3. Return success message to Vue.js
        return response()->json([
            'message' => 'Thank you! Your message has been sent.',
            'data' => $inquiry
        ], 201);
    }
}
