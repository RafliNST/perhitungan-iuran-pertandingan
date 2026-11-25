<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Http\Requests\StoreParticipantRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ParticipantController extends Controller
{
    public function store(StoreParticipantRequest $request)
    {
        $data = $request->validated();
        $event = Event::findOrFail($data['event_id']);
        $targetUserId = $data['user_id'] ?? Auth::id();

        // Memvalidasi status event (tidak completed/cancelled), bukan host, dan anggota komunitas
        Gate::authorize('apply', [$event, $targetUserId]);

        $participant = Participant::create([
            'event_id' => $event->id,
            'user_id'  => $targetUserId,
        ]);

        $participant->load('user');

        return new ParticipantResource($participant);
    }
}