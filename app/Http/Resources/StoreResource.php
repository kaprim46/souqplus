<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'business_name'             => $this->business_info['business_name'] ?? '',
            'business_type'             => $this->business_info['business_type'] ?? '',
            'business_district'         => $this->business_info['business_district'] ?? '',
            'business_location'         => isset($this->business_info['business_location']) ? json_decode($this->business_info['business_location'], true) : [],
            'business_bio'              => $this->business_info['business_bio'] ?? '',
            'business_cover_url'        => isset($this->business_info['business_cover']) && $this->business_info['business_cover'] ? asset("storage/{$this->business_info['business_cover']}") : '',
            'business_logo_url'         => isset($this->business_info['business_logo']) && $this->business_info['business_logo'] ? asset("storage/{$this->business_info['business_logo']}") : '',
            'business_email'            => $this->business_info['business_email'] ?? '',
            'business_slug'             => Str::slug($this->business_info['business_name'] ?? ''),
            "business_is_verified"      => $this->is_verified,
            'phone_number'              => $this->business_info['phone_number'] ?? '',
            'country_code'              => $this->business_info['country_code'] ?? '',
            'is_following_store'        => $this->is_following_store,
            'followers'                 => $this->followers_count,
            'advertisements'            => $this->advertisements_count,
            'country'            => $this->country ? [
                'id'                => $this->country->id,
                'name'              => $this->country->name,
            ] : null,
            'city'               => $this->city ? [
                'id'                => $this->city->id,
                'name'              => $this->city->name,
            ] : null,
        ];
    }
}
