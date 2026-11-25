<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunityMember>
 */
class CommunityMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $member = User::inRandomOrder()->first();
        $community = Community::whereNot('owner_id', $member->id)->inRandomOrder()->first();
        
        return [
            'community_id' => $community->id,
            'user_id' => $member->id
        ];
    }
}
