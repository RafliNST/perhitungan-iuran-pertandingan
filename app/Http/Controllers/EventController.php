<?php

namespace App\Http\Controllers;

use App\Enum\EventStatus;
use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Community;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['host', 'community'])
            ->latest('start_time')
            ->get();

        return EventResource::collection($events);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        $community = isset($data['community_id'])
            ? Community::findOrFail($data['community_id'])
            : null;

        Gate::authorize('create', [Event::class, $community]);

        $data['slug']    = Str::slug($data['name']) . '-' . Str::random(5);
        $data['host_id'] = Auth::id();
        $data['status']  = EventStatus::PLANNED;

        $event = Event::create($data);

        return new EventResource($event);
    }

    public function show(Event $event)
    {
        $event->load(['host', 'community', 'participants.user']);

        return new EventResource($event);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        Gate::authorize('update', $event);

        $data = $request->validated();

        if (isset($data['name']) && $data['name'] !== $event->name) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        }

        $event->update($data);

        return new EventResource($event);
    }

    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $event->update([
            'status' => EventStatus::CANCELLED,
        ]);

        return new EventResource($event);
    }
}
