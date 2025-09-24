<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'url'       => $this->url,
            'url_thumb' => $this->url_thumb,
            'id'        => $this->id,
            'is_main'   => $this->pivot->is_main,
        ];
    }
}
