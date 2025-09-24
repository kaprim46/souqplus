<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryRequest;
use App\Models\Category;
use App\Models\SubSubCategory;
use Illuminate\Http\JsonResponse;
use Throwable;

class CategoryController extends Controller
{

    CONST MAX_PER_PAGE = 15;

    /**
     * Get all categories
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function index(CategoryRequest $request): JsonResponse
    {
        try {
            $categories = Category::query()->when($request->get('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });

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
     * @param Category $category
     * @return JsonResponse
     * @throws Throwable
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('filters');

        try {
            return response()->json([
                'ok'            => true,
                'category'      => [
                    ...$category->toArray(),
                    'filters' => collect($category->filters)->pluck('id')
                ]
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
     * Update category
     * @param Category $category
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function update(Category $category, CategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description', 'status', 'meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'filters']);

            $category->update($data);

            if($request->get('filters') && count($request->get('filters')))
            {
                $category->filters()->sync($request->get('filters'));
            }

            return response()->json([
                'ok'            => true,
                'category'      => $category,
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
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description', 'status', 'meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'filters']);

            $category = Category::query()->create($data);

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
                'errors'    => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete category
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();

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

    /**
     * subCategories
     * @param Category $category
     * @return JsonResponse
     */
    public function subCategories(Category $category): JsonResponse
    {
        try {
            $subCategories = $category->subCategories()->when(!request()->user() || !request()->user()->isAdmin(), function ($query) {
                $query->where('status', 'active');
            })->get();

            return response()->json([
                'ok'            => true,
                'categories'    => $subCategories
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
     * Get sub sub categories by sub category
     */
    public function subSubCategories($subCategoryId)
    {
        $subSubCategories = SubSubCategory::where('sub_category_id', $subCategoryId)
            ->get();

        return response()->json([
            'data' => $subSubCategories,
            'ok' => true,
        ]);
    }
}
