<?php

namespace Tests\Feature;

use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitaionsTest extends TestCase
{
    use RefreshDatabase;

    function test_non_owner_may_not_invite_user()
    {
        $project = ProjectFactory::create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);

    }

    function test_a_project_can_invite_a_user()
    {
        $this->withoutExceptionHandling();

        $project = ProjectFactory::create();

        $userToInvite = factory(User::class)->create();

        $this->actingAs($project->owner)->post($project->path() . '/invitations', [
            'email' => $userToInvite->email
        ])->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));


    }

    function test_invited_users_can_update_project_details()
    {
        $project = ProjectFactory::ownedBy(factory(User::class)->create())->create();

        $project->invite($newUser = factory(User::class)->create());

        $this->signIn($newUser);

        $this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'foo']);

        $this->assertDatabaseHas('tasks', $task);
    }

    function test_invited_email_address_must_be_on_birdboard()
    {

        $project = ProjectFactory::create();

        $this->actingAs($project->owner)->post($project->path() . '/invitations', [
            'email' => 'someone@example.com'
        ])->assertSessionHasErrors('email', null, 'invitations');
    }
}
