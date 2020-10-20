<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
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
        $project = Project::factory()->create();
        $attributes = Task::factory()->raw();
        $task = $project->addTask($attributes);
        $new_task_body = $this->faker->sentence;
        $attributes['body'] = $new_task_body;
        $this->patch(route('projects.tasks.update', [
            'project' => $project->id,
            'task' => $task->id,
        ]), ['body' => $new_task_body])
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('tasks', $attributes);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = auth('web')->user()->projects()->create(Project::factory()->raw());
        $attributes = Task::factory()->raw();
        $this->post(route('projects.tasks.store', [
            'project' => $project->id
        ]), $attributes);
        $this->get($project->path())
            ->assertSee($attributes['body']);
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = auth('web')->user()->projects()->create(Project::factory()->raw());
        $attributes = Task::factory()->raw();
        $task = $project->addTask($attributes);

        $new_task_body = $this->faker->sentence;

        $this->patch(route('projects.tasks.update', [
            'project' => $project->id,
            'task' => $task->id,
            'body' => $new_task_body,
            'is_completed' => 1,
        ]))->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('tasks', [
            'body' => $new_task_body,
            'is_completed' => 1,
            'project_id' => $project->id,
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $this->signIn();
        $project = auth('web')->user()->projects()->create(Project::factory()->raw());
        $attributes = Task::factory()->raw(['body' => '']);
        $this->post(route('projects.tasks.store', [
            'project' => $project->id,
        ]), $attributes)->assertSessionHasErrors('body');
    }
}
