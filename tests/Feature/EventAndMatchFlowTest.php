<?php

namespace Tests\Feature;

use App\Enum\EventStatus;
use App\Enum\ItemDistributionType;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventAndMatchFlowTest extends TestCase
{
    use RefreshDatabase;

    private function setupCommunityWithMember(): array
    {
        $host = User::factory()->create();
        $member = User::factory()->create();
        $community = Community::create([
            'name'     => 'PB Medan',
            'slug'     => 'pb-medan',
            'owner_id' => $host->id,
        ]);

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id'      => $member->id,
        ]);

        return [$host, $member, $community];
    }

    public function test_host_can_create_event_and_member_can_apply(): void
    {
        [$host, $member, $community] = $this->setupCommunityWithMember();

        // 1. Host membuat event
        Sanctum::actingAs($host);
        $eventRes = $this->postJson('/api/events', [
            'name'         => 'Mabar Jumat Malam',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay()->toDateTimeString(),
            'end_time'     => now()->addDay()->addHours(2)->toDateTimeString(),
            'community_id' => $community->id,
        ]);

        $eventRes->assertStatus(201);
        $event = Event::first();

        // 2. Member mendaftar ke event
        Sanctum::actingAs($member);
        $this->postJson('/api/participants', [
            'event_id' => $event->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('participants', [
            'event_id' => $event->id,
            'user_id'  => $member->id,
        ]);
    }

    public function test_host_cannot_apply_to_own_event(): void
    {
        [$host, , $community] = $this->setupCommunityWithMember();

        $event = Event::create([
            'name'         => 'Mabar Jumat Malam',
            'slug'         => 'mabar-jumat-malam',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay(),
            'community_id' => $community->id,
            'host_id'      => $host->id,
            'status'       => EventStatus::PLANNED,
        ]);

        Sanctum::actingAs($host);

        $this->postJson('/api/participants', [
            'event_id' => $event->id,
        ])->assertStatus(403);
    }

    public function test_user_cannot_apply_to_cancelled_or_completed_event(): void
    {
        [$host, $member, $community] = $this->setupCommunityWithMember();

        $cancelledEvent = Event::create([
            'name'         => 'Event Batal',
            'slug'         => 'event-batal',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay(),
            'community_id' => $community->id,
            'host_id'      => $host->id,
            'status'       => EventStatus::CANCELLED,
        ]);

        Sanctum::actingAs($member);

        $this->postJson('/api/participants', [
            'event_id' => $cancelledEvent->id,
        ])->assertStatus(403);
    }

    public function test_delete_event_changes_status_to_cancelled(): void
    {
        [$host, , $community] = $this->setupCommunityWithMember();

        $event = Event::create([
            'name'         => 'Mabar Rutin',
            'slug'         => 'mabar-rutin',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay(),
            'community_id' => $community->id,
            'host_id'      => $host->id,
            'status'       => EventStatus::PLANNED,
        ]);

        Sanctum::actingAs($host);

        $this->deleteJson("/api/events/{$event->slug}")
            ->assertStatus(200)
            ->assertJsonPath('data.status', EventStatus::CANCELLED->value);
    }

    public function test_host_can_manage_games_items_and_item_expenses(): void
    {
        [$host, $member, $community] = $this->setupCommunityWithMember();

        $event = Event::create([
            'name'         => 'Mabar Rutin',
            'slug'         => 'mabar-rutin',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay(),
            'community_id' => $community->id,
            'host_id'      => $host->id,
            'status'       => EventStatus::PLANNED,
        ]);

        Participant::create([
            'event_id' => $event->id,
            'user_id'  => $member->id,
        ]);

        Sanctum::actingAs($host);

        // 1. Host menambahkan peserta ke game (Tim 1)
        $this->postJson('/api/games', [
            'team_id'  => 1,
            'event_id' => $event->id,
            'user_id'  => $member->id,
        ])->assertStatus(201);

        // 2. Host menambahkan item GLOBAL (Sewa Lapangan) & SPECIFIED (Kok / Minum)
        $this->postJson('/api/items', [
            'event_id'          => $event->id,
            'name'              => 'Sewa Lapangan 2 Jam',
            'price'             => 100000,
            'distribution_type' => ItemDistributionType::GLOBAL->value,
        ])->assertStatus(201);

        $shuttlecockRes = $this->postJson('/api/items', [
            'event_id'          => $event->id,
            'name'              => 'Shuttlecock',
            'price'             => 15000,
            'distribution_type' => ItemDistributionType::SPECIFIED->value,
        ])->assertStatus(201);

        $itemId = $shuttlecockRes->json('data.id');

        // 3. Host mencatat pemakaian item specified untuk peserta
        $this->postJson('/api/item-expenses', [
            'event_id' => $event->id,
            'item_id'  => $itemId,
            'user_id'  => $member->id,
            'qty'      => 2,
        ])->assertStatus(201);

        $this->assertDatabaseHas('item_expenses', [
            'event_id' => $event->id,
            'item_id'  => $itemId,
            'user_id'  => $member->id,
            'qty'      => 2,
        ]);
    }

    public function test_non_host_cannot_add_game_or_item(): void
    {
        [$host, $member, $community] = $this->setupCommunityWithMember();

        $event = Event::create([
            'name'         => 'Mabar Rutin',
            'slug'         => 'mabar-rutin',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay(),
            'community_id' => $community->id,
            'host_id'      => $host->id,
            'status'       => EventStatus::PLANNED,
        ]);

        Participant::create([
            'event_id' => $event->id,
            'user_id'  => $member->id,
        ]);

        // Login sebagai member biasa (bukan host)
        Sanctum::actingAs($member);

        $this->postJson('/api/games', [
            'team_id'  => 1,
            'event_id' => $event->id,
            'user_id'  => $member->id,
        ])->assertStatus(403);

        $this->postJson('/api/items', [
            'event_id'          => $event->id,
            'name'              => 'Air Mineral',
            'price'             => 5000,
            'distribution_type' => ItemDistributionType::SPECIFIED->value,
        ])->assertStatus(403);
    }

    public function test_host_cannot_add_non_participant_to_game(): void
    {
        [$host, , $community] = $this->setupCommunityWithMember();
        $randomUser = User::factory()->create();

        $event = Event::create([
            'name'         => 'Mabar Rutin',
            'slug'         => 'mabar-rutin',
            'location'     => 'GOR Cemara',
            'start_time'   => now()->addDay(),
            'community_id' => $community->id,
            'host_id'      => $host->id,
            'status'       => EventStatus::PLANNED,
        ]);

        Sanctum::actingAs($host);

        // Gagal validasi (422) karena $randomUser belum terdaftar di tabel participants
        $this->postJson('/api/games', [
            'team_id'  => 1,
            'event_id' => $event->id,
            'user_id'  => $randomUser->id,
        ])->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }
}