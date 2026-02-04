<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display listing (Read All)
     */
    public function index(): JsonResponse
    {
        $projects = Project::with('category')->latest()->paginate(12);
        return response()->json($projects);
    }

    /**
     * Get projects by category
     */
    public function getByCategory($categoryId): JsonResponse
    {
        // Validate that category exists
        $category = Category::findOrFail($categoryId);

        $projects = Project::where('category_id', $categoryId)
            ->with('category')
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $projects,
            'message' => 'Projects retrieved successfully'
        ]);
    }

    /**
     * Get projects by category slug (alternative method)
     */
    public function getByCategorySlug($categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        $projects = Project::where('category_id', $category->id)
            ->with('category')
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $projects,
            'message' => 'Projects retrieved successfully'
        ]);
    }

    /**
     * Get featured projects
     */
    public function getFeatured(): JsonResponse
    {
        $projects = Project::where('is_featured', true)
            ->with('category')
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $projects,
            'message' => 'Featured projects retrieved successfully'
        ]);
    }

    /**
     * Create Project (Create)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'title'        => 'required|string|max:255',
            'client_name'  => 'required|string|max:255',
            'description'  => 'required|string',
            'tech_stack'   => 'array',
            'is_featured'  => 'boolean',
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB Max
        ]);

        // Handle File Upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('projects/thumbnails', 'public');
            $validated['thumbnail_url'] = Storage::url($path);
        }

        // Generate SEO Slug
        $validated['slug'] = Str::slug($request->title) . '-' . Str::random(5);

        $project = Project::create($validated);

        return response()->json(['result' => true, 'data' => $project], 201);
    }

    /**
     * Show single project (Read One)
     */
    public function show(string $slug): JsonResponse
    {
        $project = Project::with('category')->where('slug', $slug)->firstOrFail();
        return response()->json($project);
    }

    /**
     * Update Project (Update)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'category_id'  => 'sometimes|exists:categories,id',
            'title'        => 'sometimes|string|max:255',
            'client_name'  => 'sometimes|string|max:255',
            'description'  => 'sometimes|string',
            'tech_stack'   => 'nullable|array',
            'is_featured'  => 'boolean',
        ]);

        // Logic to delete old image if new one is uploaded
        if ($request->hasFile('thumbnail')) {
            // Delete old file if it exists
            if ($project->thumbnail_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $project->thumbnail_url));
            }
            $path = $request->file('thumbnail')->store('projects/thumbnails', 'public');
            $validated['thumbnail_url'] = Storage::url($path);
        }

        $project->update($validated);

        return response()->json(['result' => true, 'message' => 'Updated successfully']);
    }

    /**
     * Delete Project (Delete)
     */
    public function destroy($id): JsonResponse
    {
        $project = Project::findOrFail($id);

        // Cleanup file from storage
        if ($project->thumbnail_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $project->thumbnail_url));
        }

        $project->delete();

        return response()->json(['result' => true, 'message' => 'Project deleted']);
    }
}
