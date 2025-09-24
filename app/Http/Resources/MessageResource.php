<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'current_page'  => $this->currentPage(),
            'data'          => collect($this->items())->map(function($message) use ($request) {
                return [
                    'id'            => $message->id,
                    'body'          => $message->body,
                    'is_sender'     => $message->sender_id == $request->user()->id,
                    'created_at'    => $message->created_at->fromNow()
                ];
            }),
            'first_page_url'    => $this->url(1),
            'from'              => $this->firstItem(),
            'last_page'         => $this->lastPage(),
            'last_page_url'     => $this->url($this->lastPage()),
            'next_page_url'     => $this->nextPageUrl(),
            'path'              => $this->path(),
            'per_page'          => $this->perPage(),
            'prev_page_url'     => $this->previousPageUrl(),
            'to'                => $this->lastItem(),
            'total'             => $this->total(),
        ];
    }
}
