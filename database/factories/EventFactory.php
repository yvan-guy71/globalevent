<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $types = array_keys(config('events.types'));
        $cities = config('events.cities');
        $title = fake()->randomElement([
            'Nuit du Mawazo',
            'Lomé Beach Marathon',
            'Festival des Arts de la Rue',
            'Hackathon Digital 2026',
            'Concert Symphonie d\'Or',
            'Championnat National de Foot',
            'Salon de l\'Entrepreneuriat',
            'Soirée Gala Caritatif',
            'Masterclass Photographie',
            'Foire Artisanale de Kara',
        ]);
        $type = fake()->randomElement($types);
        $startsAt = fake()->dateTimeBetween('+3 days', '+4 months');
        $price = fake()->randomElement([0, 0, 2000, 5000, 10000, 15000, 25000]);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'type' => $type,
            'description' => implode("\n\n", [
                fake()->paragraphs(2, true),
                fake()->sentence(12),
            ]),
            'starts_at' => $startsAt,
            'ends_at' => fake()->boolean(70) ? (clone $startsAt)->modify('+'.fake()->numberBetween(2, 8).' hours') : null,
            'venue' => fake()->randomElement([
                'Stade de Kpévé',
                'Palais des Congrès',
                'Espace Kwezi',
                'Hôtel Sarakawa',
                'Place de la Libertation',
                'Gnassingbé Eyadéma Arena',
            ]),
            'address' => fake()->boolean() ? fake()->address() : null,
            'city' => fake()->randomElement($cities),
            'country' => fake()->randomElement([
                'Togo', 'Togo', 'Togo', 'Togo', 'Togo',
                'Bénin', 'Ghana', 'Côte d\'Ivoire', 'Burkina Faso', 'France',
            ]),
            'price' => $price,
            'currency' => 'FCFA',
            'capacity' => fake()->numberBetween(100, 5000),
            'image' => null,
            'contact_name' => fake()->name(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => '+228 '.fake()->numerify('## ## ## ##'),
            'status' => fake()->randomElement(['published', 'published', 'published', 'draft']),
            'is_featured' => fake()->boolean(30),
            'published_at' => now(),
        ];
    }
}
