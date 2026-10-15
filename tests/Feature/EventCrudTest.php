<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCrudTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Concert de test à Lomé',
            'type' => 'concert',
            'description' => 'Une description suffisamment longue pour passer la validation.',
            'starts_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'venue' => 'Palais des Congrès',
            'city' => 'Lomé',
            'country' => 'Togo',
            'price' => 5000,
            'currency' => 'FCFA',
            'capacity' => 200,
            'status' => 'published',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/evenements/creer')->assertRedirect('/connexion');
        $this->get('/mes-evenements')->assertRedirect('/connexion');
        $this->post('/evenements', $this->payload())->assertRedirect('/connexion');
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/evenements')->assertOk();
        $this->get('/comment-ca-marche')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/connexion')->assertOk();
        $this->get('/inscription')->assertOk();
    }

    public function test_user_can_create_view_edit_and_delete_event(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/evenements', $this->payload())
            ->assertRedirect();

        $event = Event::query()->firstOrFail();
        $this->assertSame($user->id, $event->user_id);
        $this->assertSame('published', $event->status);
        $this->assertSame('concert-de-test-a-lome', $event->slug);

        $this->get('/evenements/'.$event->slug)->assertOk();
        $this->get('/evenements/'.$event->slug.'/modifier')->assertOk();
        $this->get('/mes-evenements')->assertOk();

        $this->actingAs($user)
            ->put('/evenements/'.$event->slug, $this->payload(['title' => 'Titre modifié']))
            ->assertRedirect();

        $this->assertSame('Titre modifié', $event->fresh()->title);

        $this->actingAs($user)
            ->delete('/evenements/'.$event->fresh()->slug)
            ->assertRedirect();

        $this->assertDatabaseCount('events', 0);
    }

    public function test_other_user_cannot_edit_or_delete_event(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $event = Event::factory()->create(['user_id' => $owner->id, 'status' => 'published']);

        $this->actingAs($other)->get('/evenements/'.$event->slug.'/modifier')->assertForbidden();
        $this->actingAs($other)->put('/evenements/'.$event->slug, $this->payload())->assertForbidden();
        $this->actingAs($other)->delete('/evenements/'.$event->slug)->assertForbidden();
    }

    public function test_draft_event_is_hidden_from_guests_but_visible_to_owner(): void
    {
        $owner = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $owner->id, 'status' => 'draft']);

        $this->get('/evenements/'.$event->slug)->assertNotFound();
        $this->actingAs($owner)->get('/evenements/'.$event->slug)->assertOk();
    }

    public function test_validation_errors_are_returned(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/evenements/creer')
            ->post('/evenements', $this->payload(['title' => 'ab', 'type' => 'inconnu', 'city' => 'Paris', 'country' => 'Atlantide']))
            ->assertRedirect('/evenements/creer')
            ->assertSessionHasErrors(['title', 'type', 'city', 'country']);
    }

    public function test_admin_panel_is_restricted_and_renders_for_admin(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Event::factory()->count(2)->create(['user_id' => $user->id]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_event_filters_work(): void
    {
        Event::factory()->create(['status' => 'published', 'type' => 'concert', 'title' => 'Soirée Jazz']);
        Event::factory()->create(['status' => 'published', 'type' => 'sport', 'title' => 'Match de football']);
        Event::factory()->create(['status' => 'draft', 'type' => 'concert', 'title' => 'Brouillon concert']);

        $this->get('/evenements?type=concert')
            ->assertOk()
            ->assertSee('Soirée Jazz')
            ->assertDontSee('Match de football')
            ->assertDontSee('Brouillon concert');

        $this->get('/evenements?q=football')
            ->assertOk()
            ->assertSee('Match de football')
            ->assertDontSee('Soirée Jazz');
    }

    public function test_event_country_filter_works(): void
    {
        Event::factory()->create(['status' => 'published', 'title' => 'Concert de Lomé', 'country' => 'Togo', 'city' => 'Lomé']);
        Event::factory()->create(['status' => 'published', 'title' => 'Festival de Cotonou', 'country' => 'Bénin', 'city' => 'Autre']);

        $this->get('/evenements?country=Bénin')
            ->assertOk()
            ->assertSee('Festival de Cotonou')
            ->assertDontSee('Concert de Lomé');

        $this->get('/evenements?country=Togo')
            ->assertOk()
            ->assertSee('Concert de Lomé')
            ->assertDontSee('Festival de Cotonou');

        $this->actingAs(User::factory()->create())
            ->post('/evenements', $this->payload(['title' => 'Gala de Kara', 'country' => 'Togo']))
            ->assertRedirect();

        $this->assertSame('Togo', Event::query()->latest('id')->firstOrFail()->country);
    }

    public function test_login_and_registration_flow(): void
    {
        $this->post('/inscription', [
            'name' => 'Nouveau Client',
            'email' => 'nouveau@eventglobal.test',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
            'terms' => '1',
        ])->assertRedirect('/')->assertSessionHas('confetti');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'nouveau@eventglobal.test', 'role' => 'client']);

        $this->get('/')->assertOk()->assertSee('confetti-canvas');

        $this->post('/deconnexion')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/connexion', [
            'email' => 'nouveau@eventglobal.test',
            'password' => 'motdepasse123',
        ])->assertRedirect('/');

        $this->assertAuthenticated();

        $this->post('/deconnexion');
        $this->assertGuest();

        $this->post('/connexion', [
            'email' => 'nouveau@eventglobal.test',
            'password' => 'mauvais',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_contact_form_sends_message(): void
    {
        $this->post('/contact', [
            'name' => 'Testeur',
            'email' => 'testeur@example.com',
            'subject' => 'Question',
            'message' => 'Bonjour, ceci est un message de test suffisamment long.',
        ])->assertRedirect();
    }

    public function test_authenticated_header_groups_account_links_in_dropdown(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/')->assertOk();

        $response->assertSee('user-menu-button', false)
            ->assertSee('user-menu-dropdown', false)
            ->assertSee('toggleUserMenu', false)
            ->assertSee('Mes commandes')
            ->assertSee('Panel admin');
    }

    public function test_free_checkbox_forces_price_to_zero_on_create_and_update(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/evenements', $this->payload(['price' => 8000, 'is_free' => '1']))
            ->assertRedirect();

        $event = Event::query()->firstOrFail();
        $this->assertSame(0.0, (float) $event->price);
        $this->assertTrue($event->is_free);

        $this->actingAs($user)
            ->put('/evenements/'.$event->slug, $this->payload([
                'title' => 'Titre modifié gratuit',
                'price' => 0,
                'is_free' => '1',
            ]))
            ->assertRedirect();

        $this->assertSame(0.0, (float) $event->fresh()->price);
        $this->assertTrue($event->fresh()->is_free);
    }

    public function test_organizer_dashboard_shows_stats(): void
    {
        $organizer = User::factory()->create();
        Event::factory()->create(['user_id' => $organizer->id, 'status' => 'published']);

        $this->actingAs($organizer)
            ->get('/mes-evenements')
            ->assertOk()
            ->assertSee('Billets vendus')
            ->assertSee('Recettes');
    }

    public function test_creating_an_event_promotes_a_client_to_organizer(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $this->actingAs($user)
            ->post('/evenements', $this->payload())
            ->assertRedirect();

        $this->assertSame('organisateur', $user->fresh()->role);
    }

    public function test_creating_an_event_does_not_change_an_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/evenements', $this->payload())
            ->assertRedirect();

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_client_can_become_organizer_with_one_click(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $this->actingAs($user)
            ->post('/devenir-organisateur')
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('organisateur', $user->fresh()->role);
    }

    public function test_already_organizer_is_not_changed(): void
    {
        $organizer = User::factory()->create(['role' => 'organisateur']);

        $this->actingAs($organizer)
            ->post('/devenir-organisateur')
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertSame('organisateur', $organizer->fresh()->role);
    }

    public function test_becoming_organizer_requires_authentication(): void
    {
        $this->post('/devenir-organisateur')->assertRedirect('/connexion');
    }

    public function test_header_shows_become_organizer_only_for_clients(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $organizer = User::factory()->create(['role' => 'organisateur']);

        $this->actingAs($client)->get('/')->assertOk()->assertSee('Devenir organisateur');
        $this->actingAs($organizer)->get('/')->assertOk()->assertDontSee('Devenir organisateur');
    }
}
