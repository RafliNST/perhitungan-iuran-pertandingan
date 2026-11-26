<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

class ItemController extends Controller
{
    // Menampilkan daftar item & rincian pembagiannya pada suatu event
    public function index(Event $event)
    {
        $items = Item::with('expenses.user')
            ->where('event_id', $event->id)
            ->get();

        return ItemResource::collection($items);
    }

    // Menambahkan item baru ke event (Hanya Host)
    public function store(StoreItemRequest $request)
    {
        $data = $request->validated();
        $event = Event::findOrFail($data['event_id']);

        Gate::authorize('manageDetails', $event);

        $item = Item::create($data);

        return (new ItemResource($item));
    }

    // Memperbarui data item (Hanya Host)
    public function update(UpdateItemRequest $request, Item $item)
    {
        Gate::authorize('manageDetails', $item->event);

        $item->update($request->validated());

        return (new ItemResource($item));
    }

    // Menghapus item (Hanya Host)
    public function destroy(Item $item)
    {
        Gate::authorize('manageDetails', $item->event);

        $item->delete();

        return response()->noContent();
    }
}