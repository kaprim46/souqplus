<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdvertisementRequest;
use App\Http\Requests\MobileAdvertisementRequest;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use App\Models\File;
use App\Notifications\AdStore;
use App\Services\FileServices;
use App\Services\NotificationServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{

    public function __construct(protected FileServices $fileServices, protected NotificationServices $notificationService)
    {
        $this->fileServices         = $fileServices;
        $this->notificationService  = $notificationService;
    }

    /**
     * Store advertisement
     * @param MobileAdvertisementRequest $request
     * @return JsonResponse
     */
    public function store(MobileAdvertisementRequest $request): JsonResponse
    {
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

            $main_image     = $request->file('main_image');
            $images         = $request->file('images');

            $advertisement  = Advertisement::create([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'] ?? null,
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
            $fileInfo = [];
            if($request->hasFile('main_image')) {
                $mainResponse = $this->fileServices->upload($main_image, 'media', false, true, 350, 350);

                if($mainResponse['ok']) {
                    $file = File::create($mainResponse['data']);
                    if($file) {
                        $media = $advertisement->mediaRelation()->create([
                            'file_id' => $file->id,
                            'is_main' => true
                        ]);
                        $fileInfo[] = $media;
                    }
                }
            }

            if($request->hasFile('images')) {
                foreach ($images as $image) {
                    $multiResponse = $this->fileServices->upload($image, 'media', false, true, 350, 350);

                    if($multiResponse['ok']) {
                        $file = File::create($multiResponse['data']);
                        if($file) {
                            $media = $advertisement->mediaRelation()->create([
                                'file_id' => $file->id,
                                'is_main' => false
                            ]);
                            $fileInfo[] = $media;
                        }
                    }
                }
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
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Update advertisement
     * @param Advertisement $advertisement
     * @param MobileAdvertisementRequest $request
     * @return JsonResponse
     */
    public function update(Advertisement $advertisement, MobileAdvertisementRequest $request): JsonResponse
    {
        try {
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

            $main_image     = $request->file('main_image');
            $images         = $request->file('images');

            $advertisement->update([
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'] ?? null,
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

            /**
             * Upload images
             */
            if($request->hasFile('main_image')) {
                $mainResponse = $this->fileServices->upload($main_image, 'media', false, true, 350, 350);

                if($mainResponse['ok']) {
                    $file = File::create($mainResponse['data']);

                    if($file) {
                        $advertisement->mediaRelation()->where('is_main', true)->delete();
                        $advertisement->mediaRelation()->create([
                            'file_id'       => $file->id,
                            'is_main'      => true
                        ]);
                    }
                }
            }

            if($request->hasFile('images')) {
                $advertisement->mediaRelation()->where('is_main', false)->delete();
                foreach ($images as $image) {
                    $multiResponse = $this->fileServices->upload($image, 'media', false, true, 350, 350);

                    if($multiResponse['ok']) {
                        $file = File::create($multiResponse['data']);

                        if($file)
                            $advertisement->mediaRelation()->create([
                                'file_id'       => $file->id,
                                'is_main'      => false
                            ]);
                    }
                }
            }

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
}
