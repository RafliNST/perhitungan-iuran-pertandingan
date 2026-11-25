<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_community(): void
    {
        $response = $this->postJson('/api/communities', [
            'name'        => 'PB Medan Smash',
            'description' => 'Komunitas badminton rutin mingguan',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_community(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/communities', [
            'name'        => 'PB Medan Smash',
            'description' => 'Komunitas badminton rutin mingguan',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'PB Medan Smash');

        $this->assertDatabaseHas('communities', [
            'name'     => 'PB Medan Smash',
            'owner_id' => $user->id,
        ]);
    }

    public function test_user_can_join_existing_community(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $community = Community::factory()->create(['owner_id' => $owner->id]);

        Sanctum::actingAs($member);

        $response = $this->postJson('/api/community-members', [
            'community_id' => $community->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('community_members', [
            'community_id' => $community->id,
            'user_id'      => $member->id,
        ]);
    }

    public function test_community_owner_cannot_join_own_community(): void
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['owner_id' => $owner->id]);

        Sanctum::actingAs($owner);

        $response = $this->postJson('/api/community-members', [
            'community_id' => $community->id,
        ]);

        // Ditolak oleh CommunityPolicy@join
        $response->assertStatus(403);
    }

    public function test_user_cannot_join_same_community_twice(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $community = Community::factory()->create(['owner_id' => $owner->id]);

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id'      => $member->id,
        ]);

        Sanctum::actingAs($member);

        $response = $this->postJson('/api/community-members', [
            'community_id' => $community->id,
        ]);

        // Ditolak oleh Rule::unique di StoreCommunityMemberRequest
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['community_id']);
    }

    public function test_non_owner_cannot_delete_community(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $community = Community::factory()->create(['owner_id' => $owner->id]);

        Sanctum::actingAs($otherUser);

        $response = $this->deleteJson("/api/communities/{$community->slug}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('communities', ['id' => $community->id]);
    }
}