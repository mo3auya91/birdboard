<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_project_records_activity()
    {
        $project = (new ProjectFactory())->create();
        $this->assertCount(1, $project->activities);
        $this->assertEquals('created', $project->activities()->first()->description);
    }

    /** @test */
    public function updating_a_project_records_activity()
    {
        $project = (new ProjectFactory())->create();
        $project->update(['title' => 'new title']);
        $this->assertCount(2, $project->activities);
        $this->assertEquals('updated', $project->activities->last()->description);
    }

    /** @test */
    public function creating_a_new_task_records_project_activity()
    {
        $project = (new ProjectFactory())->witTasks(1)->create();
        $this->assertCount(2, $project->activities);
        $this->assertEquals('created_task', $project->activities->last()->description);
    }

    /** @test */
    public function completing_a_new_task_records_project_activity()
    {
        $this->withoutExceptionHandling();
        $project = (new ProjectFactory())->witTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), ['is_completed' => 1]);

        $this->assertCount(3, $project->activities);
        $this->assertEquals('completed_task', $project->activities->last()->description);
    }
}
