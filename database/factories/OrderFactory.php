<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'reference' => 'CMD-'.Str::upper(Str::random(10)),
            'total_amount' => fake()->randomElement([0, 5000, 10000, 25000]),
            'currency' => 'FCFA',
            'status' => 'paid',
            'paid_at' => now(),
        ];
    }
}
