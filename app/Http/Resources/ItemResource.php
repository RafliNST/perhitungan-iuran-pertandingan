<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'event_id'          => (int) $this->event_id,
            'name'              => $this->name,
            'price'             => (float) $this->price,
            'distribution_type' => $this->distribution_type?->value,
            'expenses'          => ItemExpenseResource::collection($this->whenLoaded('expenses')),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
