<?php

namespace Database\Factories;

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
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'subject' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['open', 'pending', 'closed']),
            'is_urgent' => fake()->boolean(20),
        ];
    }
}
