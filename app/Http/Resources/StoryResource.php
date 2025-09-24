<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /*
       ** Get All Viewed to the story the logic is work without any problem you can use it if you want to get all viewed users to the story
       if($request->user()?->id === $this->user_id) {
            $response['viewed_by'] = $this->views?->with('user')
                ->where('advertisement_story_id', $this->id)
                ->get()?->map(function($view) {
                return $view->user?->id ? [
                    'id'            => $view->user->id,
                    'name'          => $view->user->name,
                    'avatar_url'    => $view->user->avatar_url,
                    'viewed_at'     => $view->created_at->diffForHumans(),
                ] : [
                    'id'            => null,
                    'name'          => __('GUEST'),
                    'avatar_url'    => 'https://ui-avatars.com/api/?name=GUEST&color=7F9CF5&background=EBF4FF',
                    'viewed_at'     => $view->created_at->diffForHumans(),
                ];
            });
        }*/

        return [
            'id'            => $this->id,
            'advertisement_id' => $this->advertisement->id,
            'views'         => $this->views?->where('advertisement_story_id', $this->id)->count() ?? 0,
            'is_viewed'     => $this->views?->where('user_id', auth()?->id())?->exists() ?? false,
            'media'         => MediaResource::collection($this->advertisement->media),
            'main_media'    => [
                'url' => $this->advertisement->mainMedia?->url ?? $this->advertisement->media->first()?->url ?? null
            ],
            'advertisement' => AdvertisementsResource::make($this->advertisement),
        ];
    }
}
