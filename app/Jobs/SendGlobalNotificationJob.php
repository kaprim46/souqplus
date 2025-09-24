<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Setting;
use App\Services\NotificationServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendGlobalNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected NotificationServices $notificationService;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data)
    {
        $this->notificationService = app(NotificationServices::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * Get app settings for logo and address
         */
        $logoFile = Setting::get('app_logo', 'logo.png');
        $appName = Setting::get('app_name', 'SooqPlus');
        $appUrl = Setting::get('app_url', 'https://sooqplus.net/');
        $appEmail = Setting::get('app_email', 'support@sooqplus.net');
        $appWhatsapp = Setting::get('app_whatsapp', '+966 53 860 8467');

        $logoUrl = asset('storage/logo/' . $logoFile);

        /**
         * Prepare notification data with logo and address
         */
        $notificationBody = $this->data['body'] . "\n\n" .
                           "📱 " . $appName . "\n" .
                           "🌐 " . $appUrl . "\n" .
                           "📧 " . $appEmail . "\n" .
                           "📞 " . $appWhatsapp;

        /**
         * Sent notification to all users
         */
        $users = User::query()->when($this->data['role'] !== 'all', function($q) {
            $q->where('role', $this->data['role']);
        })->get()->pluck('id')->values();

        $notificationTitle = $this->data['title'];

        $users->each(function ($userId) use ($notificationTitle, $notificationBody, $logoUrl) {
            $this->notificationService->saveNotification(
                $userId,
                null, 
                'admin_notification', 
                $notificationTitle, 
                null, 
                $notificationBody, 
                $logoUrl 
            );
        });
    }
}