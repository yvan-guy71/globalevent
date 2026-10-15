<?php

namespace Tests\Feature;

use App\Models\CompetitionCandidate;
use App\Models\CompetitionVote;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    private function competition(?User $owner = null): Event
    {
        $owner ??= User::factory()->create();

        return Event::factory()->create([
            'user_id' => $owner->id,
            'type' => 'competition',
            'status' => 'published',
        ]);
    }

    public function test_vote_page_renders_for_competition(): void
    {
        $event = $this->competition();
        CompetitionCandidate::factory()->count(3)->create(['event_id' => $event->id]);

        $this->get('/evenements/'.$event->slug.'/candidats')
            ->assertOk()
            ->assertSee('Classement');
    }

    public function test_vote_page_is_not_available_for_other_types(): void
    {
        $event = Event::factory()->create(['type' => 'concert', 'status' => 'published']);

        $this->get('/evenements/'.$event->slug.'/candidats')->assertNotFound();
    }

    public function test_only_owner_can_add_candidate(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $event = $this->competition($owner);

        $this->post('/evenements/'.$event->slug.'/candidats', ['name' => 'Team Étoile'])
            ->assertRedirect('/connexion');

        $this->actingAs($other)
            ->post('/evenements/'.$event->slug.'/candidats', ['name' => 'Team Étoile'])
            ->assertForbidden();

        $this->actingAs($owner)
            ->post('/evenements/'.$event->slug.'/candidats', ['name' => 'Team Étoile', 'description' => 'Présentation'])
            ->assertRedirect(route('competitions.index', $event));

        $this->assertDatabaseHas('competition_candidates', [
            'event_id' => $event->id,
            'name' => 'Team Étoile',
            'slug' => 'team-etoile',
            'is_active' => true,
        ]);
    }

    public function test_owner_can_update_and_delete_candidate(): void
    {
        $owner = User::factory()->create();
        $event = $this->competition($owner);
        $candidate = CompetitionCandidate::factory()->create(['event_id' => $event->id]);

        $this->actingAs($owner)
            ->put('/evenements/'.$event->slug.'/candidats/'.$candidate->id, ['name' => 'Nouveau Nom'])
            ->assertRedirect(route('competitions.index', $event));

        $this->assertSame('Nouveau Nom', $candidate->fresh()->name);

        $this->actingAs($owner)
            ->delete('/evenements/'.$event->slug.'/candidats/'.$candidate->id)
            ->assertRedirect(route('competitions.index', $event));

        $this->assertDatabaseCount('competition_candidates', 0);
    }

    public function test_candidate_from_another_event_returns_404(): void
    {
        $owner = User::factory()->create();
        $event = $this->competition($owner);
        $other = $this->competition($owner);
        $candidate = CompetitionCandidate::factory()->create(['event_id' => $other->id]);

        $this->actingAs($owner)
            ->put('/evenements/'.$event->slug.'/candidats/'.$candidate->id, ['name' => 'Pirate'])
            ->assertNotFound();
    }

    public function test_user_can_vote_for_then_toggle_and_remove_vote(): void
    {
        $owner = User::factory()->create();
        $voter = User::factory()->create();
        $event = $this->competition($owner);
        $candidate = CompetitionCandidate::factory()->create(['event_id' => $event->id]);

        $url = '/evenements/'.$event->slug.'/candidats/'.$candidate->id.'/vote';

        $this->actingAs($voter)->post($url, ['value' => 1])->assertRedirect();
        $this->assertDatabaseHas('competition_votes', [
            'candidate_id' => $candidate->id,
            'user_id' => $voter->id,
            'value' => 1,
        ]);
        $this->assertSame(1, $candidate->fresh()->score);

        $this->actingAs($voter)->post($url, ['value' => -1])->assertRedirect();
        $this->assertDatabaseCount('competition_votes', 1);
        $this->assertSame(-1, $candidate->fresh()->score);

        $this->actingAs($voter)->post($url, ['value' => -1])->assertRedirect();
        $this->assertDatabaseCount('competition_votes', 0);
        $this->assertSame(0, $candidate->fresh()->score);
    }

    public function test_vote_value_must_be_one_or_minus_one(): void
    {
        $owner = User::factory()->create();
        $voter = User::factory()->create();
        $event = $this->competition($owner);
        $candidate = CompetitionCandidate::factory()->create(['event_id' => $event->id]);

        $this->actingAs($voter)
            ->post('/evenements/'.$event->slug.'/candidats/'.$candidate->id.'/vote', ['value' => 5])
            ->assertSessionHasErrors('value');
    }

    public function test_owner_cannot_vote_in_own_competition(): void
    {
        $owner = User::factory()->create();
        $event = $this->competition($owner);
        $candidate = CompetitionCandidate::factory()->create(['event_id' => $event->id]);

        $this->actingAs($owner)
            ->post('/evenements/'.$event->slug.'/candidats/'.$candidate->id.'/vote', ['value' => 1])
            ->assertForbidden();
    }

    public function test_guest_cannot_vote(): void
    {
        $owner = User::factory()->create();
        $event = $this->competition($owner);
        $candidate = CompetitionCandidate::factory()->create(['event_id' => $event->id]);

        $this->post('/evenements/'.$event->slug.'/candidats/'.$candidate->id.'/vote', ['value' => 1])
            ->assertRedirect('/connexion');
    }

    public function test_score_is_sum_of_votes_and_leaderboard_is_sorted(): void
    {
        $owner = User::factory()->create();
        $event = $this->competition($owner);

        $leader = CompetitionCandidate::factory()->create(['event_id' => $event->id, 'name' => 'Zzz Leader']);
        $second = CompetitionCandidate::factory()->create(['event_id' => $event->id, 'name' => 'Aaa Second']);

        foreach (User::factory()->count(3)->create() as $voter) {
            CompetitionVote::create(['event_id' => $event->id, 'candidate_id' => $leader->id, 'user_id' => $voter->id, 'value' => 1]);
        }

        CompetitionVote::create(['event_id' => $event->id, 'candidate_id' => $leader->id, 'user_id' => User::factory()->create()->id, 'value' => -1]);
        CompetitionVote::create(['event_id' => $event->id, 'candidate_id' => $second->id, 'user_id' => User::factory()->create()->id, 'value' => 1]);

        $this->assertSame(2, $leader->fresh()->score);
        $this->assertSame(1, $second->fresh()->score);

        $this->get('/evenements/'.$event->slug.'/candidats')
            ->assertOk()
            ->assertSeeInOrder(['Zzz Leader', 'Aaa Second']);
    }

    public function test_competition_section_is_shown_on_event_page(): void
    {
        $event = $this->competition();
        CompetitionCandidate::factory()->create(['event_id' => $event->id, 'name' => 'Candidat Vedette']);

        $this->get('/evenements/'.$event->slug)
            ->assertOk()
            ->assertSee('Candidats & votes', false)
            ->assertSee('Candidat Vedette');
    }
}
