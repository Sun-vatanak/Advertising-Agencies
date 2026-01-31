<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects for the portfolio page.
     */
    public function index(): JsonResponse
    {
        // We use 'with' to eager-load the category and media to keep the API fast
        $projects = Project::with(['category', 'media'])
            ->latest()
            ->paginate(12);

        return response()->json($projects);
    }

    /**
     * Display a single project detail page.
     */
    public function show(string $slug): JsonResponse
    {
        $project = Project::with(['category', 'media'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($project);
    }
}
