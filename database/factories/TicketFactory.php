<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\User;
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
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['open', 'in_progress', 'closed', 'on_hold', 'cancelled']),
            'priority_id' => Priority::all()->random()->id,
            'category_id' => Category::all()->random()->id,
            'requester_id' => User::all()->random()->id,
            'agent_id' => User::all()->random()->id,
            'started_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'closed_at' => fake()->dateTimeBetween('now', '+1 week'),
            'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
