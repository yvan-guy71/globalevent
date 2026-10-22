<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/event-admin/utilisateurs')->assertRedirect('/event-admin/connexion');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/event-admin/utilisateurs')
            ->assertForbidden();
    }

    public function test_admin_sees_the_user_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['name' => 'Awa Candidate', 'email' => 'awa@eventglobal.test']);

        $this->actingAs($admin)
            ->get('/event-admin/utilisateurs')
            ->assertOk()
            ->assertSee('Awa Candidate')
            ->assertSee('awa@eventglobal.test')
            ->assertSee($admin->email);
    }

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Kossi Trovato']);
        User::factory()->create(['name' => 'Ama Zenou']);

        $this->actingAs($admin)
            ->get('/event-admin/utilisateurs?q=Trovato')
            ->assertOk()
            ->assertSee('Kossi Trovato')
            ->assertDontSee('Ama Zenou');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Org Un', 'role' => 'organisateur']);
        User::factory()->create(['name' => 'Client Deux', 'role' => 'client']);

        $this->actingAs($admin)
            ->get('/event-admin/utilisateurs?role=organisateur')
            ->assertOk()
            ->assertSee('Org Un')
            ->assertDontSee('Client Deux');
    }

    public function test_admin_can_change_a_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'client']);

        $this->actingAs($admin)
            ->put('/event-admin/utilisateurs/'.$user->id.'/role', ['role' => 'organisateur'])
            ->assertRedirect();

        $this->assertSame('organisateur', $user->fresh()->role);
    }

    public function test_admin_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put('/event-admin/utilisateurs/'.$admin->id.'/role', ['role' => 'client'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_an_invalid_role_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'client']);

        $this->actingAs($admin)
            ->put('/event-admin/utilisateurs/'.$user->id.'/role', ['role' => 'superuser'])
            ->assertSessionHasErrors('role');

        $this->assertSame('client', $user->fresh()->role);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete('/event-admin/utilisateurs/'.$user->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete('/event-admin/utilisateurs/'.$admin->id)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_change_roles_or_delete(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create(['role' => 'client']);

        $this->actingAs($user)
            ->put('/event-admin/utilisateurs/'.$target->id.'/role', ['role' => 'admin'])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete('/event-admin/utilisateurs/'.$target->id)
            ->assertForbidden();

        $this->assertSame('client', $target->fresh()->role);
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }
}
