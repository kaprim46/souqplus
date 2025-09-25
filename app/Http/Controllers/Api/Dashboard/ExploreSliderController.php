<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Requests\Dashboard\ExploreSliderRequest;
use App\Models\ExploreSlider;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;

class ExploreSliderController
{
    const MAX_PER_PAGE = 15;

    /**
     * Get all explore sliders
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $exploreSliders = ExploreSlider::query()->when($request->get('search'), function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });

            if($request->get('pagination')) {
                $exploreSliders = $exploreSliders->paginate(self::MAX_PER_PAGE);
            } else {
                $exploreSliders = $exploreSliders->get();
            }

            return response()->json([
                'ok'        => true,
                'exploreSliders' => $exploreSliders
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
            ]);
        }
    }

    /**
     * Show explore slider details
     * @param ExploreSlider $slider
     * @return JsonResponse
     * @throws Throwable
     */
    public function show(ExploreSlider $slider): JsonResponse
    {
        try {
            return response()->json([
                'ok'            => true,
                'exploreSlider' => $slider
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'error'     => $e->getMessage()
            ]);
        }
    }

    /**
     * Update explore slider
     * @param ExploreSlider $slider
     * @param ExploreSliderRequest $request
     * @return JsonResponse
     */
    public function update(ExploreSlider $slider, ExploreSliderRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['title', 'description', 'link']);
            $image = $request->file('image');

            if($request->hasFile('image')) {
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $cleanName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $originalName);
        $filename = $cleanName . '_' . time() . '.' . $image->getClientOriginalExtension();
        
        $destinationPath = public_path('storage/explore_sliders');
        
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $image->move($destinationPath, $filename);
        $data['image'] = $filename;
    }

        $slider->update($data);

        return response()->json([
            'ok'            => true,
            'slider'        => $slider,
            'message'       => __('Explore Slider updated successfully')
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'ok'        => false,
            'message'   => __('Something went wrong, please try again later'),
            'error'     => $e->getMessage()
        ]);
    }
}

    /**
     * Store explore slider
     * @param ExploreSliderRequest $request
     * @return JsonResponse
     */
    public function store(ExploreSliderRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['title', 'description', 'link']);
            $image = $request->file('image');

            if($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $originalName);
                $filename = $cleanName . '_' . time() . '.' . $image->getClientOriginalExtension();
                
                $destinationPath = public_path('storage/explore_sliders');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $image->move($destinationPath, $filename);
                $data['image'] = $filename;
            }

            $exploreSlider = ExploreSlider::query()->create($data);

            return response()->json([
                'ok'            => true,
                'slider'        => $exploreSlider,
                'message'       => __('Explore Slider created successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'error'     => $e->getMessage()
            ]);
        }
    }
    /**
     * Delete explore slider
     * @param ExploreSlider $slider
     * @return JsonResponse
     */
    public function destroy(ExploreSlider $slider): JsonResponse
    {
        try {
            $slider->delete();

            return response()->json([
                'ok'            => true,
                'message'       => __('Explore Slider deleted successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
            ]);
        }
    }
}
