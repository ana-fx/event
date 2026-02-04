<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->randomElement(['General Admission', 'VIP', 'Early Bird', 'Student']),
            'price' => $this->faker->randomElement([0, 50000, 100000, 150000, 200000, 250000]),
            'quota' => $this->faker->numberBetween(50, 500),
            'max_purchase_per_user' => $this->faker->numberBetween(1, 5),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_date' => $this->faker->dateTimeBetween('+2 weeks', '+1 month'),
            'description' => $this->faker->sentence(),
        ];
    }
}
