<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * GET: List all categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('projects')->get();
        return response()->json([
            'result' => true,
             'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    /**
     * POST: Create a new category
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($request->name);

        $category = Category::create($validated);

        return response()->json([
            'result' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * GET: Show a specific category with its projects
     */
    public function show($id): JsonResponse
    {
        $category = Category::with('projects')->findOrFail($id);
        return response()->json([
            'result' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category
        ]);
    }

    /**
     * PUT/PATCH: Update category details
     */
    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        if ($request->has('name')) {
            $validated['slug'] = Str::slug($request->name);
        }

        $category->update($validated);

        return response()->json([
            'result' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * DELETE: Remove a category
     */
    public function destroy($id): JsonResponse
    {
        $category = Category::findOrFail($id);

        // Check if category has projects before deleting
        if ($category->projects()->count() > 0) {
            return response()->json([
                'result' => false,
                'message' => 'Cannot delete category: It still contains projects.'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'result' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
