<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'event_id'       => (int) $this->event_id,
            'user_id'        => (int) $this->user_id,
            'payment_status' => $this->payment_status?->value,
            'fee_total'      => (float) $this->fee_total,
            'attended_at'    => $this->attended_at,
            'user'           => new UserResource($this->whenLoaded('user')),
            'event'          => new EventResource($this->whenLoaded('event')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
