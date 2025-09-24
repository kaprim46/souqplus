<?php
namespace App\Services;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class FollowServices
{

    private NotificationServices $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationServices();
    }

    /**
     * Check if the current user follows the given user.
     *
     * @param int $followedId
     * @param string $followedAccountType
     * @return bool
     */
    public function isFollowing(int $followedId, string $followedAccountType = 'customer'): bool
    {
        $currentUserId = auth()->id();

        if (!$currentUserId) {
            return false;
        }

        return Follow::query()->where('follower_id', $currentUserId)
            ->where('followed_id', $followedId)
            ->where('follow_account_type', $followedAccountType)
            ->exists();
    }

    /**
     * Make function to follow a user if not already following but unfollow if already following
     * @param int $followedId
     * @param string $followedAccountType
     * @return JsonResponse
     */
    public function followOrUnfollow(int $followedId, string $followedAccountType): JsonResponse
    {
        $currentUserId = auth()->id();

        if (!$currentUserId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $follow = Follow::query()->where('follower_id', $currentUserId)
            ->where('followed_id', $followedId)
            ->where('follow_account_type', $followedAccountType)
            ->first();

        if ($follow) {
            $follow->delete();

            return response()->json([
                'ok'            => true,
                'isFollowing'   => false,
            ], 200);
        }

        Follow::query()->create([
            'follower_id'           => $currentUserId,
            'followed_id'           => $followedId,
            'follow_account_type'   => $followedAccountType,
        ]);

        dispatch(function () use ($followedId, $followedAccountType) {
            $this->saveFollowNotification($followedId, $followedAccountType);
        })->afterResponse();

        return response()->json([
            'ok' => true,
            'isFollowing' => true,
        ], 200);
    }

    /**
     * Save the follow notification
     * @param int $followedId
     * @param string $followedAccountType
     * @return void
     */
    public function saveFollowNotification(int $followedId, string $followedAccountType): void
    {
        $currentUserId = auth()->id();

        if (!$currentUserId) {
            return;
        }

        $followedUser = User::query()->find($followedId);

        /**
         * Create a notification if not already following and notification exists
         */
        if (
            $followedUser &&
            $this->isFollowing($followedId, $followedAccountType) &&
            !$followedUser->notifications()
                ->where('sender_id', $currentUserId)
                ->where('type', $followedAccountType === 'customer' ? 'new_follower' : 'new_store_follower')
                ->exists()
        ) {
            $this->notificationService->saveNotification($followedId, $currentUserId, $followedAccountType === 'customer' ? 'new_follower' : 'new_store_follower');
        }
    }

    /**
     * followers
     * @param User $customer
     * @param string $followedAccountType
     * @return mixed
     */
    public function followers(User $customer, string $followedAccountType = 'customer'): mixed
    {
        return $customer->followers()
            ->wherePivot('follow_account_type', $followedAccountType)
            ->get()
            ->map(function ($follower) {
                return [
                    'id' => $follower->id,
                    'name' => $follower->name,
                    'email' => $follower->email,
                    'avatar_url' => $follower->avatar_url,
                ];
            })->values();
    }

    /**
     * following
     * @param User $customer
     * @param string $followedAccountType
     * @return mixed
     */
    public function followings(User $customer, string $followedAccountType = 'customer'): mixed
    {
        return $customer->following()
            ->wherePivot('follow_account_type', $followedAccountType)
            ->get()
            ->map(function ($follower) {
                return [
                    'id' => $follower->id,
                    'name' => $follower->name,
                    'email' => $follower->email,
                    'avatar_url' => $follower->avatar_url,
                ];
            })->values();
    }
}