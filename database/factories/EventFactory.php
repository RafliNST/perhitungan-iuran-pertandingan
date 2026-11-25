<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, 1);
        $host = User::inRandomOrder()->first();
        $slug = Str::slug($name . '-' . $host->name);

        $startTime = Carbon::instance(fake()->dateTimeBetween('+1 day', '+1 month'));

        // 2. 70% kemungkinan terisi (rentang start_time s/d maksimal +1 hari), 30% bernilai null
        $endTime = fake()->boolean(70)
            ? fake()->dateTimeBetween($startTime, $startTime->copy()->addDay())
            : null;

        $communityId = fake()->boolean(60)
            ? (Community::inRandomOrder()->value('id') ?? Community::factory())
            : null;

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->paragraph(),
            'location' => fake('id_ID')->streetAddress() . ', ' . fake('id_ID')->city(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'host_id' => $host->id,
            'community_id' => $communityId
        ];
    }
}
