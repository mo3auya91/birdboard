<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function a_user_can_creat_a_project()
    {
        //$this->withoutExceptionHandling();

        $this->signIn();

        $this->get(route('projects.create'))
            ->assertStatus(Response::HTTP_OK);
        $attributes = Project::factory()->raw();
        unset($attributes['owner_id']);
        //assume database is empty and the project id is 1
        $this->post(route('projects.store'), $attributes)
            ->assertRedirect(route('projects.show', ['project' => 1]));

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))->assertSee($attributes['title']);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $user = User::factory()->create();
        $this->signIn($user);
        $this->withoutExceptionHandling();
        $project = auth('web')->user()->projects()->create(Project::factory()->raw());
        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        $attributes = Project::factory()->raw(['title' => '']);

        $this->post(route('projects.store'), $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();
        $attributes = Project::factory()->raw(['description' => '']);
        $this->post(route('projects.store'), $attributes)->assertSessionHasErrors('description');
    }

    /** @test */
    public function a_model_has_path()
    {
        $user = User::factory()->create();
        $project = $user->projects()->create(Project::factory()->raw());
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
    public function a_model_can_add_a_task()
    {
        $user = User::factory()->create();
        $project = $user->projects()->create(Project::factory()->raw());
        $task = $project->addTask(['body' => 'Test Task']);
        $this->assertCount(1, $project->tasks);
        $this->assertTrue($project->tasks->contains($task));
    }

    /** @test */
    public function guests_cannot_control_a_project()
    {
        $user = User::factory()->create();
        $project = $user->projects()->create(Project::factory()->raw());

        $this->get(route('projects.create'))->assertRedirect(route('login'));
        $this->post(route('projects.store'), $project->toArray())->assertRedirect(route('login'));
        $this->get(route('projects.index'))->assertRedirect(route('login'));
        $this->get($project->path())->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_cannot_view_others_projects()
    {
        $this->signIn();
        $other_user = User::factory()->create();
        $project = $other_user->projects()->create(Project::factory()->raw());
        $this->get($project->path())->assertStatus(403);
    }
}
