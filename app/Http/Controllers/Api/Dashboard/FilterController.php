<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CategoryFilter;
use App\Models\City;
use App\Models\Country;
use App\Models\Filter;
use App\Models\SubCategoryFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Throwable;

class FilterController extends Controller
{

    private const MAX_PER_PAGE = 10;

    /**
     * Display a listing of the resource or search for a country
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = Filter::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->when($request->get('pagination'), function ($query) {
                return $query->paginate(self::MAX_PER_PAGE);
            }, function ($query) {
                return $query->get();
            });


            return response()->json([
                'ok'                => true,
                'data_filters'      => $filters
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'          => 'required|in:select,radio,checkbox,range,text,number,year,color',
            'name'          => 'required|string',
            'options'       => 'required|array',
            'options.nameField'     => 'required|string',
            'options.placeholder'   => 'required|string',
            'options.options'       => 'required_if:type,select,radio,checkbox|array',
            'options.options.*.name'    => 'required_if:type,select,radio,checkbox|nullable|string',
            'options.options.*.value'   => 'required_if:type,select,radio,checkbox|nullable|string',
            'options.from'              => 'required_if:type,year,range',
            'options.to'                => 'required_if:type,year,range'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'ok'        => false,
                'errors'    => $validator->errors()
            ], 422);
        }

        $filter = Filter::create([
            'type'          => $request->get('type'),
            'name'          => $request->get('name'),
            'options'       => $request->get('options')
        ]);

        return response()->json([
            'data'      => $filter,
            'ok'        => true,
            'message'   => __('Filter created successfully')
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Filter $filter
     * @return JsonResponse
     */
    public function update(Request $request, Filter $filter): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'          => 'required|in:select,radio,checkbox,range,text,number,year,color',
            'name'          => 'required|string',
            'options'       => 'required|array',
            'options.nameField'     => 'required|string',
            'options.placeholder'   => 'required|string',
            'options.options'       => 'required_if:type,select,radio,checkbox|array',
            'options.options.*.name'    => 'required_if:type,select,radio,checkbox|nullable|string',
            'options.options.*.value'   => 'required_if:type,select,radio,checkbox|nullable|string',
            'options.from'              => 'required_if:type,year,range',
            'options.to'                => 'required_if:type,year,range'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'ok'        => false,
                'errors'    => $validator->errors()
            ], 422);
        }

        $filter->update([
            'type'          => $request->get('type'),
            'name'          => $request->get('name'),
            'options'       => $request->get('options')
        ]);

        return response()->json([
            'data'      => $filter,
            'ok'        => true,
            'message'   => __('Filter updated successfully')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Filter $filter
     * @return JsonResponse
     */
    public function destroy(Filter $filter): JsonResponse
    {
        $filter->delete();

        return response()->json([
            'ok' => true
        ]);
    }

    /**
     * getFiltersByCategory
     * @param int $CategoryId
     * @return JsonResponse
     */
    public function getFiltersByCategory(int $CategoryId): JsonResponse
    {
        $cacheKey = 'filters_for_category_' . $CategoryId;

        $filters = Cache::remember($cacheKey, 60, function() use ($CategoryId) {
            return CategoryFilter::where('category_id', $CategoryId)
                ->with(['filter' => function($query) {
                    $query->select('id', 'name', 'type', 'options');
                }])
                ->get(['filter_id'])
                ->map(function ($categoryFilter) {
                    return $categoryFilter->filter;
                });
        });

        $filtersArray = $filters->toArray();


        return response()->json([
            'ok'        => true,
            'filters'   => $filtersArray
        ]);
    }

    /**
     * getFiltersByCategory
     * @param int $SubCategoryId
     * @return JsonResponse
     */
    public function getFiltersBySubCategory(int $SubCategoryId): JsonResponse
    {
        $cacheKey = 'filters_for_sub_category_' . $SubCategoryId;

        $filters = Cache::remember($cacheKey, 60, function() use ($SubCategoryId) {
            return SubCategoryFilter::where('sub_category_id', $SubCategoryId)
                ->with(['filter' => function($query) {
                    $query->select('id', 'name', 'type', 'options');
                }])
                ->get(['filter_id'])
                ->map(function ($categoryFilter) {
                    return $categoryFilter->filter;
                });
        });

        $filtersArray = $filters->toArray();

        return response()->json([
            'ok' => true,
            'filters' => $filtersArray
        ]);
    }
}
