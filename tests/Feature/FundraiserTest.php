<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Fundraiser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundraiserTest extends TestCase
{
    use RefreshDatabase;

    private function fundraiser(array $attributes = []): Fundraiser
    {
        return Fundraiser::factory()->create($attributes);
    }

    public function test_index_lists_open_fundraisers(): void
    {
        $open = $this->fundraiser(['title' => 'Cagnotte Ouverte', 'status' => 'open']);
        $this->fundraiser(['title' => 'Cagnotte Fermée', 'status' => 'closed']);

        $this->get('/cagnottes')
            ->assertOk()
            ->assertSee('Cagnotte Ouverte')
            ->assertDontSee('Cagnotte Fermée');
    }

    public function test_index_can_search_fundraisers(): void
    {
        $this->fundraiser(['title' => 'Aide aux sinistrés', 'status' => 'open']);
        $this->fundraiser(['title' => 'Festival des arts', 'status' => 'open']);

        $this->get('/cagnottes?q=sinistrés')
            ->assertOk()
            ->assertSee('Aide aux sinistrés')
            ->assertDontSee('Festival des arts');
    }

    public function test_show_page_renders_with_progress(): void
    {
        $fundraiser = $this->fundraiser(['title' => 'Cagnotte Visible', 'goal_amount' => 100000]);

        Donation::factory()->create([
            'fundraiser_id' => $fundraiser->id,
            'amount' => 25000,
            'status' => 'paid',
        ]);

        $this->get('/cagnottes/'.$fundraiser->slug)
            ->assertOk()
            ->assertSee('Cagnotte Visible')
            ->assertSee('25%');
    }

    public function test_create_requires_authentication(): void
    {
        $this->get('/cagnottes/creer')->assertRedirect('/connexion');
    }

    public function test_authenticated_user_can_create_fundraiser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/cagnottes', [
                'title' => 'Nouvelle collecte solidaire',
                'description' => 'Une description suffisamment longue pour passer la validation.',
                'goal_amount' => 500000,
                'currency' => 'FCFA',
                'status' => 'open',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fundraisers', [
            'user_id' => $user->id,
            'title' => 'Nouvelle collecte solidaire',
            'status' => 'open',
        ]);
    }

    public function test_only_owner_or_admin_can_update_fundraiser(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $fundraiser = $this->fundraiser(['user_id' => $owner->id]);

        $payload = [
            'title' => 'Titre modifié',
            'description' => 'Une description suffisamment longue pour passer la validation.',
            'goal_amount' => 300000,
            'status' => 'open',
        ];

        $this->actingAs($other)
            ->put('/cagnottes/'.$fundraiser->slug, $payload)
            ->assertForbidden();

        $this->actingAs($owner)
            ->put('/cagnottes/'.$fundraiser->slug, $payload)
            ->assertRedirect();

        $this->assertSame('Titre modifié', $fundraiser->fresh()->title);

        $this->actingAs($admin)
            ->put('/cagnottes/'.$fundraiser->fresh()->slug, array_merge($payload, ['title' => 'Modifié par admin']))
            ->assertRedirect();

        $this->assertSame('Modifié par admin', $fundraiser->fresh()->title);
    }

    public function test_owner_can_delete_fundraiser(): void
    {
        $owner = User::factory()->create();
        $fundraiser = $this->fundraiser(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->delete('/cagnottes/'.$fundraiser->slug)
            ->assertRedirect('/cagnottes');

        $this->assertDatabaseMissing('fundraisers', ['id' => $fundraiser->id]);
    }

    public function test_donating_requires_authentication(): void
    {
        $fundraiser = $this->fundraiser();

        $this->post('/cagnottes/'.$fundraiser->slug.'/dons', ['amount' => 5000])
            ->assertRedirect('/connexion');

        $this->assertDatabaseCount('donations', 0);
    }

    public function test_user_can_donate_and_progress_updates(): void
    {
        $user = User::factory()->create();
        $fundraiser = $this->fundraiser(['goal_amount' => 100000, 'status' => 'open']);

        $this->actingAs($user)
            ->post('/cagnottes/'.$fundraiser->slug.'/dons', [
                'amount' => 10000,
                'message' => 'Bravo pour ce projet !',
            ])
            ->assertRedirect(route('fundraisers.show', $fundraiser));

        $this->assertDatabaseHas('donations', [
            'fundraiser_id' => $fundraiser->id,
            'user_id' => $user->id,
            'amount' => 10000,
            'status' => 'paid',
        ]);

        $this->assertSame(10000.0, $fundraiser->fresh()->raised_amount);
        $this->assertSame(10, $fundraiser->fresh()->progress_percent);
    }

    public function test_donation_amount_is_validated(): void
    {
        $user = User::factory()->create();
        $fundraiser = $this->fundraiser(['status' => 'open']);

        $this->actingAs($user)
            ->post('/cagnottes/'.$fundraiser->slug.'/dons', ['amount' => 100])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('donations', 0);
    }

    public function test_cannot_donate_to_closed_fundraiser(): void
    {
        $user = User::factory()->create();
        $fundraiser = $this->fundraiser(['status' => 'closed']);

        $this->actingAs($user)
            ->post('/cagnottes/'.$fundraiser->slug.'/dons', ['amount' => 5000])
            ->assertForbidden();

        $this->assertDatabaseCount('donations', 0);
    }

    public function test_mine_lists_only_own_fundraisers(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->fundraiser(['user_id' => $owner->id, 'title' => 'Ma cagnotte']);
        $this->fundraiser(['user_id' => $other->id, 'title' => 'Cagnotte des autres']);

        $this->actingAs($owner)
            ->get('/mes-cagnottes')
            ->assertOk()
            ->assertSee('Ma cagnotte')
            ->assertDontSee('Cagnotte des autres');
    }
}
