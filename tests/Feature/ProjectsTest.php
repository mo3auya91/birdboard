<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function a_user_can_creat_a_project()
    {
        $this->signIn();

        $this->get(route('projects.create'))
            ->assertStatus(Response::HTTP_OK);

        $attributes = Project::factory()->raw();

        $response = $this->post(route('projects.store'), $attributes);

        $project = auth('web')->user()->projects()->first();

        $response->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'][app()->getLocale()])
            ->assertSee($attributes['description'][app()->getLocale()])
            ->assertSee($attributes['notes']);
    }

    /** @test */
    public function a_user_can_delete_a_project()
    {
        $project = (new ProjectFactory())->create();

        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    /** @test */
    public function unauthorized_users_can_not_delete_a_project()
    {
        $project = (new ProjectFactory())->create();

        $this->delete($project->path())
            ->assertRedirect(route('login'));

        $this->signIn();

        $this->delete($project->path())
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_access_edit_project_page()
    {
        $project = (new ProjectFactory())->create();

        $this->actingAs($project->owner)
            ->get(route('projects.edit', ['project' => $project->id]))
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function a_user_can_update_a_project()
    {
        $this->withoutExceptionHandling();
        $project = (new ProjectFactory())->create();

        $updated_attributes = ['notes' => $this->faker->sentence,];

        foreach (LaravelLocalization::getSupportedLocales() as $key => $locale) {
            $updated_attributes['title'][$key] = $this->faker->sentence;
            $updated_attributes['description'][$key] = $this->faker->sentence;
        }

        $this->actingAs($project->owner)
            ->patch($project->path(), $updated_attributes)
            ->assertRedirect($project->path());

        $this->get(route('projects.edit', ['project' => $project->id]))
            ->assertOk()
            ->assertSee($project->refresh()->getTranslation('title', app()->getLocale()));
        //it does not work to store array in sql light
        //$this->assertDatabaseHas('projects', $updated_attributes);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $project = (new ProjectFactory())->create();

        $this->actingAs($project->owner)
            ->get($project->path())
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
        $this->get(route('projects.edit', ['project' => $project->id]))->assertRedirect(route('login'));
        $this->post(route('projects.store'), $project->toArray())->assertRedirect(route('login'));
        $this->get(route('projects.index'))->assertRedirect(route('login'));
        $this->get($project->path())->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_cannot_view_others_projects()
    {
        $this->signIn();
        $project = Project::factory()->create();
        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_others_projects()
    {
        $this->signIn();
        $project = Project::factory()->create();
        $this->patch($project->path(), ['notes' => 'new note'])->assertStatus(403);
    }

    /** @test */
    public function a_project_can_invite_a_user()
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
