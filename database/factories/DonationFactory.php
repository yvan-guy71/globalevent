<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\Fundraiser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'fundraiser_id' => Fundraiser::factory(),
            'user_id' => User::factory(),
            'donor_name' => fake()->name(),
            'amount' => fake()->randomElement([1000, 2500, 5000, 10000, 25000, 50000]),
            'currency' => 'FCFA',
            'message' => fake()->boolean(50) ? fake()->sentence(8) : null,
            'is_anonymous' => fake()->boolean(20),
            'status' => 'paid',
            'reference' => 'DON-'.Str::upper(Str::random(10)),
        ];
    }
}
