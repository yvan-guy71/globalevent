<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketingTest extends TestCase
{
    use RefreshDatabase;

    private function orderableEvent(User $organizer): Event
    {
        return Event::factory()->create([
            'user_id' => $organizer->id,
            'status' => 'published',
            'starts_at' => now()->addDays(10),
        ]);
    }

    private function ticketType(Event $event, array $attributes = []): TicketType
    {
        return TicketType::factory()->create($attributes + [
            'event_id' => $event->id,
            'is_active' => true,
        ]);
    }

    public function test_reservation_page_lists_active_ticket_types(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $this->ticketType($event, ['name' => 'Standard', 'price' => 5000]);
        $this->ticketType($event, ['name' => 'VIP', 'price' => 20000]);
        $this->ticketType($event, ['name' => 'Masqué', 'is_active' => false]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/evenements/'.$event->slug.'/reserver')
            ->assertOk()
            ->assertSee('Standard')
            ->assertSee('VIP')
            ->assertDontSee('Masqué');
    }

    public function test_reservation_requires_authentication(): void
    {
        $event = $this->orderableEvent(User::factory()->create());
        $this->ticketType($event);

        $this->get('/evenements/'.$event->slug.'/reserver')->assertRedirect('/connexion');
    }

    public function test_user_can_reserve_and_tickets_are_created(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $type = $this->ticketType($event, ['price' => 5000, 'quantity' => 10]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/evenements/'.$event->slug.'/reserver', [
                'items' => [$type->id => 3],
            ])
            ->assertRedirect();

        $order = Order::where('user_id', $user->id)->firstOrFail();

        $this->assertSame('paid', $order->status);
        $this->assertSame(15000.0, (float) $order->total_amount);
        $this->assertSame(3, $order->tickets()->count());

        $this->assertSame(3, $type->fresh()->sold);

        $this->assertSame(
            3,
            Ticket::where('user_id', $user->id)->where('status', 'valid')->count()
        );

        $this->assertDatabaseHas('tickets', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'valid',
        ]);
    }

    public function test_order_page_requires_at_least_one_ticket(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $type = $this->ticketType($event, ['quantity' => 10]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/evenements/'.$event->slug.'/reserver', [
                'items' => [$type->id => 0],
            ])
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_cannot_exceed_available_quantity(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $type = $this->ticketType($event, ['price' => 5000, 'quantity' => 5]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/evenements/'.$event->slug.'/reserver', [
                'items' => [$type->id => 6],
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_past_event_is_no_longer_orderable(): void
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'status' => 'published',
            'starts_at' => now()->subDays(5),
        ]);
        $this->ticketType($event);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/evenements/'.$event->slug.'/reserver')
            ->assertStatus(410);
    }

    public function test_guest_cannot_reserve(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $type = $this->ticketType($event);

        $this->post('/evenements/'.$event->slug.'/reserver', [
            'items' => [$type->id => 1],
        ])->assertRedirect('/connexion');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_show_is_restricted_to_owner(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $owner = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
        ]);

        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->get('/commandes/'.$order->id)
            ->assertForbidden();

        $this->actingAs($owner)
            ->get('/commandes/'.$order->id)
            ->assertOk();
    }

    public function test_admin_and_organizer_can_view_order(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $owner = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/commandes/'.$order->id)
            ->assertOk();

        $this->actingAs($organizer)
            ->get('/commandes/'.$order->id)
            ->assertOk();
    }

    public function test_ticket_show_renders_qr_for_owner(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->actingAs($user)
            ->get('/billets/'.$ticket->id)
            ->assertOk()
            ->assertSee($ticket->code);
    }

    public function test_ticket_show_is_restricted_to_owner_and_admin(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $owner = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
        ]);

        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->get('/billets/'.$ticket->id)
            ->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/billets/'.$ticket->id)
            ->assertOk();
    }

    public function test_checkin_validates_a_ticket_and_marks_it_used(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'status' => 'valid',
        ]);

        $this->actingAs($organizer)
            ->postJson('/evenements/'.$event->slug.'/check-in', [
                'code' => $ticket->code,
            ])
            ->assertOk()
            ->assertJson(['status' => 'valid']);

        $this->assertSame('used', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->checked_in_at);
    }

    public function test_duplicate_scan_is_rejected(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'status' => 'used',
            'checked_in_at' => now(),
        ]);

        $this->actingAs($organizer)
            ->postJson('/evenements/'.$event->slug.'/check-in', [
                'code' => $ticket->code,
            ])
            ->assertStatus(422)
            ->assertJson(['status' => 'used']);
    }

    public function test_unknown_code_is_rejected(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $this->actingAs($organizer)
            ->postJson('/evenements/'.$event->slug.'/check-in', [
                'code' => 'TICKET-DOES-NOT-EXIST',
            ])
            ->assertStatus(422)
            ->assertJson(['status' => 'invalid']);
    }

    public function test_only_organizer_or_admin_can_check_in(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'status' => 'valid',
        ]);

        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->postJson('/evenements/'.$event->slug.'/check-in', [
                'code' => $ticket->code,
            ])
            ->assertForbidden();

        $this->assertSame('valid', $ticket->fresh()->status);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->postJson('/evenements/'.$event->slug.'/check-in', [
                'code' => $ticket->code,
            ])
            ->assertOk();

        $this->assertSame('used', $ticket->fresh()->status);
    }

    public function test_my_tickets_page_lists_only_own_tickets(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        Ticket::factory()->create([
            'user_id' => User::factory()->create()->id,
            'event_id' => $event->id,
        ]);

        $this->get('/mes-billets')->assertRedirect('/connexion');

        $this->actingAs($user)
            ->get('/mes-billets')
            ->assertOk()
            ->assertSee($ticket->code);
    }

    public function test_my_orders_page_lists_only_own_orders(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        Order::factory()->create([
            'user_id' => User::factory()->create()->id,
            'event_id' => $event->id,
        ]);

        $this->actingAs($user)
            ->get('/mes-commandes')
            ->assertOk()
            ->assertSee($order->reference);
    }

    public function test_only_managers_can_open_ticket_management(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $this->actingAs(User::factory()->create())
            ->get('/evenements/'.$event->slug.'/billets')
            ->assertForbidden();

        $this->actingAs($organizer)
            ->get('/evenements/'.$event->slug.'/billets')
            ->assertOk();
    }

    public function test_organizer_can_create_update_and_delete_ticket_types(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $this->actingAs($organizer)
            ->post('/evenements/'.$event->slug.'/billets', [
                'name' => 'Standard',
                'price' => 7500,
                'quantity' => 40,
                'is_active' => 1,
            ])
            ->assertRedirect(route('ticket-types.index', $event));

        $type = TicketType::where('event_id', $event->id)->firstOrFail();

        $this->assertSame('Standard', $type->name);
        $this->assertSame(7500.0, (float) $type->price);
        $this->assertSame(40, (int) $type->quantity);
        $this->assertTrue((bool) $type->is_active);

        $this->actingAs($organizer)
            ->put('/evenements/'.$event->slug.'/billets/'.$type->id, [
                'name' => 'Standard Plus',
                'price' => 9000,
                'quantity' => 40,
                'is_active' => 1,
            ])
            ->assertRedirect(route('ticket-types.index', $event));

        $this->assertSame('Standard Plus', $type->fresh()->name);
        $this->assertSame(9000.0, (float) $type->fresh()->price);

        $this->actingAs($organizer)
            ->delete('/evenements/'.$event->slug.'/billets/'.$type->id)
            ->assertRedirect(route('ticket-types.index', $event));

        $this->assertDatabaseMissing('ticket_types', ['id' => $type->id]);
    }

    public function test_cannot_delete_a_ticket_type_with_sold_tickets(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $type = $this->ticketType($event, ['quantity' => 10]);

        Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $type->id,
        ]);

        $this->actingAs($organizer)
            ->delete('/evenements/'.$event->slug.'/billets/'.$type->id)
            ->assertForbidden();

        $this->assertDatabaseHas('ticket_types', ['id' => $type->id]);
    }

    public function test_ticket_type_from_another_event_is_not_found(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $otherEvent = $this->orderableEvent($organizer);
        $foreignType = $this->ticketType($otherEvent);

        $this->actingAs($organizer)
            ->put('/evenements/'.$event->slug.'/billets/'.$foreignType->id, [
                'name' => 'Piratage',
                'price' => 1,
                'is_active' => 1,
            ])
            ->assertNotFound();

        $this->actingAs($organizer)
            ->delete('/evenements/'.$event->slug.'/billets/'.$foreignType->id)
            ->assertNotFound();
    }

    public function test_organizer_can_generate_and_revoke_a_public_scan_link(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $this->actingAs($organizer)
            ->post('/evenements/'.$event->slug.'/check-in/partage')
            ->assertRedirect();

        $event->refresh();
        $this->assertTrue($event->hasScanShare());
        $this->assertNotNull($event->scanUrl());

        $this->actingAs($organizer)
            ->get('/evenements/'.$event->slug.'/check-in')
            ->assertOk()
            ->assertSee('Partager le scanner')
            ->assertSee($event->scan_token);

        $this->actingAs($organizer)
            ->delete('/evenements/'.$event->slug.'/check-in/partage')
            ->assertRedirect();

        $event->refresh();
        $this->assertFalse($event->hasScanShare());
    }

    public function test_only_manager_can_manage_scan_share(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->post('/evenements/'.$event->slug.'/check-in/partage')
            ->assertForbidden();

        $this->assertFalse($event->fresh()->hasScanShare());
    }

    public function test_scan_share_requires_authentication(): void
    {
        $event = $this->orderableEvent(User::factory()->create());

        $this->post('/evenements/'.$event->slug.'/check-in/partage')
            ->assertRedirect('/connexion');
    }

    public function test_guest_can_open_the_shared_scan_page_without_an_account(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $event->generateScanToken();

        $this->get(route('scan.show', ['event' => $event->slug, 'token' => $event->scan_token]))
            ->assertOk()
            ->assertSee('Scanner un billet')
            ->assertSee($event->title);
    }

    public function test_guest_can_scan_a_ticket_via_shared_link(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $event->generateScanToken();

        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'status' => 'valid',
        ]);

        $this->postJson(route('scan.process', ['event' => $event->slug, 'token' => $event->scan_token]), [
            'code' => $ticket->code,
        ])
            ->assertOk()
            ->assertJson(['status' => 'valid']);

        $ticket->refresh();
        $this->assertSame('used', $ticket->status);
        $this->assertNull($ticket->checked_in_by);
    }

    public function test_shared_scan_link_rejects_invalid_token(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $event->generateScanToken();

        $this->get(route('scan.show', ['event' => $event->slug, 'token' => 'not-the-real-token']))
            ->assertNotFound();
    }

    public function test_shared_scan_link_is_unavailable_when_no_token_exists(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);

        $this->get(route('scan.show', ['event' => $event->slug, 'token' => 'whatever']))
            ->assertNotFound();
    }

    public function test_shared_scan_link_only_accepts_tickets_from_its_event(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $event->generateScanToken();
        $otherEvent = $this->orderableEvent($organizer);

        $foreignTicket = Ticket::factory()->create([
            'event_id' => $otherEvent->id,
            'status' => 'valid',
        ]);

        $this->postJson(route('scan.process', ['event' => $event->slug, 'token' => $event->scan_token]), [
            'code' => $foreignTicket->code,
        ])
            ->assertStatus(422)
            ->assertJson(['status' => 'invalid']);

        $this->assertSame('valid', $foreignTicket->fresh()->status);
    }

    public function test_revoking_the_scan_link_disables_public_access(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $token = $event->generateScanToken();

        $this->get(route('scan.show', ['event' => $event->slug, 'token' => $token]))->assertOk();

        $event->revokeScanToken();

        $this->get(route('scan.show', ['event' => $event->slug, 'token' => $token]))->assertNotFound();
    }

    public function test_ticket_page_exposes_a_qr_download_trigger(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->actingAs($user)
            ->get('/billets/'.$ticket->id)
            ->assertOk()
            ->assertSee('data-qr-download', false)
            ->assertSee('billet-'.$ticket->code.'.png', false);
    }

    public function test_my_tickets_page_exposes_qr_download_triggers(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->actingAs($user)
            ->get('/mes-billets')
            ->assertOk()
            ->assertSee('data-qr-download', false)
            ->assertSee('billet-'.$ticket->code.'.png', false);
    }

    public function test_order_page_exposes_qr_download_triggers(): void
    {
        $organizer = User::factory()->create();
        $event = $this->orderableEvent($organizer);
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $ticket = Ticket::factory()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->actingAs($user)
            ->get('/commandes/'.$order->id)
            ->assertOk()
            ->assertSee('data-qr-download', false)
            ->assertSee('billet-'.$ticket->code.'.png', false);
    }
}
