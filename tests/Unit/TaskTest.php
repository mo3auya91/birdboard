<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_project()
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(Project::class, $task->project);
    }

    /** @test */
    public function it_has_a_path()
    {
        $task = Task::factory()->create();
        $this->assertEquals(route('projects.tasks.show', [
            'project' => $task->project->id,
            'task' => $task->id,
        ]), $task->path());
    }
}