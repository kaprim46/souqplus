<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;

class SubSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource for a specific SubCategory.
     */
    public function index(Request $request, SubCategory $subCategory)
    {
        $query = $subCategory->subSubCategories();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $subSubCategories = $query->latest()->paginate($request->pagination ?? 10);

        return $subSubCategories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        $subSubCategory = SubSubCategory::create($validated);

        return response()->json([
            'ok' => true,
            'message' => 'Sub Sub Category created successfully.',
            'data' => $subSubCategory,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubSubCategory $subSubCategory)
    {
        return response()->json([
            'data' => $subSubCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubSubCategory $subSubCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $subSubCategory->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'Sub Sub Category updated successfully.',
            'data' => $subSubCategory,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubSubCategory $subSubCategory)
    {
        $subSubCategory->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Sub Sub Category deleted successfully.',
        ]);
    }
}