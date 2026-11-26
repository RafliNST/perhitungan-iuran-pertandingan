<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityMemberResource extends JsonResource
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
            'community_id' => (int) $this->community_id,
            'user_id'      => (int) $this->user_id,
            'user'         => new UserResource($this->whenLoaded('user')),
            'community'    => new CommunityResource($this->whenLoaded('community')),
            'joined_at'    => $this->created_at,
        ];
    }
}
