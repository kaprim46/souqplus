<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AdvertisementsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->clean_description,
            'slug'          => $this->slug,
            'price'         => $this->price,
            'category' => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'slug'  => $this->category->slug,
            ],
            'sub_category' => [
                'id'    => $this->subCategory?->id,
                'name'  => $this->subCategory?->name,
                'slug'  => $this->subCategory?->slug,
            ],
            'sub_sub_category' => [
                'id'    => $this->subSubCategory?->id,
                'name'  => $this->subSubCategory?->name,
                'slug'  => $this->subSubCategory?->slug,
            ],
            'main_media' => [
                'url'           => $this->mainMedia?->url,
                'url_thumb'     => $this->mainMedia?->url_thumb,
            ],
            'user' => [
                'id'            => $this->user->id,
                'name'          => $this->user->name,
                'avatar_url'    => $this->user->role === 'business'
                    ? (is_array($this->user->business_info) && isset($this->user->business_info['business_logo']) ? asset($this->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$this->user->name}&color=7F9CF5&background=EBF4FF")
                    : $this->user->avatar_url,
                'email'         => $this->user->email ?? '--',
                'country_code'  => $this->country_code,
                'phone_number'  => $this->phone_number,
                'is_business'   => $this->user->role === 'business',
                'is_verified'   => $this->user->is_verified,
                'slug'          => Str::slug($this->user->name),
            ],
            'country'           => $this->country ? ['id' => $this->country->id, 'name' => $this->country->name] : null,
            'city'              => $this->city ? ['id' => $this->city->id, 'name' => $this->city->name] : null,
            'comments_count'    => $this->comments_count,
            'is_favorite'       => $this->is_favorite,
            'views'             => !$request->user()?->isAdmin() ? $this->views->count() + $this->fake_views: $this->views->count(),
            'created_at'        => $this->created_at->diffForHumans(),
        ];

        if($request->user()?->isAdmin()) {
            $data['deleted_at']             = $this->deleted_at?->diffForHumans();
            $data['status']                 = $this->status;
        }

        if($this->user->id === $request->user()?->id) {
            $data['is_owner']       = true;
            $data['status']         = $this->status;
            $data['post_as_story']  = $this->advertisementStory?->where('advertisement_id', $this->id)->exists();
        }

        return $data;
    }
}
