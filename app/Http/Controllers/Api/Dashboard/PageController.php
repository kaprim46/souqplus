<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Requests\Dashboard\CategoryRequest;
use App\Http\Requests\Dashboard\PageRequest;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Throwable;

class PageController
{

    CONST MAX_PER_PAGE = 15;

    /**
     * Get all categories
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function index(PageRequest $request): JsonResponse
    {
        try {
            $pages = Page::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            })
                ->paginate(self::MAX_PER_PAGE);

            return response()->json([
                'ok'        => true,
                'pages'     => $pages
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
            ]);
        }
    }

    /**
     * Show page details
     * @param Page $page
     * @return JsonResponse
     * @throws Throwable
     */
    public function show(Page $page): JsonResponse
    {
        try {
            return response()->json([
                'ok'            => true,
                'page'          => $page
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
     * Update page
     * @param Page $page
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function update(Page $page, PageRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'content', 'status']);
            
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                
                // THE FIX:
                // 1. We specify 'pages' as the path.
                // 2. We explicitly state 'public' as the disk to use.
                $path = $icon->store('pages', 'public');
                
                $data['icon'] = basename($path);
            }

            $page->update($data);

            return response()->json([
                'ok' => true,
                'page' => $page,
                'message' => __('Page updated successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'error' => $e->getMessage()
            ]);
        }
    }


    /**
     * Store Page
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function store(PageRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'content', 'status']);

            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');

                // THE SAME FIX APPLIED HERE:
                $path = $icon->store('pages', 'public');

                $data['icon'] = basename($path);
            }

            $page = Page::query()->create($data);

            return response()->json([
                'ok' => true,
                'page' => $page,
                'message' => __('Page created successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete Page
     * @param Page $page
     * @return JsonResponse
     */
    public function destroy(Page $page): JsonResponse
    {
        try {
            $page->delete();

            return response()->json([
                'ok'            => true,
                'message'       => __('Page deleted successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
            ]);
        }
    }
}
