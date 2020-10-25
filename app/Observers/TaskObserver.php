<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the task "created" event.
     *
     * @param Task $task
     * @return void
     */
    public function created(Task $task)
    {
        $task->recordActivity('created_task');
    }

    /**
     * Handle the task "updated" event.
     *
     * @param Task $task
     * @return void
     */
    public function updated(Task $task)
    {
        $task->isDirty(['is_completed']);
        if ($task->isDirty(['is_completed'])) {
            $type = $task->is_completed ? 'completed_task' : 'incomplete_task';
            $task->recordActivity($type);
        } else {
            $task->recordActivity('updated_task');
        }
    }

    /**
     * Handle the task "deleted" event.
     *
     * @param Task $task
     * @return void
     */
    public function deleted(Task $task)
    {
        $task->recordActivity('deleted_task');
    }

    /**
     * Handle the task "restored" event.
     *
     * @param Task $task
     * @return void
     */
    public function restored(Task $task)
    {
        //
    }

    /**
     * Handle the task "force deleted" event.
     *
     * @param Task $task
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}
