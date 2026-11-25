<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Community>
 */
class CommunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, 1);
        $slug = Str::slug($name . '-' . $name);


        return [
            'name' => $name,
            'description' => fake()->paragraph(),
            'slug' => $slug,
            'owner_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
