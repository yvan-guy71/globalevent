<?php

namespace Database\Seeders;

use App\Models\CompetitionCandidate;
use App\Models\CompetitionVote;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Fundraiser;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Event Global',
            'email' => 'admin@eventglobal.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $organizer = User::create([
            'name' => 'Kossi Organisateur',
            'email' => 'organisateur@eventglobal.test',
            'password' => 'password',
            'role' => 'client',
            'organization' => 'Events Togo',
            'phone' => '+228 90 00 00 01',
        ]);

        $member = User::create([
            'name' => 'Ama Cliente',
            'email' => 'client@eventglobal.test',
            'password' => 'password',
            'role' => 'client',
        ]);

        $featured = [
            'Nuit du Mawazo - Concert Live',
            'Festival des Arts de la Rue 2026',
            'Lomé Beach Marathon',
        ];

        for ($index = 0; $index < 18; $index++) {
            Event::factory()->create([
                'user_id' => $index % 3 === 0 ? $member->id : $organizer->id,
                'is_featured' => $index < 3,
            ]);
        }

        Event::query()->orderBy('id')->limit(3)->get()->each(function (Event $event, int $index) use ($featured) {
            $event->update([
                'title' => $featured[$index] ?? $event->title,
                'slug' => Event::generateSlug($featured[$index] ?? $event->title, $event->id),
            ]);
        });

        $this->seedCompetition($organizer, [$member, $admin]);
        $this->seedCompetition($organizer, [$member, $admin]);

        $this->seedFundraisers($organizer, [$member, $admin]);

        $this->seedTicketing($organizer, $member, [$admin, $organizer]);

        $this->command?->info('Comptes de démo : admin@eventglobal.test / organisateur@eventglobal.test / client@eventglobal.test (mot de passe : password)');
    }

    private function seedCompetition(User $organizer, array $voters): void
    {
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'type' => 'competition',
            'title' => 'Trophée des Talents '.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(3)),
            'status' => 'published',
        ]);
        $event->update(['slug' => Event::generateSlug($event->title, $event->id)]);

        $candidates = CompetitionCandidate::factory()
            ->count(5)
            ->create([
                'event_id' => $event->id,
                'user_id' => $organizer->id,
            ]);

        foreach ($candidates as $candidate) {
            foreach ($voters as $voter) {
                CompetitionVote::create([
                    'event_id' => $event->id,
                    'candidate_id' => $candidate->id,
                    'user_id' => $voter->id,
                    'value' => fake()->randomElement([1, 1, 1, -1]),
                ]);
            }
        }
    }

    private function seedFundraisers(User $organizer, array $donors): void
    {
        $featured = [
            'Sauvez le festival des arts de Lomé',
            'Un toit pour les enfants de Kara',
        ];

        $fundraisers = Fundraiser::factory()
            ->count(8)
            ->create(['user_id' => $organizer->id]);

        $fundraisers->take(2)->each(function (Fundraiser $fundraiser, int $index) use ($featured) {
            $fundraiser->update([
                'title' => $featured[$index] ?? $fundraiser->title,
                'slug' => Fundraiser::generateSlug($featured[$index] ?? $fundraiser->title, $fundraiser->id),
                'is_featured' => true,
            ]);
        });

        foreach ($fundraisers as $fundraiser) {
            $target = (float) $fundraiser->goal_amount;
            $raised = 0.0;

            for ($i = 0; $i < fake()->numberBetween(3, 12); $i++) {
                $amount = fake()->randomElement([1000, 2500, 5000, 10000, 25000, 50000]);
                $donor = fake()->randomElement($donors);
                $anonymous = fake()->boolean(20);

                Donation::create([
                    'fundraiser_id' => $fundraiser->id,
                    'user_id' => $donor->id,
                    'donor_name' => $donor->name,
                    'amount' => $amount,
                    'currency' => $fundraiser->currency,
                    'message' => fake()->boolean(50) ? fake()->sentence(8) : null,
                    'is_anonymous' => $anonymous,
                    'status' => fake()->randomElement(['paid', 'paid', 'paid', 'pending']),
                    'reference' => 'DON-'.Str::upper(Str::random(10)),
                ]);

                $raised += $amount;

                if ($raised >= $target) {
                    break;
                }
            }
        }
    }

    private function seedTicketing(User $organizer, User $buyer, array $attendants): void
    {
        $events = Event::query()
            ->where('status', 'published')
            ->where('starts_at', '>=', now())
            ->orderBy('id')
            ->take(5)
            ->get();

        if ($events->isEmpty()) {
            return;
        }

        $categories = [
            ['VIP', 25000],
            ['Standard', 10000],
            ['Étudiant', 5000],
        ];

        foreach ($events as $event) {
            foreach ($categories as $index => [$name, $price]) {
                TicketType::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'description' => 'Accès '.\Illuminate\Support\Str::lower($name).'.',
                    'price' => $price,
                    'currency' => $event->currency ?: 'FCFA',
                    'quantity' => fake()->numberBetween(100, 300),
                    'sold' => 0,
                    'is_active' => true,
                    'sort_order' => $index,
                ]);
            }
        }

        $event = $events->first(fn (Event $candidate) => $candidate->user_id === $organizer->id) ?? $events->first();

        $types = $event->ticketTypes()->active()->take(2)->get();

        if ($types->isEmpty()) {
            return;
        }

        $order = Order::create([
            'reference' => Order::generateReference(),
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => 'pending',
            'total_amount' => 0,
            'currency' => $event->currency ?: 'FCFA',
        ]);

        $total = 0.0;

        foreach ($types as $type) {
            $quantity = fake()->numberBetween(1, 3);
            $subtotal = (float) $type->price * $quantity;
            $total += $subtotal;

            $item = $order->items()->create([
                'ticket_type_id' => $type->id,
                'quantity' => $quantity,
                'unit_price' => (float) $type->price,
                'subtotal' => $subtotal,
            ]);

            for ($i = 0; $i < $quantity; $i++) {
                Ticket::create([
                    'code' => Ticket::generateCode(),
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'ticket_type_id' => $type->id,
                    'event_id' => $event->id,
                    'user_id' => $buyer->id,
                    'holder_name' => $buyer->name,
                    'status' => 'valid',
                ]);
            }

            $type->increment('sold', $quantity);
        }

        $order->update(['total_amount' => $total]);
        $order->markPaid();

        $extraTicket = $order->tickets()->valid()->first();

        if ($extraTicket !== null && count($attendants) > 1) {
            $second = Order::create([
                'reference' => Order::generateReference(),
                'event_id' => $event->id,
                'user_id' => $attendants[1]->id,
                'status' => 'pending',
                'total_amount' => 0,
                'currency' => $event->currency ?: 'FCFA',
            ]);

            $type = $types->last();
            $subtotal = (float) $type->price;

            $item = $second->items()->create([
                'ticket_type_id' => $type->id,
                'quantity' => 1,
                'unit_price' => $subtotal,
                'subtotal' => $subtotal,
            ]);

            Ticket::create([
                'code' => Ticket::generateCode(),
                'order_id' => $second->id,
                'order_item_id' => $item->id,
                'ticket_type_id' => $type->id,
                'event_id' => $event->id,
                'user_id' => $attendants[1]->id,
                'holder_name' => $attendants[1]->name,
                'status' => 'valid',
            ]);

            $type->increment('sold', 1);

            $second->update(['total_amount' => $subtotal]);
            $second->markPaid();
        }
    }
}
