<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\SplashScreen;
use App\Http\Requests\SplashScreenRequest;


class SplashScreensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = SplashScreen::all();

        return response()->json([
            'ok'        => true,
            'data'      => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SplashScreenRequest $request
     * @return JsonResponse
     */
    public function store(SplashScreenRequest $request): JsonResponse
    {
        $data           = $request->only('title', 'content');

        if($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('splash-screens', 'public');
        }

        $splashScreen   = SplashScreen::create($data);

        return response()->json([
            'ok'        => true,
            'data'      => $splashScreen,
            'message'   => __('Splash Screen created')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SplashScreenRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(SplashScreenRequest $request, int $id): JsonResponse
    {
        $splashScreen = SplashScreen::find($id);

        if(!$splashScreen) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Splash Screen not found')
            ], 404);
        }

        $data           = $request->only('title', 'content');

        if($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('splash-screens', 'public');
        }

        $splashScreen->update($data);

        return response()->json([
            'ok'        => true,
            'data'      => $splashScreen,
            'message'   => __('Splash Screen updated')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $splashScreen = SplashScreen::find($id);

        if(!$splashScreen) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Splash Screen not found')
            ], 404);
        }

        $splashScreen->delete();

        return response()->json([
            'ok'        => true,
            'message'   => __('Splash Screen deleted')
        ]);
    }

}
