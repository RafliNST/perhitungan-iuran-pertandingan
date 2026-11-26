<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    // Menampilkan daftar pemain per game/tim pada suatu event
    public function index(Event $event)
    {
        $games = Game::with('user')
            ->where('event_id', $event->id)
            ->orderBy('team_id')
            ->get();

        return GameResource::collection($games);
    }

    // Menambahkan pemain ke dalam game (Hanya Host)
    public function store(StoreGameRequest $request)
    {
        $data = $request->validated();
        $event = Event::findOrFail($data['event_id']);

        Gate::authorize('manageDetails', $event);

        // Otomatis samakan community_id dengan milik event jika tidak dikirim
        $data['community_id'] = $data['community_id'] ?? $event->community_id;

        $game = Game::create($data);
        $game->load('user');

        return (new GameResource($game));
    }

    // Menghapus pemain dari game menggunakan ID tunggal (Hanya Host)
    public function destroy(Game $game)
    {
        Gate::authorize('manageDetails', $game->event);

        $game->delete();

        return response()->noContent();
    }
}