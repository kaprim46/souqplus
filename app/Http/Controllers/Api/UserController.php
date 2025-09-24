<?php

namespace App\Http\Controllers\Api;

use App\Notifications\VerificationUserNotification;
use App\Notifications\ForgotPasswordNotification;
use App\Http\Resources\NotificationResource;
use App\Notifications\RegisterNotification;
use Illuminate\Support\Facades\Validator;
use libphonenumber\NumberParseException;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use App\Http\Resources\MeResource;
use Illuminate\Http\JsonResponse;
use App\Services\FollowServices;
use Illuminate\Http\Request;
use App\Models\User;
use Throwable;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    const MAX_PER_PAGE = 15;

    public function __construct(protected  FollowServices $followService)
    {
        $this->followService = $followService;
    }

    /**
     * Create User
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validateUser = Validator::make($request->only(['name', 'email', 'password', 'phone_number', 'country_code', 'business_info', 'role', 'country_id', 'city_id']), [
            'role'          => 'required|in:business,customer',
            'phone_number'  => [
                'nullable',
                'string',
                'max:255',
                'unique:users,phone_number',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $phoneNumberUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneNumberUtil->parse("{$request->get('country_code')}{$value}", null);

                            if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                $fail(__('Invalid phone number'));
                            }
                        } catch (NumberParseException $e) {
                            $fail($e->getMessage());
                        }
                    }
                },
            ],
            'country_id'    => 'nullable|required_if:role,business|exists:countries,id',
            'city_id'       => 'nullable|required_if:role,business|exists:cities,id',
            'business_info' => 'required_if:role,business',
            'business_info.business_type' => 'required_if:role,business',
            'business_info.business_name' => 'required_if:role,business',
            'business_info.business_district' => 'required_if:role,business',
            'business_info.business_email' => 'nullable|email',
            'business_info.country_code' => 'nullable',
            'business_info.phone_number' => 'nullable|string|max:255',
            ...(new RegisterRequest())->rules()
        ], [
            'role.required' => __('Role is required'),
            'role.in' => __('Role must be business or customer'),
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        $phoneNumber = $request->get('phone_number');
        /**
         * Check if phone number start with 0 remove it
         */
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        $generateOtpCode = function () {
            do {
                $otpCode = mt_rand(100000, 999999);
            } while (User::where('otp_code', $otpCode)->exists());

            return $otpCode;
        };

        try {
            $user = User::create([
                'name'          => $request->get('name'),
                'email'         => $request->get('email'),
                'password'      => Hash::make($request->get('password')),
                'phone_number'  => $phoneNumber,
                'country_code'  => $request->get('country_code'),
                'role'          => $request->get('role'),
                'business_info' => $request->get('business_info'),
                'country_id'    => $request->get('country_id') ?? null,
                'city_id'       => $request->get('city_id') ?? null,
                'otp_code'      => $generateOtpCode()
            ]);

            try {
                dispatch(function () use ($user) {
                    $user->notify(new RegisterNotification($user));
                    $user->notify(new VerificationUserNotification($user));
                })
                    ->afterResponse()
                    ->onQueue('notifications');
            } catch (Throwable $th) {
                Log::error($th->getMessage());
            }

            return response()->json([
                'ok'        => true,
                'message'   => __('You are registered successfully'),
                'token'     => $user->createToken("token")->plainTextToken,
                'user'      => $user->only(['id', 'uuid', 'name', 'email', 'role', 'is_verified', 'avatar_url'])
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login User
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'phone_number' => [
                        'required',
                        'string',
                        'max:255',
                        function ($attribute, $value, $fail) use ($request) {
                            $phoneNumberUtil = PhoneNumberUtil::getInstance();
                            try {
                                $value = str_starts_with($value, '0') ? substr($value, 1) : $value;

                                $phoneNumber = $phoneNumberUtil->parse("{$request->get('country_code')}{$value}", null);

                                if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                    $fail(__('Invalid phone number'));
                                }
                            } catch (NumberParseException $e) {
                                $fail('Invalid phone number: ' . $e->getMessage());
                            }
                        },
                    ],
                    'country_code' => ['required', 'string', 'max:255'],
                    'password'  => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            /***
             * Check if phone number start with 0 remove it
             */
            $phoneNumber = $request->get('phone_number');
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = substr($phoneNumber, 1);
            }

            $user = User::where('phone_number', $phoneNumber)
                ->where('country_code', $request->get('country_code'))
                ->first();

            if (
                !$user ||
                !Hash::check($request->get('password'), $user->password)
            ) {
                return response()->json([
                    'ok'        => false,
                    'message'   => __('Login failed')
                ], 201);
            }

            return response()->json([
                'ok'        => true,
                'message'   => __('You are logged in'),
                'token'     => $user->createToken("token")->plainTextToken,
                'user'      => (new MeResource($user))
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * forgotPassword User
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'phone_number' => [
                        'required',
                        'string',
                        'max:255',
                        'exists:users,phone_number',
                        function ($attribute, $value, $fail) use ($request) {
                            $phoneNumberUtil = PhoneNumberUtil::getInstance();
                            try {
                                $phoneNumber = $phoneNumberUtil->parse("{$request->get('country_code')}{$value}", null);

                                if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                    $fail(__('Invalid phone number'));
                                }
                            } catch (NumberParseException $e) {
                                $fail('Invalid phone number: ' . $e->getMessage());
                            }
                        },
                    ],
                    'country_code' => ['required', 'string', 'max:255'],
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $user = User::where('phone_number', $request->get('phone_number'))
                ->where('country_code', $request->get('country_code'))
                ->first();

            if (!$user) {
                return response()->json([
                    'ok'        => false,
                    'message'   => __('User not found')
                ], 404);
            }

            dispatch(function () use ($user) {
                $user->notify(new ForgotPasswordNotification($user));
            })
                ->afterResponse()
                ->onQueue('notifications');

            return response()->json([
                'ok'        => true,
                'message'   => __('Password reset code sent successfully')
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * resetPassword
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'phone_number' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:users,phone_number',
                    function ($attribute, $value, $fail) use ($request) {
                        $phoneNumberUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneNumberUtil->parse("{$request->get('country_code')}{$value}", null);

                            if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                $fail('Invalid phone number');
                            }
                        } catch (NumberParseException $e) {
                            $fail('Invalid phone number: ' . $e->getMessage());
                        }
                    },
                ],
                'country_code' => ['required', 'string', 'max:255'],
                'code'          => 'required|numeric|min:100000|max:999999',
                'password'      => 'required|confirmed'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        $user = User::where('phone_number', $request->get('phone_number'))
            ->where('country_code', $request->get('country_code'))
            ->where('forget_password_code', $request->get('code'))
            ->first();

        if (!$user) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Code invalid or expired please contact support')
            ], 200);
        }

        try {
            $user->update([
                'password'              => Hash::make($request->get('password')),
                'forget_password_code'  => null
            ]);

            return response()->json([
                'ok'        => true,
                'message'   => __('Password reset successfully')
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Sent Verification otp_code to user email
     * @param Request $request
     * @return JsonResponse
     */
    public function sendVerificationCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);


        $user = User::where('email', $request->user() ? $request->user()->email : $request->get('email'))->first();

        if ($user->email_verified_at) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Your account is already verified')
            ], 400);
        }

        $generateOtpCode = function () {
            do {
                $otpCode = mt_rand(100000, 999999);
            } while (User::where('otp_code', $otpCode)->exists());

            return $otpCode;
        };

        try {
            $user->update([
                'otp_code' => $generateOtpCode()
            ]);

            $user->notify(new VerificationUserNotification($user));

            return response()->json([
                'ok'        => true,
                'message'   => __('Verification code sent successfully')
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Verify User otp_code
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'otp_code' => 'required|numeric',
                'email'    => 'required|email'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        $user = User::where('email', $request->user() ? $request->user()->email : $request->get('email'))->whereNotNull('otp_code')->where('otp_code', $request->get('otp_code'))->first();

        if (!$user) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid otp code')
            ], 400);
        }

        try {
            $user->update([
                'otp_code'          => null,
                'email_verified_at'  => now()
            ]);

            return response()->json([
                'ok'        => true,
                'message'   => __('User verified successfully'),
                'user'      => (new MeResource($user))
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Me User
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'ok'        => true,
                'message'   => __('User profile'),
                'user'      => (new MeResource($user))
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update Password
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'current_password'  => 'required',
                    'password'          => 'required|confirmed'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->get('current_password'), $user->password)) {
                return response()->json([
                    'ok' => false,
                    'message' => __('Current password is incorrect')
                ], 401);
            }

            $user->update([
                'password' => Hash::make($request->get('password'))
            ]);

            return response()->json([
                'ok'        => true,
                'message'   => __('Successfully updated password'),
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update Profile
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        \Log::info('=== Starting Profile Update ===');
        \Log::info('Request Data:', $request->except(['avatar'])); // Log all except file data
        
        try {
            // 1. First, validate the request
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $request->user()->id,
                'phone_number' => 'required|string|max:255|unique:users,phone_number,' . $request->user()->id,
                'country_code' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'bio' => 'nullable|string|max:255',
            ]);

            if ($validateUser->fails()) {
                \Log::error('Validation failed', ['errors' => $validateUser->errors()->all()]);
                return response()->json([
                    'ok' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $user = $request->user();
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone_number' => $request->phone_number,
                'bio' => $request->bio
            ];

            \Log::info('Processing avatar upload', [
                'has_file' => $request->hasFile('avatar'),
                'file_valid' => $request->file('avatar') ? $request->file('avatar')->isValid() : false
            ]);

            // 2. Handle file upload if present
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $file = $request->file('avatar');
                
                \Log::info('File details', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ]);

                // Create directory if it doesn't exist
                $directory = 'avatars';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory, 0755, true);
                    \Log::info('Created directory: ' . $directory);
                }

                // Generate unique filename
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());
                $path = $file->storeAs($directory, $filename);
                
                \Log::info('File stored', [
                    'filename' => $filename,
                    'path' => $path,
                    'full_path' => storage_path('app/' . $path),
                    'exists' => Storage::exists($path)
                ]);

                // Delete old avatar if exists
                if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                    $deleted = Storage::delete('public/avatars/' . $user->avatar);
                    \Log::info('Deleted old avatar', [
                        'path' => 'public/avatars/' . $user->avatar,
                        'success' => $deleted
                    ]);
                }

                $updateData['avatar'] = $filename;
                \Log::info('Avatar will be updated in database', ['avatar' => $filename]);
            }

            // 3. Update user data
            \Log::info('Updating user with data:', $updateData);
            $user->update($updateData);

            // 4. Verify the update
            $user->refresh();
            \Log::info('User updated successfully', [
                'avatar_path' => $user->avatar,
                'avatar_url' => $user->avatar ? Storage::url('avatars/' . $user->avatar) : null,
                'storage_path' => $user->avatar ? storage_path('app/public/avatars/' . $user->avatar) : null
            ]);

            // 5. Verify file exists on disk
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                \Log::info('Avatar file verified on disk', [
                    'size' => Storage::size('public/avatars/' . $user->avatar)
                ]);
            } else if ($user->avatar) {
                \Log::error('Avatar file not found on disk', [
                    'expected_path' => storage_path('app/public/avatars/' . $user->avatar)
                ]);
            }

            return response()->json([
                'ok' => true,
                'message' => __('Profile updated successfully'),
                'user' => (new MeResource($user))
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Profile update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'ok' => false,
                'message' => 'An error occurred while updating the profile',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Delete User
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();


            $user->advertisements()->delete();
            $user->delete();

            return response()->json([
                'ok'        => true,
                'message'   => __('User deleted successfully'),
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Me Notifications
     * @param Request $request
     * @return JsonResponse
     */
    public function notifications(Request $request): JsonResponse
    {
        try {
            $notifications = $request->user()
                ->notifications()
                ->latest()
                ->paginate(self::MAX_PER_PAGE);

            return response()->json([
                'ok'            => true,
                'message'       => __('Notifications'),
                'notifications' => NotificationResource::collection($notifications),
                'unread'        => $request->user()->unreadNotifications()->count(),
                'pagination' => [
                    'total' => $notifications->total(),
                    'count' => $notifications->count(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'total_pages' => $notifications->lastPage(),
                    'next_page_url' => $notifications->nextPageUrl(),
                    'prev_page_url' => $notifications->previousPageUrl(),
                    'last_page' => $notifications->lastPage(),
                ]
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Mark Notification as Read
     * @param Request $request
     * @return JsonResponse
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $request->user()->notifications()->update(['read' => true]);

            return response()->json([
                'ok'        => true,
                'message'   => __('Notification marked as read'),
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'ok'        => false,
                'message'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific user
     * @param User $customer
     * @return JsonResponse
     */
    public function show(User $customer): JsonResponse
    {
        if (!$customer->isCustomer() && !$customer->isAdmin()) {
            return response()->json([
                'ok'      => false,
                'message' => __('Error, Not found'),
            ], 404);
        }


        $customer->load([
            'badges' => function ($query) {
                $query->with(['badge:id,name,icon'])
                    ->select(['id', 'badge_id', 'user_id']);
            },
        ]);

        $customer->loadCount([
            'followers' => function ($query) {
                $query->where('follow_account_type', 'customer');
            },
            'following' => function ($query) {
                $query->where('follow_account_type', 'customer');
            },
        ]);

        $customer->makeHidden([
            'role',
            'avatar',
            'phone_number_verified_at',
            'deleted_at',
            'created_at',
            'updated_at',
            'business_info'
        ]);

        $customerClone =  $customer->toArray();

        if (count($customer->badges)) {
            $customerClone['badges'] = $customer->badges->map(function ($badge) {
                return $badge->badge->only(['name', 'icon_url']);
            })->toArray();
        }

        /**
         * Check if the authenticated user is following the user profile
         */
        $isFollowing                    = $this->followService->isFollowing($customer->id);
        $customerClone['is_following']  = $isFollowing;


        try {
            return response()->json([
                'ok'   => true,
                'user' => $customerClone,
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
     * Follow or Unfollow a user
     * @param User $customer
     * @param Request $request
     * @return JsonResponse
     */
    public function followOrUnfollow(User $customer, Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:customer,business'
        ]);

        try {
            return $this->followService->followOrUnfollow($customer->id, $request->get('type'));
        } catch (Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => __('Something went wrong, please try again later'),
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Followers of a user
     * @param User $customer
     * @return JsonResponse
     */
    public function followers(User $customer): JsonResponse
    {
        try {
            $followers = $this->followService->followers($customer);

            return response()->json([
                'ok'        => true,
                'followers' => $followers,
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
     * Followings of a user
     * @param User $customer
     * @return JsonResponse
     */
    public function followings(User $customer): JsonResponse
    {
        try {
            $followings = $this->followService->followings($customer);

            return response()->json([
                'ok'        => true,
                'followings' => $followings,
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
     * Update the user's FCM token
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string|max:255',
        ]);

        try {
            $user = $request->user();
            $user->fcm_token = $request->input('fcm_token');
            $user->save();

            return response()->json([
                'ok' => true,
                'message' => __('FCM token updated successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Failed to update FCM token'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
