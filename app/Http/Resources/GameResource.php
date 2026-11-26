<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'team_id'      => (int) $this->team_id,
            'event_id'     => (int) $this->event_id,
            'community_id' => $this->community_id ? (int) $this->community_id : null,
            'user_id'      => (int) $this->user_id,
            'player'       => new UserResource($this->whenLoaded('user')),
            'event'        => new EventResource($this->whenLoaded('event')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
