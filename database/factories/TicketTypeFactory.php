<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketType>
 */
class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Standard', 'VIP', 'Pass Journée', 'Loge', 'Étudiant', 'Prévente']);

        return [
            'event_id' => Event::factory(),
            'name' => $name,
            'description' => fake()->boolean(50) ? fake()->sentence(8) : null,
            'price' => fake()->randomElement([0, 2000, 5000, 10000, 25000]),
            'currency' => 'FCFA',
            'quantity' => fake()->randomElement([null, 50, 100, 200]),
            'sold' => 0,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
