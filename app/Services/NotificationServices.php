<?php

namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Jobs\FirebaseNotificationProcess;
use App\Models\UserNotification;

class NotificationServices
{
    /**
     * Save a notification for a user.
     *
     * @param int $userId
     * @param int|null $senderId
     * @param string $type
     * @param string|null $title 
     * @param int|null $adId
     * @param string|null $text
     * @param string|null $logoUrl
     * @return UserNotification|bool
     */
    public function saveNotification(int $userId, ?int $senderId, string $type, ?string $title, ?int $adId = null, ?string $text = null, ?string $logoUrl = null): UserNotification|bool
    {
        $message = $type === 'admin_notification' ? $text : $this->generateMessage($type);

        $create = UserNotification::create([
            'user_id'       => $userId,
            'sender_id'     => $senderId,
            'type'          => $type,
            'title'        => $title,
            
            'message'       => $message,
            'logo'          => $logoUrl,
            'ad_id'         => $adId,
        ]);

        if(!$create) {
            return false;
        }

        if($adId) {
            $create->load(['ad' => fn($q) => $q->select(['id', 'name', 'slug'])]);
        }

        if($senderId) {
            $create->load(['sender' => fn($q) => $q->select(['id', 'name', 'email'])]);
        }

        $ResourceNotification = NotificationResource::make($create->toArray());


        FirebaseNotificationProcess::dispatch((array) $ResourceNotification->resource)->onQueue('firebase')->afterResponse();

        return $create;
    }

    /**
     * Generate message based on the notification type.
     *
     * @param string $type
     * @return string
     */
    protected function generateMessage(string $type): string
    {
        return match ($type) {
            'add_your_ad_to_favorites'      => 'notification.add_your_ad_to_favorites',
            'followed_business_ad'          => 'notification.followed_business_ad',
            'new_store_follower'            => 'notification.new_store_follower',
            'followed_user_ad'              => 'notification.followed_user_ad',
            'new_follower'                  => 'notification.new_follower',
            'ad_approved'                   => 'notification.ad_approved',
            'ad_rejected'                   => 'notification.ad_rejected',
            'new_comment'                   => 'notification.new_comment',
            'ad_pending'                    => 'notification.ad_pending',
            default => '',
        };
    }
}
