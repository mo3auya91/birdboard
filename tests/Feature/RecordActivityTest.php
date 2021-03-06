<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class RecordActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function creating_a_project()
    {
        $project = (new ProjectFactory())->create();
        $this->assertCount(1, $project->activities);
        tap($project->activities->last(), function ($activity) {
            $this->assertEquals('created_project', $activity->description);
            $this->assertNull($activity->changes);
        });
    }

    /** @test */
    public function updating_a_project()
    {
        $project = (new ProjectFactory())->create();
        $original_title = $project->getTranslations('title');
        //dd($original_title);
        //$project->update(['title' => 'new title']);
        $updated_attributes = [];
        foreach (LaravelLocalization::getSupportedLocales() as $key => $locale) {
            $updated_attributes['title'][$key] = 'new title';
        }
        $project->update($updated_attributes);
        $this->assertCount(2, $project->activities);
        tap($project->activities->last(), function ($activity) use ($original_title, $updated_attributes) {
            $this->assertEquals('updated_project', $activity->description);
            $changes = $activity->changes;
            unset($changes['before']['updated_at']);
            unset($changes['before']['created_at']);
//            dd([
//                'before' => ['title' => $original_title],
//                'after' => $updated_attributes,
//            ], $changes);
            $this->assertEqualsCanonicalizing([
                'before' => ['title' => $original_title],
                'after' => $updated_attributes,
            ], $changes);
        });
    }

    /** @test */
    public function creating_a_new_task()
    {
        $project = (new ProjectFactory())->witTasks(1)->create();
        $this->assertCount(2, $project->activities);
        $task = $project->tasks->last();
        tap($project->activities->last(), function ($activity) use ($task) {
            $this->assertEquals('created_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals($task->body, $activity->subject->body);
        });
    }

    /** @test */
    public function completing_a_new_task_records_project_activity()
    {
        $this->withoutExceptionHandling();
        $project = (new ProjectFactory())->witTasks(1)->create();
        $task = $project->tasks->first();

        $this->actingAs($project->owner)
            ->patch($task->path(), ['is_completed' => 1]);

        $this->assertCount(3, $project->activities);
        $this->assertEquals('completed_task', $project->activities->last()->description);

        tap($project->activities->last(), function ($activity) use ($task) {
            $this->assertEquals('completed_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            //$this->assertEquals($task->body, $activity->subject->body);
        });
    }

    /** @test */
    public function incomplete_a_new_task()
    {
        $this->withoutExceptionHandling();
        $project = (new ProjectFactory())->witTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), ['is_completed' => 1]);

        $this->assertCount(3, $project->activities);
        $this->assertEquals('completed_task', $project->activities->last()->description);

        $this->patch($project->tasks->first()->path(), ['is_completed' => 0]);
        $project->refresh();
        $this->assertCount(4, $project->activities);
        $this->assertEquals('incomplete_task', $project->activities->last()->description);
    }

    /** @test */
    public function delete_a_task()
    {
        $this->withoutExceptionHandling();
        $project = (new ProjectFactory())->witTasks(1)->create();
        $project->tasks()->first()->delete();
        $this->assertCount(3, $project->activities);
    }
}
