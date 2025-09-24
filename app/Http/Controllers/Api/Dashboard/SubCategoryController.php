<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Throwable;

class SubCategoryController extends Controller
{

    CONST MAX_PER_PAGE = 15;

    /**
     * Get all categories
     * @param SubCategoryRequest $request
     * @return JsonResponse
     */
    public function index(SubCategoryRequest $request): JsonResponse
    {
        try {
            $categories = SubCategory::query()->when($request->get('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                })
                ->when(!$request->user() || !$request->user()->isAdmin(), function ($query) use ($request) {
                    $query->whereStatus('active');
                })
                ->with('category:id,name');

            if ($request->get('pagination'))
                $categories = $categories->paginate(self::MAX_PER_PAGE);

            else
                $categories = $categories->get();

            return response()->json([
                'ok'                => true,
                'categories'        => $categories
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Show category details
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        try {
            return response()->json([
                'ok'            => true,
                'category'      => [
                    ...$subCategory->toArray(),
                    'filters' => collect($subCategory->filters)->pluck('id')
                ]
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Update category
     * @param SubCategory $subCategory
     * @param SubCategoryRequest $request
     * @return JsonResponse
     */
    public function update(SubCategory $subCategory, SubCategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description', 'status', 'meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'category_id', 'filters']);

            if($request->get('filters') && count($request->get('filters')))
            {
                $subCategory->filters()->sync($request->get('filters'));
            }

            $subCategory->update(collect($data)->except('filters')->toArray());

            return response()->json([
                'ok'            => true,
                'category'      => $subCategory,
                'message'       => __('Category updated successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Store category
     * @param SubCategoryRequest $request
     * @return JsonResponse
     */
    public function store(SubCategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description', 'status', 'meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'category_id', 'filters']);

            $category = SubCategory::create($data);

            if($request->get('filters') && count($request->get('filters')))
            {
                $category->filters()->sync($request->get('filters'));
            }

            return response()->json([
                'ok'            => true,
                'category'      => $category,
                'message'       => __('Category created successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'error'     => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete category
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function destroy(SubCategory $subCategory): JsonResponse
    {
        try {
            $subCategory->delete();

            return response()->json([
                'ok'            => true,
                'message'       => __('Category deleted successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }
}
