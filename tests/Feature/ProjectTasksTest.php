<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function guests_cannot_control_a_task()
    {
        $project = Project::factory()->create();
        $attributes = Task::factory()->raw();
        $this->post(route('projects.tasks.store', [
            'project' => $project->id
        ]), $attributes)->assertRedirect(route('login'));
    }

    /** @test */
    public function only_the_project_owner_can_add_a_task()
    {
        $this->signIn();
        //$project = auth('web')->user()->projects()->create(Project::factory()->raw());
        $project = Project::factory()->create();
        $attributes = Task::factory()->raw();
        $this->post(route('projects.tasks.store', [
            'project' => $project->id
        ]), $attributes)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('tasks', $attributes);
    }

    /** @test */
    public function only_the_project_owner_can_update_a_task()
    {
        $this->signIn();
        $project = (new ProjectFactory())->witTasks(1)->create();
        $task = $project->tasks()->first();
        $attributes['body'] = $this->faker->sentence;
        $this->patch($task->path(), $attributes)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('tasks', $attributes);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $project = (new ProjectFactory())->create();
        $attributes = Task::factory()->raw();
        $this->actingAs($project->owner)
            ->post(route('projects.tasks.store', [
                'project' => $project->id
            ]), $attributes);
        $this->get($project->path())
            ->assertSee($attributes['body']);
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        $project = (new ProjectFactory())->witTasks(1)->create();
        $new_task_body = $this->faker->sentence;
        $attributes = ['body' => $new_task_body, 'is_completed' => 1];
        $this->actingAs($project->owner)
            ->patch($project->tasks()->first()->path(), $attributes)
            ->assertStatus(Response::HTTP_OK);
        //->assertJson($attributes);

        $this->assertDatabaseHas('tasks', $attributes);
    }

    /** @test */
    public function a_task_can_be_completed()
    {
        $project = (new ProjectFactory())->witTasks(1)->create();
        $attributes = ['is_completed' => 1];
        $task = $project->tasks()->first();
        $this->actingAs($project->owner)
            ->patch($task->path(), $attributes)
            ->assertStatus(Response::HTTP_OK);

        $this->assertTrue($task->refresh()->is_completed);

        $this->assertDatabaseHas('tasks', $attributes);
    }

    /** @test */
    public function a_task_can_be_incomplete()
    {
        $this->withoutExceptionHandling();
        $project = (new ProjectFactory())->witTasks(1)->create();
        $attributes = ['is_completed' => 1];
        $task = $project->tasks()->first();
        $this->actingAs($project->owner)
            ->patch($task->path(), $attributes)
            ->assertStatus(Response::HTTP_OK);

        $this->assertTrue($task->refresh()->is_completed);

        $this->patch($project->tasks()->first()->path(), ['is_completed' => 0])
            ->assertStatus(Response::HTTP_OK);

        $this->assertFalse($task->refresh()->is_completed);
        $this->assertDatabaseHas('tasks', ['is_completed' => 0]);
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $project = (new ProjectFactory())->create();
        $attributes = Task::factory()->raw(['body' => '']);
        $this->actingAs($project->owner)
            ->post(route('projects.tasks.store', [
                'project' => $project->id,
            ]), $attributes)->assertSessionHasErrors('body');
    }
}
