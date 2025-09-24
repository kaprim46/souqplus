<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            "action"        => $this->type,
            "message"       => $this->generateMessage($this->type),
            "is_read"       => $this->read,
            "sender"        => $this->sender?->id  ? [
                "id"            => $this->sender->id,
                "name"          => $this->sender->name,
                "email"         => $this->sender->email,
                "country_code"  => $this->sender->country_code,
                "phone_number"  => $this->sender->phone_number,
                "avatar_url"    => $this->sender->avatar_url
            ] : null,
            "date_ago"      => $this->created_at->diffForHumans(),
            "date"          => $this->created_at->format('Y-m-d H:i:s'),
            'date_time'     => $this->created_at->timestamp,
        ];


        if($this->type !== 'new_follower' && $this->type !== 'new_store_follower' && $this->type !== 'admin_notification' && $this->ad?->id) {
           $data["ad"] = [
                "id"                => $this->ad->id,
                "name"              => $this->ad->name,
                "slug"              => $this->ad->slug,
                "clean_description" => $this->ad->clean_description
           ];
        }

        return $data;
    }

    /**
     * Generate message for notification
     * @param string $type
     * @return string
     */
    private function generateMessage(string $type): string
    {
        return match ($type) {
            'add_your_ad_to_favorites'  => __("Your ad has been added to favorites :ad by :sender", ['ad' => $this->ad->name, 'sender' => $this->sender?->name ?? 'Guest']),
            'new_store_follower'        => __("You have a new store follower :sender", ['sender' => $this->sender?->name ?? 'Guest']),
            'followed_business_ad'      => __("Business :sender has posted a new ad", ['sender' => $this->sender?->name ?? 'Guest']),
            'admin_notification'        => __("Administration<br />: :body", ['body' => $this->message]),
            'followed_user_ad'          => __("User :sender has posted a new ad", ['sender' => $this->sender?->name ?? 'Guest']),
            'new_follower'              => __("You have a new follower :sender", ['sender' => $this->sender?->name ?? 'Guest']),
            'ad_approved'               => __("Your ad has been approved :ad", ['ad' => $this->ad->name]),
            'ad_rejected'               => __("Your ad has been rejected :ad", ['ad' => $this->ad->name]),
            'new_comment'               => __("New comment on your ad :ad", ['ad' => $this->ad->name]),
            'ad_pending'                => __("Your ad is pending :ad", ['ad' => $this->ad->name]),
            default => "New notification",
        };
    }
}
