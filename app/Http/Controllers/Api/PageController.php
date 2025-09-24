<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $pages = Page::query()->whereStatus('active')->get();

        return response()->json([
            'ok'    => true,
            'pages' => $pages
        ]);
    }

    /**
     * Display the specified resource.
     * @param String $slug
     * @return JsonResponse
     */
    public function show(String $slug): JsonResponse
    {
        $page = Page::query()
            ->whereSlug($slug)
            ->whereStatus('active')
            ->first();

        if (!$page) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Page not found')
            ]);
        }

        return response()->json([
            'ok' => true,
            'page' => $page
        ]);
    }
}
