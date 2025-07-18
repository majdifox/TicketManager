<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Agent;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount(['tickets', 'agents'])
            ->paginate(15);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $agents = Agent::with('user:id,name')->get();
        
        return Inertia::render('Admin/Categories/Create', [
            'agents' => $agents,
        ]);
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

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['agents.user'])->loadCount(['tickets']);

        return Inertia::render('Admin/Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $category->load(['agents.user']);
        $agents = Agent::with('user:id,name')->get();

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
            'agents' => $agents,
        ]);
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

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Check if category has tickets
        if ($category->tickets()->exists()) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete category with existing tickets.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Get all active categories (API endpoint).
     */
    public function active()
    {
        $categories = Category::active()
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Get available agents for a category (API endpoint).
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