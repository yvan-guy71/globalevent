<?php

namespace Database\Factories;

use App\Models\CompetitionCandidate;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompetitionCandidate>
 */
class CompetitionCandidateFactory extends Factory
{
    protected $model = CompetitionCandidate::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(15),
            'image' => null,
            'contact_phone' => '+228 '.fake()->numerify('## ## ## ##'),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
