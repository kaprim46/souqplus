<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends Controller
{
    const MAX_PER_PAGE = 15;

    /**
     * Get all categories
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = validator()->make($request->all(), [
                'search'    => 'nullable|string',
                'paginate'  => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'ok'        => false,
                    'message'   => $validator->errors()->first()
                ]);
            }

            $categories = Category::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })->whereStatus(Category::STATUS_ACTIVE);

            if ($request->get('paginate')) {
                $categories = $categories
                    ->paginate(self::MAX_PER_PAGE);
            } else {
                $categories = $categories
                    ->get();
            }

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
     * Get category by slug
     * @param String $slug
     * @return JsonResponse
     */
    public function show(String $slug): JsonResponse
    {
        try {
            $category = Category::query()
                ->whereSlug($slug)
                ->whereStatus(Category::STATUS_ACTIVE)
                ->with(['filters'])
                ->first();

            if (!$category) {
                return response()->json([
                    'ok' => false,
                    'message' => __('Category not found')
                ]);
            }

            return response()->json([
                'ok' => true,
                'category' => $category
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
