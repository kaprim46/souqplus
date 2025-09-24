<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdvertisementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $businessInfo = is_array($this->user->business_info)
            ? $this->user->business_info
            : (is_string($this->user->business_info) ? json_decode($this->user->business_info, true) : []);
        $isBusiness = strtolower($this->user->role) === 'business';

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'price' => $this->price,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'sub_category' => [
                'id' => $this->subCategory?->id,
                'name' => $this->subCategory?->name,
                'slug' => $this->subCategory?->slug,
            ],
            'sub_sub_category' => [
                'id' => $this->subSubCategory?->id,
                'name' => $this->subSubCategory?->name,
                'slug' => $this->subSubCategory?->slug,
            ],
            'main_media' => [
                'url' => $this->mainMedia?->url,
                'url_thumb' => $this->mainMedia?->url_thumb,
            ],
            'media' => MediaResource::collection($this->media),
            'user' => [
                'id'            => $this->user->id,
                'name'          => $this->user->name,
                'avatar_url'    => $this->user->role === 'business' ? (is_array($this->user->business_info) && isset($this->user->business_info['business_logo']) ? asset($this->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$this->user->name}&color=7F9CF5&background=EBF4FF") : $this->user->avatar_url,
                'email'         => $this->user->email ?? '--',
                'country_code'  => $this->country_code,
                'phone_number'  => $this->phone_number,
                'is_business'   => $this->user->role === 'business',
                'is_verified'   => $this->user->is_verified,
                'slug'          => Str::slug($this->user->name),
            ],
            'filters' => is_array($this->custom_fields) && count($this->custom_fields) ? collect($this->custom_fields)->map(function ($item) {
                $filter = DB::table('filters')->where('id', $item['filter_id'])->first();

                if(!$filter) {
                    return null;
                }

                return [
                    'filter_id' => $filter->id,
                    'name'      => $filter->name,
                    'value'     => $this->custom_fields ? collect($this->custom_fields)->where('filter_id', $filter->id)->first()['value'] : '',
                ];
            })->toArray() : [],
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'country' => $this->country ? [
                'id' => $this->country->id,
                'name' => $this->country->name,
            ] : null,
            'city' => $this->city ? [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ] : null,
            'is_favorite'   => $this->is_favorite,
            'views'         => !$request->user()?->isAdmin() ? $this->views->count() + $this->fake_views : $this->views->count(),
            'created_at'    => $this->created_at->diffForHumans(),
        ];

        if($this->user->id === $request->user()?->id || $request->user()?->isAdmin()) {
            $data['is_owner']   = $this->user->id === $request->user()?->id;
            $data['deleted_at'] = $this->deleted_at?->diffForHumans();
            $data['status']     = $this->status;
        }

        return $data;
    }
}

