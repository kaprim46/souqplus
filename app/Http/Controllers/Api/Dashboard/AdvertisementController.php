<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Requests\Dashboard\AdvertisementRequest;
use App\Http\Resources\AdvertisementResource;
use App\Http\Resources\AdvertisementsResource;
use App\Notifications\AdStore;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationServices;
use App\Jobs\AdsApprovedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Throwable;

class AdvertisementController
{
    CONST MAX_PER_PAGE = 15;

    public function __construct(protected NotificationServices $notificationService)
    {}

    /**
     * Get advertisements
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search'        => 'string',
            'category'      => 'integer|exists:categories,id',
            'sub_category'  => 'integer|exists:sub_categories,id',
            'is_random'     => 'boolean',
            'latest'        => 'boolean',
            'pagination'    => 'boolean',
            'except'        => 'nullable|numeric|exists:advertisements,id',
            'user_id'       => 'nullable|numeric|exists:users,id',
            'category_filter'               => 'nullable|array',
            'category_filter.*.filter_id'   => 'required|exists:filters,id',
            'category_filter.*.value'       => 'required|string',

        ]);

        /**
         * Check if validation fails
         */
        if ($validator->fails()) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Validation error'),
                'errors'    => $validator->errors()
            ])->setStatusCode(422);
        }

        try {
            $advertisements = Advertisement::withTrashed()
                ->select('advertisements.*')
                ->with([
                    'category' => function($q){
                        $q->select(['id', 'name', 'slug']);
                    },
                    'user' => function($q){
                        $q->select(['id', 'name', 'email', 'country_code', 'phone_number', 'is_verified', 'deleted_at'])->withTrashed();
                    },
                    'mainMedia' => function($q){
                        $q->select(['file_name', 'path']);
                    },
                    'subCategory' => function($q){
                        $q->select(['id', 'name', 'slug']);
                    },
                    'country' => function($q){
                        $q->select(['id', 'name']);
                    },
                    'city' => function($q){
                        $q->select(['id', 'name']);
                    }
                ])
                ->withCount('comments')
                ->when($request->get('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('price', 'like', '%' . $search . '%');
                })
                ->when($request->get('category'), function ($query, $category_id) {
                    $query->where('category_id', $category_id);
                })
                ->when($request->get('sub_category'), function ($query, $sub_category_id) {
                    $query->where('sub_category_id', $sub_category_id);
                })
                ->when($request->get('except'), function ($query, $except) {
                    $query->where('id', '!=', $except);
                })
                ->when($request->get('limit'), function ($query, $limit) {
                    $query->limit($limit);
                })
                ->when($request->get('user_id'), function ($query, $user_id) {
                    $query->where('user_id', $user_id);
                })
                ->when($request->get('is_random'), function ($query) {
                    $query->inRandomOrder();
                })
                ->when($request->get('latest'), function ($query) {
                    $query->latest();
                });

            /**
             * Get or paginate advertisements
             */
            if($request->get('pagination'))
                $advertisements = $advertisements->paginate(self::MAX_PER_PAGE);
            else
                $advertisements = $advertisements->get();


            $advertisementsCollection = AdvertisementsResource::collection($advertisements);

            return response()->json([
                'ok'                 => true,
                'advertisements'     => [
                    'data'          => $advertisementsCollection,
                    'current_page'  => $advertisements->currentPage(),
                    'last_page'     => $advertisements->lastPage(),
                    'per_page'      => $advertisements->perPage(),
                    'total'         => $advertisements->total()
                ]
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Show advertisement details
     * @param int $advertisement_id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $advertisement_id, Request $request): JsonResponse
    {
        try {
            $advertisement = Advertisement::withTrashed()
                ->with('country', 'city')
                ->findOrFail($advertisement_id);

            $advertisement->load([
                'category' => function($q){
                    $q->select(['id', 'name']);
                },
                'subCategory' => function($q){
                    $q->select(['id', 'name']);
                },
                'mainMedia' => function($q){
                    $q->select(['file_name', 'path']);
                }
            ]);

            $advertisement->load(['mediaRelation' => function($q){
                $q->select(['id', 'file_id', 'is_main', 'advertisements_id']);
            }]);

            return response()->json([
                'ok'            => true,
                'advertisement' => new AdvertisementResource($advertisement)
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update advertisement
     * @param int $advertisement_id
     * @param AdvertisementRequest $request
     * @return JsonResponse
     */
    public function update(int $advertisement_id, AdvertisementRequest $request): JsonResponse
    {
        try {
            $advertisement  = Advertisement::withTrashed()->findOrFail($advertisement_id);
            $data           = $request->only([
                'name',
                'description',
                'price',
                'category',
                'sub_category',
                'latitude',
                'longitude',
                'country',
                'city',
                'user',
                'filters',
                'status',
            ]);
            $images         = $request->get('media');
            $mediaToDelete  = $request->get('mediaToDelete', []);
            $cloneStatus    = $advertisement->status;
            $categoryName = $request->input('category.name');

            $advertisement->update([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'category_id'       => $data['category']['id'],
                'sub_category_id'   => $data['sub_category']['id'] ?? null,
                'latitude'          => $data['latitude'],
                'longitude'         => $data['longitude'],
                'country_id'        => $data['country']['id'],
                'city_id'           => $data['city']['id'] ?? null,
                'country_code'      => $data['user']['country_code'],
                'phone_number'      => $data['user']['phone_number'],
                'custom_fields'     => $data['filters'] ?? [],
                'status'            => $data['status'],
            ]);

            /**
             * Handle media updates
             */
            if (is_array($images)) {
                // Get current media IDs for this advertisement
                $currentMediaIds = $advertisement->media->pluck('id')->toArray();

                // Identify media IDs that were on the advertisement but are not in the incoming request or marked for deletion
                // This is a safeguard, though frontend should handle most cases
                $mediaToDetachImplicitly = array_diff($currentMediaIds, array_column($images, 'id'), $mediaToDelete);

                // Detach media specifically marked for deletion on the frontend
                if (!empty($mediaToDelete)) {
                    $advertisement->media()->detach($mediaToDelete);
                }

                // Detach media that were removed from the frontend list without explicit delete (if any)
                if (!empty($mediaToDetachImplicitly)) {
                     $advertisement->media()->detach($mediaToDetachImplicitly);
                }

                // Sync new media (or update existing media properties if needed, like is_main)
                // This assumes $images contains the desired state of media for this ad
                $syncData = [];
                foreach ($images as $image) {
                     $syncData[$image['id']] = ['is_main' => $image['is_main'] ?? false];
                }
                $advertisement->media()->sync($syncData, false); // Use sync with false to only attach new ones not already present, and update pivot data
            }

            /**
             * Check if user admin and status not equal old status then send notification to user
             */
            if(
                $request->get('status') && $cloneStatus !== $request->get('status')
            ){
                dispatch(function () use ($advertisement) {
                    $advertisement->user->notify(new AdStore($advertisement));
                })
                    ->onQueue('notifications')
                    ->afterResponse();

                /**
                 * Sent ads approved notification to followers
                 */
                if($advertisement->status === Advertisement::STATUS_APPROVED) {
                    AdsApprovedNotification::dispatch($advertisement)
                        ->onQueue('notifications')->afterResponse();
                }

                /**
                 * Sent notification to the customer
                 */
                $matchStatus = match($advertisement->status) {
                    Advertisement::STATUS_APPROVED  => 'ad_approved',
                    Advertisement::STATUS_REJECTED  => 'ad_rejected',
                    Advertisement::STATUS_PENDING   => 'ad_pending',
                    default => null
                };

                $this->notificationService->saveNotification(
                    $advertisement->user_id,
                    null,
                    $matchStatus,
                    $advertisement->id
                );
             }

            /**
             * Filters Sync
             */
            if($request->get('filters') && count($request->get('filters')))
            {
                $advertisement->filters()->detach();
                $advertisement->filters()->sync(
                    collect($request->get('filters'))->map(fn($q) => [
                        'value' => $q['value'],
                        'filter_id' => $q['filter_id']
                    ])->values()
                );
            }

            return response()->json([
                'ok'            => true,
                'advertisement' => $advertisement,
                'message'       => __('Advertisement updated successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Store advertisement (UPDATE DONE)
     * @param AdvertisementRequest $request
     * @return JsonResponse
     */
    public function store(AdvertisementRequest $request): JsonResponse
    {
        try {
            $data           = $request->only([
                'name',
                'description',
                'price',
                'category',
                'sub_category',
                'latitude',
                'longitude',
                'country',
                'city',
                'user',
                'filters',
                'status',
            ]);
            $images         = $request->get('media');
            $categoryName = $request->input('category.name');
            $advertisement  = Advertisement::create([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'category_id'       => $data['category']['id'],
                'sub_category_id'   => $data['sub_category']['id'] ?? null,
                'latitude'          => $data['latitude'],
                'longitude'         => $data['longitude'],
                'country_id'        => $data['country']['id'],
                'city_id'           => $data['city']['id'] ?? null,
                'country_code'      => $data['user']['country_code'],
                'phone_number'      => $data['user']['phone_number'],
                'custom_fields'     => $data['filters'] ?? [],
                'status'            => $data['status'],
                'user_id'           => auth()->id(),
            ]);

            if(!$advertisement) {
                throw new \Exception(__('Advertisement not created'));
            }

            /**
             * Filter custom fields
             */
            if($request->get('filters') && count($request->get('filters')))
            {
                $advertisement->filters()->sync(
                    collect($request->get('filters'))->map(fn($q) => [
                        'value' => $q['value'],
                        'filter_id' => $q['filter_id']
                    ])->values()
                );
            }

            /**
             * Upload images
             */
            if($request->has('media')){
                $advertisement->mediaRelation()->createMany(collect($images)->map(function($item){
                    return [
                        'file_id' => $item['id'],
                        'is_main' => $item['is_main'] ?? false
                    ];
                }));
            }

            return response()->json([
                'ok'            => true,
                'advertisement' => $advertisement,
                'message'       => __('Advertisement created successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete advertisement
     * @param int $advertisement_id
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(int $advertisement_id, Request $request): JsonResponse
{
    $advertisement = Advertisement::withTrashed()
        ->findOrFail($advertisement_id);

    try {
        DB::table('favorite_advertisements')
            ->where('advertisements_id', $advertisement->id)
            ->delete();

        if($advertisement->trashed()) {
            $advertisement->restore();
        } else {
            // Only delete related stories when hard deleting
            DB::table('advertisement_stories')
                ->where('advertisement_id', $advertisement->id)
                ->delete();
            $advertisement->delete();
        }

        return response()->json([
            'ok'        => true,
            'message'   => !$advertisement->trashed() 
                ? __('Advertisement restored successfully') 
                : __('Advertisement deleted successfully')
        ]);
    } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }
}
