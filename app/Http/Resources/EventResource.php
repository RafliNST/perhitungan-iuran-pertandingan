<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name'         => $this->name,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'location'     => $this->location,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
            'status'       => $this->status?->value,
            'status_label' => $this->status?->label(),
            'community_id' => $this->community_id,
            'host_id'      => $this->host_id,
            'host'         => new UserResource($this->whenLoaded('host')),
            'community'    => new CommunityResource($this->whenLoaded('community')),
            'participants' => ParticipantResource::collection($this->whenLoaded('participants')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
