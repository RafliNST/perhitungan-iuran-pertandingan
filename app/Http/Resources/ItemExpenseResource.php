<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'item_id'    => (int) $this->item_id,
            'user_id'    => (int) $this->user_id,
            'event_id'   => (int) $this->event_id,
            'qty'        => (int) $this->qty,
            // Otomatis menghitung subtotal jika relasi 'item' ikut di-load
            'subtotal'   => $this->whenLoaded('item', fn () => (float) $this->item->price * (int) $this->qty),
            'item'       => new ItemResource($this->whenLoaded('item')),
            'user'       => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
