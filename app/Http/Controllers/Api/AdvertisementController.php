<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdvertisementRequest;
use App\Http\Requests\Dashboard\CustomerRequest;
use App\Http\Resources\AdvertisementsResource;
use App\Http\Resources\AdvertisementResource;
use App\Jobs\NewCommentNotificationJob;
use App\Http\Resources\StoriesResource;
use App\Jobs\AdsApprovedNotification;
use App\Models\Advertisement;
use App\Models\AdvertisementStory;
use App\Notifications\AdStore;
use App\Services\NotificationServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Category;
use App\Models\Filter;

class AdvertisementController extends Controller
{
    const MAX_PER_PAGE = 15;

    public function __construct(protected NotificationServices $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
            'sub_sub_category' => 'integer|exists:sub_sub_categories,id',
            'is_random'     => 'boolean',
            'latest'        => 'boolean',
            'pagination'    => 'boolean',
            'except'        => 'nullable|numeric|exists:advertisements,id',
            'user_id'       => 'nullable|numeric|exists:users,id',
            'category_filter'               => 'nullable|array',
            'category_filter.*.filter_id'   => 'required|exists:filters,id',
            'category_filter.*.value'       => 'required|string',
            'manufacturing_year'            => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'sort_by'                       => 'nullable|string|in:latest,oldest,popular',
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
            Log::info('Request all: ', $request->all());
            $userId = $request->user() ? $request->user()->id : null;

            // Get the filter ID for 'manufacturing_year' once
            $manufacturingYearFilter = Filter::where('name', 'manufacturing_year')->first();
            // If not found by 'manufacturing_year', try 'سنة الصنع'
            if (!$manufacturingYearFilter) {
                $manufacturingYearFilter = Filter::where('name', 'سنة الصنع')->first();
            }
            $manufacturingYearFilterId = $manufacturingYearFilter ? $manufacturingYearFilter->id : null;
            Log::info('Manufacturing Year Filter ID retrieved: ' . $manufacturingYearFilterId);
            Log::info('Manufacturing Year from request before when clause: ' . $request->get('manufacturing_year'));

            $advertisements = Advertisement::query()
                ->select('advertisements.*')
                ->selectSub(function ($query) use ($userId) {
                    $query->selectRaw('(COUNT(*) > 0) as is_favorite')
                        ->from('favorite_advertisements')
                        ->whereColumn('favorite_advertisements.advertisements_id', 'advertisements.id')
                        ->where(function ($query) use ($userId) {
                            if ($userId) {
                                $query->where('favorite_advertisements.user_id', $userId);
                            } else {
                                $query->whereRaw('1 = 0');
                            }
                        });
                }, 'is_favorite')
                ->withCount('comments')
                ->with(['category', 'subCategory', 'subSubCategory', 'mainMedia', 'user', 'country', 'city'])
                ->where('status', Advertisement::STATUS_APPROVED)
                ->when($request->get('city_id'), function ($query, $city_id) {
                    $query->where('city_id', $city_id);
                })
                ->when($request->get('country_id'), function ($query, $country_id) {
                    $query->where('country_id', $country_id);
                })
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
                ->when($request->get('sub_sub_category'), function ($query, $sub_sub_category_id) {
                    $query->where('sub_sub_category_id', $sub_sub_category_id);
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
                ->when($request->has('category_filter') && is_array($request->get('category_filter')) && count($request->get('category_filter')), function ($query) use ($request) {
                    collect($request->get('category_filter'))->each(function ($item, $key) use ($query) {
                        if (isset($item['filter_id']) && isset($item['value'])) {
                            if ($key === 0) {
                                $query->whereJsonContains('custom_fields', [
                                    'filter_id' => intval($item['filter_id']),
                                    'value'     => $item['value']
                                ]);
                            } else {
                                $query->orWhereJsonContains('custom_fields', [
                                    'filter_id' => intval($item['filter_id']),
                                    'value'     => $item['value']
                                ]);
                            }
                        }
                    });
                })
                ->when($request->get('is_random'), function ($query) {
                    $query->inRandomOrder();
                })
                ->when($request->get('latest'), function ($query) {
                    $query->latest();
                })
                ->when($request->get('sort_by'), function ($query, $sort_by) {
                    switch ($sort_by) {
                        case 'latest':
                            $query->latest();
                            break;
                        case 'oldest':
                            $query->oldest();
                            break;
                        case 'popular':
                            $query->withCount('views')
                                ->orderBy('views_count', 'desc');
                            break;
                    }
                }, function ($query) {
                    // Default sort by newest if no sort_by is provided
                    $query->latest();
                })
                ->when($request->get('manufacturing_year') && $manufacturingYearFilterId, function ($query) use ($manufacturingYearFilterId, $request) {
                    $manufacturing_year = $request->get('manufacturing_year');
                    Log::info('Manufacturing Year Filter - received value: ' . $manufacturing_year);
                    Log::info('Manufacturing Year Filter - filter ID: ' . $manufacturingYearFilterId);
                    $query->whereHas('filters', function ($q) use ($manufacturingYearFilterId, $manufacturing_year) {
                        $q->where('filter_id', $manufacturingYearFilterId)
                            ->where('value', (string)$manufacturing_year);
                    });
                })
                ->orderBy('created_at', 'desc');

            // Log the final query before execution
            Log::info('Final Advertisement Query: ' . $advertisements->toSql());
            Log::info('Final Advertisement Bindings: ' . json_encode($advertisements->getBindings()));

            if ($request->get('pagination')) {
                $advertisements = $advertisements->paginate(self::MAX_PER_PAGE);
            } else {
                $advertisements = $advertisements->get();
            }

            $advertisementsCollection = AdvertisementsResource::collection($advertisements);

            return response()->json([
                'ok'                 => true,
                'advertisements'     => $request->get('pagination') ? [
                    'data'          => $advertisementsCollection,
                    'current_page'  => $advertisements->currentPage(),
                    'last_page'     => $advertisements->lastPage(),
                    'per_page'      => $advertisements->perPage(),
                    'total'         => $advertisements->total()
                ] : $advertisementsCollection,
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
            $advertisement = Advertisement::select([
                'advertisements.*',
                $request->user() ? DB::raw('(SELECT COUNT(*) > 0 FROM favorite_advertisements WHERE favorite_advertisements.advertisements_id = advertisements.id AND favorite_advertisements.user_id = ' . $request->user()->id . ') as is_favorite') : DB::raw('false as is_favorite')
            ])
                ->where('status', Advertisement::STATUS_APPROVED)
                ->findOrFail($advertisement_id);

            if (!$request->get('with_media')) {
                $advertisement->load(['mediaRelation' => function ($q) {
                    $q->select(['id', 'file_id', 'is_main', 'advertisements_id']);
                }]);
            } else {
                $advertisement->load([
                    'media',
                ]);
            }

            $AdvertisementResource = new AdvertisementResource($advertisement);

            /**
             * Add new view or check if user already viewed this advertisement by user_id if auth()->check if not check by ip)
             */
            $advertisement->views()->updateOrCreate($request->user() ? ['user_id' => $request->user()->id] : ['ip' => $request->ip()], [
                'user_id'   => $request->user() ? $request->user()->id : null,
                'ip'        => $request->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'ok'            => true,
                'advertisement' => $AdvertisementResource
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
        \Log::info('Update Advertisement Request Body', $request->all());
        try {
            $advertisement = Advertisement::where('user_id', $request->user()->id)->findOrFail($advertisement_id);

            $data           = $request->only([
                'name',
                'description',
                'price',
                'category',
                'sub_category',
                'sub_sub_category',
                'latitude',
                'longitude',
                'country',
                'city',
                'user',
                'filters'
            ]);
            $images         = $request->get('media');

            $advertisement->update([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'category_id'       => $data['category']['id'],
                'sub_category_id'   => $data['sub_category']['id'] ?? null,
                'sub_sub_category_id' => $data['sub_sub_category']['id'] ?? null,
                'latitude'          => $data['latitude'],
                'longitude'         => $data['longitude'],
                'country_id'        => $data['country']['id'],
                'city_id'           => $data['city']['id'] ?? null,
                'country_code'      => $data['user']['country_code'],
                'phone_number'      => $data['user']['phone_number'],
                'custom_fields'     => $data['filters'] ?? [],
            ]);

            /**
             * Filters Sync
             */
            if ($request->get('filters') && count($request->get('filters'))) {
                $advertisement->filters()->detach();
                $advertisement->filters()->sync(
                    collect($request->get('filters'))->map(fn($q) => [
                        'value' => $q['value'],
                        'filter_id' => $q['filter_id']
                    ])->values()
                );
            }

            $advertisement->mediaRelation()->delete();
            $advertisement->mediaRelation()
                ->createMany(collect($images)
                    ->map(function ($item) {
                        return [
                            'file_id' => $item['id'],
                            'is_main' => $item['is_main'] ?? false
                        ];
                    }));

            return response()->json([
                'ok'            => true,
                'advertisement' => new AdvertisementResource($advertisement),
                'message'       => __('Advertisement updated successfully')
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
     * Store advertisement
     * @param AdvertisementRequest $request
     * @return JsonResponse
     */
    public function store(AdvertisementRequest $request): JsonResponse
    {
        \Log::info('Store Advertisement Request Body', $request->all());
        try {
            $data       = $request->only([
                'name',
                'description',
                'price',
                'category',
                'sub_category',
                'sub_sub_category',
                'latitude',
                'longitude',
                'country',
                'city',
                'user',
                'filters'
            ]);

            $images         = $request->get('media');

            $advertisement  = Advertisement::create([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'category_id'       => $data['category']['id'],
                'sub_category_id'   => $data['sub_category']['id'] ?? null,
                'sub_sub_category_id' => $data['sub_sub_category']['id'] ?? null,
                'latitude'          => $data['latitude'],
                'longitude'         => $data['longitude'],
                'country_id'        => $data['country']['id'],
                'city_id'           => $data['city']['id'] ?? null,
                'country_code'      => $data['user']['country_code'],
                'phone_number'      => $data['user']['phone_number'],
                'status'            => Advertisement::STATUS_APPROVED,
                'user_id'           => $request->user()->id,
                'custom_fields'     => $data['filters'] ?? [],
            ]);

            /**
             * Filters Sync
             */
            if ($request->get('filters') && count($request->get('filters'))) {
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
            $fileInfo = null;
            if ($request->has('media')) {
                $fileInfo = collect($images)->map(function ($item) use ($advertisement) {
                    $media = $advertisement->mediaRelation()->create([
                        'file_id' => $item['id'],
                        'is_main' => $item['is_main'] ?? false
                    ]);
                    return $media;
                });
            }

            /**
             * Send notification to user
             */
            try {
                dispatch(function () use ($advertisement) {
                    $matchStatus = match ($advertisement->status) {
                        Advertisement::STATUS_APPROVED  => 'ad_approved',
                        Advertisement::STATUS_REJECTED  => 'ad_rejected',
                        Advertisement::STATUS_PENDING   => 'ad_pending',
                        default => null
                    };

                    $this->notificationService->saveNotification($advertisement->user_id, null,  $matchStatus, $advertisement->id);

                    $advertisement->user->notify(new AdStore($advertisement));
                })
                    ->afterResponse()
                    ->onQueue('notifications');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            return response()->json([
                'ok'            => true,
                'advertisement' => new AdvertisementResource($advertisement),
                'file'          => $fileInfo,
                'message'       => __('Advertisement created successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message'   => __('Something went wrong, please try again later'),
                'error'     => $e->getMessage()
            ]);
        }
    }

    /**
     * Favorite advertisement
     * @param Request $request
     * @return JsonResponse
     */
    public function favoritesAdvertisements(Request $request): JsonResponse
    {
        try {
            $user        = $request->user();
            $favorite    = $user->favorites()
                ->latest();

            $favorite->selectSub(function ($query) use ($user) {
                $query->selectRaw('(COUNT(*) > 0) as is_favorite')
                    ->from('favorite_advertisements')
                    ->whereColumn('favorite_advertisements.advertisements_id', 'advertisements.id')
                    ->where(function ($query) use ($user) {
                        if ($user) {
                            $query->where('favorite_advertisements.user_id', $user->id);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                    });
            }, 'is_favorite');


            if ($request->get('pagination')) {
                $favorite = $favorite->paginate(self::MAX_PER_PAGE);
            } else {
                $favorite = $favorite->get();
            }

            $favorite = AdvertisementsResource::collection($favorite);

            return response()->json([
                'ok'        => true,
                'message'   => __('Advertisement favorites successfully'),
                'favorites' => $request->get('pagination') ? [
                    'data'          => $favorite,
                    'current_page'  => $favorite->currentPage(),
                    'last_page'     => $favorite->lastPage(),
                    'per_page'      => $favorite->perPage(),
                    'total'         => $favorite->total()
                ] : $favorite,
                'user'      => $user
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
     * Toggle favorite advertisement
     * @param int $advertisement_id
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleFavorite(Request $request, int $advertisement_id): JsonResponse
    {
        try {
            $user           = $request->user();
            $advertisement  = Advertisement::withTrashed()->findOrFail($advertisement_id);

            $isFavorite = DB::table('favorite_advertisements')
                ->where('user_id', $user->id)
                ->where('advertisements_id', $advertisement_id)
                ->exists();

            if ($isFavorite) {
                DB::table('favorite_advertisements')
                    ->where('user_id', $user->id)
                    ->where('advertisements_id', $advertisement_id)
                    ->delete();
                $notificationType = null;
            } else {
                DB::table('favorite_advertisements')->insert([
                    'user_id' => $user->id,
                    'advertisements_id' => $advertisement_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $notificationType = 'add_your_ad_to_favorites';
            }

            if ($notificationType && $advertisement->user_id !== $user->id) {
                $this->notificationService
                    ->saveNotification($advertisement->user_id, $user->id, $notificationType, $advertisement->id);
            }

            return response()->json([
                'ok' => true,
                'message' => !$isFavorite ? __('Advertisement added to favorites') : __('Advertisement removed from favorites'),
                'isFavorite' => !$isFavorite
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'response' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get advertisements for specific user
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function userAdvertisements(Request $request): JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'search'        => 'nullable|string',
                'category'      => 'integer|exists:categories,id',
                'pagination'    => 'boolean'
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'ok'        => false,
                    'message'   => __('Validation error'),
                    'errors'    => $validation->errors()
                ])->setStatusCode(422);
            }

            $advertisements = Advertisement::query()
                ->where('user_id', $request->user()->id)
                ->when($request->get('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('price', 'like', '%' . $search . '%');
                })
                ->when($request->get('category'), function ($query, $category_id) {
                    $query->where('category_id', $category_id);
                })
                ->with([
                    'category' => function ($q) {
                        $q->select(['id', 'name', 'slug']);
                    },
                    'user' => function ($q) {
                        $q->select(['id', 'name', 'email', 'country_code', 'phone_number']);
                    },
                    'mainMedia' => function ($q) {
                        $q->select(['file_name', 'path']);
                    },
                    'subCategory' => function ($q) {
                        $q->select(['id', 'name', 'slug']);
                    },
                    'country' => function ($q) {
                        $q->select(['id', 'name']);
                    },
                    'city' => function ($q) {
                        $q->select(['id', 'name']);
                    }
                ]);

            if ($request->get('pagination')) {
                $advertisements = $advertisements->paginate(self::MAX_PER_PAGE);
            } else {
                $advertisements = $advertisements->get();
            }

            $advertisements = AdvertisementsResource::collection($advertisements);

            return response()->json([
                'ok'                 => true,
                'advertisements'     => $request->get('pagination') ? [
                    'data'          => $advertisements,
                    'current_page'  => $advertisements->currentPage(),
                    'last_page'     => $advertisements->lastPage(),
                    'per_page'      => $advertisements->perPage(),
                    'total'         => $advertisements->total()
                ] : $advertisements,
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
     * Delete advertisement
     * @param int $advertisement_id
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(int $advertisement_id, Request $request): JsonResponse
    {
        $advertisement = Advertisement::where('user_id', $request->user()->id)
            ->findOrFail($advertisement_id);

        try {
            // Delete favorite advertisements
            DB::table('favorite_advertisements')
                ->where('advertisements_id', $advertisement->id)
                ->delete();

            // Delete related stories
            DB::table('advertisement_stories')
                ->where('advertisement_id', $advertisement->id)
                ->delete();

            $advertisement->delete();

            return response()->json([
                'ok'        => true,
                'message'   => __('Advertisement deleted successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * storeStory method
     * @param Request $request
     * @param Advertisement $advertisement
     * @return JsonResponse
     */
    public function storeStory(Request $request, Advertisement $advertisement): JsonResponse
    {
        if (
            (($advertisement->user_id === $request->user()->id && $request->user()->is_verified) || $request->user()->role !== 'admin') &&
            $advertisement->status !== 'approved'
        ) {
            return response()->json([
                'ok' => false,
                'message' => __('You can not add your advertisement to your story')
            ]);
        }

        try {
            $advertisement->advertisementStory()->firstOrCreate([
                'user_id'           => $request->user()->id,
                'advertisement_id'  => $advertisement->id
            ]);

            return response()->json([
                'ok' => true,
                'message' => __('Story added successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'response' => $e->getMessage()
            ]);
        }
    }

    /**
     * deleteStory method
     * @param Request $request
     * @param Advertisement $advertisement
     * @return JsonResponse
     */
    public function deleteStory(Request $request, Advertisement $advertisement): JsonResponse
    {
        if ($advertisement->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => __('You can not delete this story')
            ]);
        }

        try {
            $advertisement->advertisementStory()->where('user_id', $request->user()->id)->delete();

            return response()->json([
                'ok' => true,
                'message' => __('Story deleted successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'response' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fetch Stories By latest
     */
    public function fetchStories(Request $request): JsonResponse
    {
        try {
            $stories = AdvertisementStory::query()
                ->whereHas('advertisement', function ($q) {
                    $q->where('status', Advertisement::STATUS_APPROVED);
                })
                ->select('user_id', DB::raw('GROUP_CONCAT(id ORDER BY created_at DESC) as stories'))
                ->groupBy('user_id')
                ->orderBy(DB::raw('MAX(created_at)'), 'desc')
                ->paginate(self::MAX_PER_PAGE);



            return response()->json([
                'ok'        => true,
                'stories'   => StoriesResource::collection($stories),
                'pagination' => [
                    'current_page'  => $stories->currentPage(),
                    'last_page'     => $stories->lastPage(),
                    'per_page'      => $stories->perPage(),
                    'total'         => $stories->total()
                ]
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'response' => $e->getMessage()
            ]);
        }
    }

    /**
     * Add view to story
     * @param Request $request
     * @param AdvertisementStory $story
     * @return JsonResponse
     */
    public function addViewToStory(Request $request, AdvertisementStory $story): JsonResponse
    {
        try {
            $story->views()
                ->updateOrCreate($request->user() ? ['user_id' => $request->user()->id, 'advertisement_story_id' => $story->id] : ['ip_address' => $request->ip(), 'advertisement_story_id' => $story->id], [
                    'user_id'                   => $request->user() ? $request->user()->id : null,
                    'advertisement_story_id'    => $story->id,
                    'ip_address'    => $request->ip(),
                    'updated_at'    => now()
                ]);

            return response()->json([
                'ok' => true,
                'message' => __('Story viewed successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'response' => $e->getMessage()
            ]);
        }
    }

    /**
     * Explore advertisements
     * @param Request $request
     * @return JsonResponse
     */
    public function exploreAdvertisements(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search'            => 'string',
            'category'          => 'integer|exists:categories,id',
            'sub_category'      => 'integer|exists:sub_categories,id',
            'sub_sub_category'  => 'integer|exists:sub_sub_categories,id',
            'is_random'         => 'boolean',
            'latest'            => 'boolean',
            'pagination'        => 'boolean',
            'city_id'          => 'integer|exists:cities,id',
            'country_id'       => 'integer|exists:countries,id',
            'sort_by'          => 'string|in:latest,oldest,popular',
            'category_filter'               => 'nullable|array',
            'category_filter.*.filter_id'   => 'required|exists:filters,id',
            'category_filter.*.value'       => 'required|string',
            'manufacturing_year'            => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
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
            $userId = $request->user() ? $request->user()->id : null;

            // Get the filter ID for 'manufacturing_year' once
            $manufacturingYearFilter = Filter::where('name', 'manufacturing_year')->first();
            // If not found by 'manufacturing_year', try 'سنة الصنع'
            if (!$manufacturingYearFilter) {
                $manufacturingYearFilter = Filter::where('name', 'سنة الصنع')->first();
            }
            $manufacturingYearFilterId = $manufacturingYearFilter ? $manufacturingYearFilter->id : null;

            $advertisements = Advertisement::query()
                ->select('advertisements.*')
                ->selectSub(function ($query) use ($userId) {
                    $query->selectRaw('(COUNT(*) > 0) as is_favorite')
                        ->from('favorite_advertisements')
                        ->whereColumn('favorite_advertisements.advertisements_id', 'advertisements.id')
                        ->where(function ($query) use ($userId) {
                            if ($userId) {
                                $query->where('favorite_advertisements.user_id', $userId);
                            } else {
                                $query->whereRaw('1 = 0');
                            }
                        });
                }, 'is_favorite')
                
                ->withCount('comments')
                ->with(['category', 'subCategory', 'subSubCategory', 'mainMedia', 'user', 'country', 'city'])
                ->where('status', Advertisement::STATUS_APPROVED)
                ->when($request->get('city_id'), function ($query, $city_id) {
                    $query->where('city_id', $city_id);
                })
                ->when($request->get('country_id'), function ($query, $country_id) {
                    $query->where('country_id', $country_id);
                })
                ->when($request->get('search'), function ($query, $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%')
                          ->orWhere('price', 'like', '%' . $search . '%');
                    });
                })
                ->when($request->get('category'), function ($query, $category_id) {
                    $query->where('category_id', $category_id);
                })
                ->when($request->get('sub_category'), function ($query, $sub_category_id) {
                    $query->where('sub_category_id', $sub_category_id);
                })
                ->when($request->get('sub_sub_category'), function ($query, $sub_sub_category_id) {
                    $query->where('sub_sub_category_id', $sub_sub_category_id);
                })
                ->when($request->has('category_filter') && is_array($request->get('category_filter')) && count($request->get('category_filter')), function ($query) use ($request) {
                    collect($request->get('category_filter'))->each(function ($item, $key) use ($query) {
                        if (isset($item['filter_id']) && isset($item['value'])) {
                            if ($key === 0) {
                                $query->whereJsonContains('custom_fields', [
                                    'filter_id' => intval($item['filter_id']),
                                    'value'     => $item['value']
                                ]);
                            } else {
                                $query->orWhereJsonContains('custom_fields', [
                                    'filter_id' => intval($item['filter_id']),
                                    'value'     => $item['value']
                                ]);
                            }
                        }
                    });
                })
                ->when($request->get('is_random'), function ($query) {
                    $query->inRandomOrder();
                })
                ->when($request->get('latest'), function ($query) {
                    $query->latest();
                })
                ->when($request->get('sort_by'), function ($query, $sort_by) {
                    switch ($sort_by) {
                        case 'latest':
                            $query->latest();
                            break;
                        case 'oldest':
                            $query->oldest();
                            break;
                        case 'popular':
                            $query->withCount('views')
                                ->orderBy('views_count', 'desc');
                            break;
                    }
                }, function ($query) {
                    // Default sort by newest if no sort_by is provided
                    $query->latest();
                })
                ->when($request->get('manufacturing_year') && $manufacturingYearFilterId, function ($query) use ($manufacturingYearFilterId, $request) {
                    $manufacturing_year = $request->get('manufacturing_year');
                    $query->whereHas('filters', function ($q) use ($manufacturingYearFilterId, $manufacturing_year) {
                        $q->where('filter_id', $manufacturingYearFilterId)
                            ->where('value', (string)$manufacturing_year);
                    });
                });

            if ($request->get('pagination')) {
                $advertisements = $advertisements->paginate(self::MAX_PER_PAGE);
            } else {
                $advertisements = $advertisements->get();
            }

            $advertisementsCollection = AdvertisementsResource::collection($advertisements);

            return response()->json([
                'ok'                 => true,
                'advertisements'     => $request->get('pagination') ? [
                    'data'          => $advertisementsCollection,
                    'current_page'  => $advertisements->currentPage(),
                    'last_page'     => $advertisements->lastPage(),
                    'per_page'      => $advertisements->perPage(),
                    'total'         => $advertisements->total()
                ] : $advertisementsCollection,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }
}
