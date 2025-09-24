<?php

namespace App\Jobs;

use App\Models\Advertisement;
use App\Models\User;
use App\Services\NotificationServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewCommentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Advertisement $advertisement
     * @param User $user
     */
    public function __construct(protected Advertisement $advertisement, protected User $user)
    {
        $this->advertisement = $advertisement;
        $this->user         = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(!$this->advertisement->user || !$this->user->id) {
            Log::error('User or advertisement not found');
            return;
        }

        $notificationService    = app(NotificationServices::class);

        $notificationService->saveNotification(
            $this->advertisement->user_id,
            $this->user->id,
            'new_comment',
            $this->advertisement->id
        );
    }
}