<?php

namespace Tests\Feature;

use App\Enum\EventStatus;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_host_cannot_apply_to_own_event(): void
    {
        $host = User::factory()->create();
        $event = Event::factory()->create([
            'host_id' => $host->id,
            'status'  => EventStatus::PLANNED,
        ]);

        Sanctum::actingAs($host);

        $response = $this->postJson('/api/participants', [
            'event_id' => $event->id,
        ]);

        // Ditolak oleh EventPolicy@apply
        $response->assertStatus(403);
    }

    public function test_non_community_member_cannot_apply_to_community_event(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $community = Community::factory()->create(['owner_id' => $owner->id]);

        $event = Event::factory()->create([
            'host_id'      => $owner->id,
            'community_id' => $community->id,
            'status'       => EventStatus::PLANNED,
        ]);

        Sanctum::actingAs($outsider);

        $response = $this->postJson('/api/participants', [
            'event_id' => $event->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_apply_to_cancelled_or_completed_event(): void
    {
        $host = User::factory()->create();
        $user = User::factory()->create();

        $cancelledEvent = Event::factory()->create([
            'host_id'      => $host->id,
            'community_id' => null,
            'status'       => EventStatus::CANCELLED,
        ]);

        $completedEvent = Event::factory()->create([
            'host_id'      => $host->id,
            'community_id' => null,
            'status'       => EventStatus::COMPLETED,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/participants', ['event_id' => $cancelledEvent->id])
            ->assertStatus(403);

        $this->postJson('/api/participants', ['event_id' => $completedEvent->id])
            ->assertStatus(403);
    }

    public function test_host_cancelling_event_via_delete_updates_status_to_cancelled(): void
    {
        $host = User::factory()->create();
        $event = Event::factory()->create([
            'host_id' => $host->id,
            'status'  => EventStatus::PLANNED,
        ]);

        Sanctum::actingAs($host);

        $response = $this->deleteJson("/api/events/{$event->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', EventStatus::CANCELLED->value);

        $this->assertDatabaseHas('events', [
            'id'     => $event->id,
            'status' => EventStatus::CANCELLED->value,
        ]);
    }
}