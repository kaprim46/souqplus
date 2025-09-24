<?php

namespace App\Jobs;

use App\Models\Advertisement;
use App\Services\FollowServices;
use App\Services\NotificationServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AdsApprovedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Advertisement $advertisement;

    /**
     * Create a new job instance.
     *
     * @param Advertisement $advertisement
     */
    public function __construct(Advertisement $advertisement)
    {
        $this->advertisement = $advertisement;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(!$this->advertisement->user) {
            Log::error('User not found for advertisement', ['advertisement_id' => $this->advertisement->id]);
            return;
        }

        $followService          = app(FollowServices::class);
        $notificationService    = app(NotificationServices::class);

        $this->notifyFollowers($followService, $notificationService, 'customer', 'followed_user_ad');

        if ($this->advertisement->user->role === 'business') {
            $this->notifyFollowers($followService, $notificationService, 'business', 'followed_business_ad');
        }
    }

    /**
     * Notify followers based on their type.
     *
     * @param FollowServices $followService
     * @param NotificationServices $notificationService
     * @param string $followerType
     * @param string $notificationType
     */
    protected function notifyFollowers(FollowServices $followService, NotificationServices $notificationService, string $followerType, string $notificationType): void
    {
        $followers = $followService->followers($this->advertisement->user, $followerType);

        foreach ($followers as $follower) {
            $notificationService->saveNotification(
                $follower['id'],
                $this->advertisement->user_id,
                $notificationType,
                $this->advertisement->id
            );
        }
    }
}