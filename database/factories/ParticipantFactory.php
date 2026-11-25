<?php

namespace Database\Factories;

use App\Enum\PaymentStatus;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $event = Event::inRandomOrder()->first();
        $participant = User::inRandomOrder()->first();
        $payment_status = fake()->randomElement(PaymentStatus::cases())->value;

        return [
            'event_id' => $event->id,
            'user_id' => $participant->id,
            'payment_status' => $payment_status,
            'fee_total' => fake()->numberBetween(15000, 55000)
        ];
    }
}
