<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class InvitationsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function non_owners_can_not_invite_users()
    {
        $project = (new ProjectFactory())->create();
        $john = User::factory()->create();
        $assertInvitationIsForbidden = function () use ($john, $project) {
            $this->actingAs($john)
                ->post(route('project.invitations', ['project' => $project->id]))
                ->assertStatus(Response::HTTP_FORBIDDEN);
        };

        $assertInvitationIsForbidden();

        $project->invite($john);

        $assertInvitationIsForbidden();
    }

//    /** @test */
//    public function non_owners_can_not_see_invite_user_form()
//    {
//        //$this->withoutExceptionHandling();
//        $project = (new ProjectFactory())->create();
//        $john = User::factory()->create();
//        $this->actingAs($project->owner)
//            ->post(route('project.invitations', ['project' => $project->id]), [
//                'email' => $john->email
//            ])
//            ->assertRedirect($project->path());
//
//        $this->assertTrue($project->refresh()->members->contains($john));
//
//        $this->actingAs($john)
//            ->get($project->path())
////            ->assertDontSeeText('Invite a user');
//            //->assertDontSeeText('Invite a user');
//            ->assertSee('Invite a user');
//    }

    /** @test */
    public function a_project_owner_can_invite_a_user()
    {
        $project = (new ProjectFactory())->create();
        $john = User::factory()->create();
        $this->actingAs($project->owner)
            ->post(route('project.invitations', ['project' => $project->id]), [
                'email' => $john->email
            ])
            ->assertRedirect($project->path());

        $this->assertTrue($project->refresh()->members->contains($john));
    }

    /** @test */
    public function the_email_address_must_be_associated_with_birdboard_account()
    {
        $project = (new ProjectFactory())->create();
        $this->actingAs($project->owner)
            ->post(route('project.invitations', ['project' => $project->id]), [
                'email' => $this->faker->email
            ])
            ->assertSessionHasErrors([
                'email' => __('app.the_invited_email_address_must_be_associated_with_birdboard_account')
            ]);
    }

    /** @test */
    public function invited_users_may_update_project_details()
    {
        $project = (new ProjectFactory())->create();

        $project->invite($user = User::factory()->create());
        $this->signIn($user);
        $this->post(route('projects.tasks.store', ['project' => $project->id]), $task = ['body' => 'foo task']);
        $this->assertDatabaseHas('tasks', $task);
    }

    /** @test */
    public function a_user_can_see_all_projects_they_invited_to_on_their_dashboard()
    {
        $user = $this->signIn();

        $project = (new ProjectFactory())->create();

        $project->invite($user);

        $this->get(route('projects.index'))
            ->assertSee($project->getTranslation('title', app()->getLocale()));
    }
}
