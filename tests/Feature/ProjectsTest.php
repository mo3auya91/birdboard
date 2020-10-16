<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function a_user_can_creat_a_project()
    {
        //$this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $attributes = Project::factory()->raw();
        $this->post(route('projects.store'), $attributes)->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))->assertSee($attributes['title']);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $user = User::factory()->create();
        $this->be($user);
        $this->withoutExceptionHandling();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->actingAs(User::factory()->create());

        $attributes = Project::factory()->raw(['title' => '']);

        $this->post(route('projects.store'), $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->actingAs(User::factory()->create());

        $attributes = Project::factory()->raw(['description' => '']);
        $this->post(route('projects.store'), $attributes)->assertSessionHasErrors('description');
    }

    /** @test */
    public function a_model_has_path()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $this->assertEquals(route('projects.show', ['project' => $project->id]), $project->path());
    }

    /** @test */
    public function a_model_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $this->assertInstanceOf(User::class, $project->owner);
    }

    /** @test */
    public function guests_cannot_create_a_project()
    {
        //$this->withoutExceptionHandling();
        $attributes = Project::factory()->raw();
        $this->post(route('projects.store'), $attributes)->assertRedirect(route('login'));
    }

    /** @test */
    public function guests_cannot_view_projects()
    {
        $this->get(route('projects.index'))->assertRedirect(route('login'));
    }

    /** @test */

    public function guests_cannot_view_a_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $this->get($project->path())->assertRedirect(route('login'));
    }

    /** @test */

    public function an_authenticated_user_cannot_view_others_projects()
    {
        $this->be($user = User::factory()->create());
        $other_user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $other_user->id]);
        $this->get($project->path())->assertStatus(403);
    }
}
