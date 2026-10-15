<?php

namespace Database\Factories;

use App\Models\Fundraiser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fundraiser>
 */
class FundraiserFactory extends Factory
{
    protected $model = Fundraiser::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Cagnotte pour le Gala des Arts',
            'Collecte pour l\'hôpital de Kara',
            'Soutien à la tournée des jeunes talents',
            'Fonds d\'amorçage du festival de Lomé',
            'Aide aux sinistrés des inondations',
            'Financement du studio d\'enregistrement',
        ]);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => implode("\n\n", [fake()->paragraphs(2, true), fake()->sentence(10)]),
            'goal_amount' => fake()->randomElement([100000, 250000, 500000, 1000000, 2000000]),
            'currency' => 'FCFA',
            'deadline' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'image' => null,
            'contact_name' => fake()->name(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => '+228 '.fake()->numerify('## ## ## ##'),
            'status' => fake()->randomElement(['open', 'open', 'open', 'closed']),
            'is_featured' => fake()->boolean(30),
        ];
    }
}
