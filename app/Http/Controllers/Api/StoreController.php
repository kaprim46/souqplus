<?php

namespace App\Http\Controllers\Api;

use libphonenumber\NumberParseException;
use App\Http\Resources\StoreResource;
use App\Http\Controllers\Controller;
use libphonenumber\PhoneNumberUtil;
use Illuminate\Http\JsonResponse;
use App\Services\FollowServices;
use Illuminate\Http\Request;
use App\Models\User;
use Throwable;

class StoreController extends Controller
{
    public function __construct(protected  FollowServices $followService)
    {
        $this->followService = $followService;
    }

    /**
     * Display a specific store
     * @param User $store
     * @return JsonResponse
     */
    public function show(User $store): JsonResponse
    {
        if (!$store->isStore()) {
            return response()->json([
                'ok'      => false,
                'message' => __('Error, Not found'),
            ], 404);
        }

        $store->loadCount([
            'followers' => function ($query) {
                $query->where('follow_account_type', 'business');
            },
            'advertisements' => function ($query) {
                $query->where('status', 'approved');
            }
        ]);


        $store->makeHidden(['role', 'avatar', 'phone_number_verified_at',  'deleted_at', 'created_at', 'updated_at']);


        /**
         * Check if the authenticated user is following the user store business
         */
        $isFollowingStore               = $this->followService->isFollowing($store->id, 'business');
        $store->is_following_store      = $isFollowingStore;

        try {
            return response()->json([
                'ok'   => true,
                'store' => (new StoreResource($store))
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => __('Something went wrong, please try again later'),
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * On Update store
     */
    public function onUpdate(Request $request): JsonResponse
    {
        $rules = [
            'business_name'             => 'required',
            'business_type'             => 'required',
            'business_location'         => 'nullable',
            'business_district'         => 'nullable',
            'business_bio'              => 'nullable',
            'business_email'            => 'nullable|email',
            'phone_number'              => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->get('country_code')) {
                        $phoneNumberUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneNumberUtil->parse("{$request->get('country_code')}{$value}", null);

                            if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                $fail('Invalid phone number');
                            }
                        } catch (NumberParseException $e) {
                            $fail($e->getMessage());
                        }
                    }
                },
            ],
            'country_code'              => 'nullable',
            'country_id'                => 'required|exists:countries,id',
            'city_id'                   => 'required|exists:cities,id',
        ];

        if ($request->hasFile('business_logo'))
            $rules['business_logo'] = 'required|image';

        if ($request->hasFile('cover_image'))
            $rules['cover_image'] = 'required|image';

        $validation = \Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validation->errors()
            ], 422);
        }

        $data = collect($validation->valid())->except(['cover_image', '_method']);

        if ($request->hasFile('business_logo')) {
            $data['business_logo'] = $request->file('business_logo')->store('business_assets', 'public');
        } else if (isset(auth()->user()->business_info['business_logo'])) {
            $data['business_logo'] = auth()->user()->business_info['business_logo'];
        } else {
            // do not update business_logo if not provided (or not present in request)
            unset($data['business_logo']);
        }

        if ($request->hasFile('cover_image')) {
            $data['business_cover'] = $request->file('cover_image')->store('business_assets', 'public');
        } else if (isset(auth()->user()->business_info['business_cover'])) {
            $data['business_cover'] = auth()->user()->business_info['business_cover'];
        } else {
            // do not update business_cover if not provided (or not present in request)
            unset($data['business_cover']);
        }

        $updateData = [];
        if (isset($data['country_id'])) {
             $updateData['country_id'] = $data['country_id'];
        }
        if (isset($data['city_id'])) {
             $updateData['city_id'] = $data['city_id'];
        }
        if (count($data) > 0) {
             $updateData['business_info'] = $data;
        }
        auth()->user()->update($updateData);

        return response()->json([
            'ok' => true,
            'message' => __('Settings updated successfully')
        ]);
    }
}
