<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'company' => $this->faker->optional()->company(),
            'subject' => $this->faker->sentence(4),
            'message' => $this->faker->paragraph(3),
            'is_read' => $this->faker->boolean(30), // 30% chance of being read
            'is_replied' => $this->faker->boolean(20), // 20% chance of being replied
            'admin_reply' => $this->faker->optional(20)->paragraph(2), // 20% chance of having a reply
            'replied_at' => $this->faker->optional(20)->dateTimeBetween('-1 month', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}
