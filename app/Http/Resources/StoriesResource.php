<?php

namespace App\Http\Resources;

use App\Models\AdvertisementStory;
use App\Models\AdvertisementStoryView;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function Laravel\Prompts\select;

class StoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $stories        = StoryResource::collection(AdvertisementStory::whereIn('id', explode(',', $this->stories))->get()) ?? [];
        $isViewedAll    = AdvertisementStoryView::whereIn('advertisement_story_id', explode(',', $this->stories))->where('user_id', $request->user()?->id)->count() === $stories->count();
        $poster_url     = collect($stories[rand(0, count($stories) - 1)])->toArray()['media']?->first()?->url_thumb ?? ( $this->user->role === 'business' ? (is_array($this->user->business_info) && isset($this->user->business_info['business_logo']) ? asset($this->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$this->user->name}&color=7F9CF5&background=EBF4FF") : $this->user->avatar_url);


        return [
            'user' => [
                'id'            => $this->user->id,
                'name'          => $this->user->role === 'business' ? (is_array($this->user->business_info) && isset($this->user->business_info['business_name']) ? $this->user->business_info['business_name'] : '--') : $this->user->name,
                'avatar_url'    => $this->user->role === 'business' ? (is_array($this->user->business_info) && isset($this->user->business_info['business_logo']) ? asset($this->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$this->user->name}&color=7F9CF5&background=EBF4FF") : $this->user->avatar_url,
                'is_business'   => $this->user->role === 'business',
                'is_verified'   => $this->user->is_verified,
                'slug'          => Str::slug($this->user->role === 'business' ? (is_array($this->user->business_info) && isset($this->user->business_info['business_name']) ? $this->user->business_info['business_name'] : '--') : $this->user->name),
            ],
            'is_viewed_all'       => $isViewedAll,
            'poster_url'          => $poster_url,
            'stories'             => $stories,
            'is_owner'            => $request->user()?->id === $this->user_id,
        ];
    }
}
