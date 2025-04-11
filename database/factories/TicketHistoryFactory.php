<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketHistory>
 */
class TicketHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::all()->random()->id,
            'changed_by' => User::all()->random()->id,
            'field_changed' => fake()->randomElement(['status', 'priority', 'agent', 'title']),
            'old_value' => fake()->word,
            'new_value' => fake()->word,
        ];
    }
}
