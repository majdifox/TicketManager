<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Agent;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount(['tickets', 'agents'])
            ->paginate(15);

        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'agents' => 'array',
            'agents.*' => 'exists:agents,id',
        ]);

        $category = Category::create($request->only(['name', 'description', 'is_active']));

        if ($request->has('agents')) {
            $category->agents()->attach($request->agents);
        }

        $category->loadCount(['tickets', 'agents']);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category,
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['agents.user'])->loadCount(['tickets']);

        return response()->json($category);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'agents' => 'array',
            'agents.*' => 'exists:agents,id',
        ]);

        $category->update($request->only(['name', 'description', 'is_active']));

        if ($request->has('agents')) {
            $category->agents()->sync($request->agents);
        }

        $category->load(['agents.user'])->loadCount(['tickets']);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Check if category has tickets
        if ($category->tickets()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with existing tickets.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * Get all active categories.
     */
    public function active()
    {
        $categories = Category::active()
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Get available agents for a category.
     */
    public function availableAgents()
    {
        $agents = Agent::with('user:id,name')
            ->available()
            ->get()
            ->map(function ($agent) {
                return [
                    'id' => $agent->id,
                    'name' => $agent->user->name,
                    'department' => $agent->department,
                ];
            });

        return response()->json($agents);
    }
}