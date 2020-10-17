<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

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
