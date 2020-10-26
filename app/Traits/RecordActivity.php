<?php


namespace App\Traits;


use App\Models\Activity;
use App\Models\Project;
use Illuminate\Support\Arr;

trait RecordActivity
{
    public $oldAttributes = [];

    public static function bootRecordActivity()
    {
        static::updating(function ($model) {
            $model->oldAttributes = $model->getOriginal();
        });

        foreach (self::recordableEvents() as $recordableEvent) {
            static::$recordableEvent(function ($model) use ($recordableEvent) {
                $model->recordActivity($model->activityDescription($recordableEvent));
            });
        }
    }

    public function activityDescription($description)
    {
        if ((class_basename($this) === 'Task') && ($description == 'updated')) {
            $this->isDirty(['is_completed']);
            if ($this->isDirty(['is_completed'])) {
                $description = $this->is_completed ? 'completed_task' : 'incomplete_task';
            }
        } else {
            $description = $description . '_' . strtolower(class_basename($this));
        }
        return $description;
    }

    protected static function recordableEvents(): array
    {
        return isset(static::$recordableEvents) ? static::$recordableEvents : ['created', 'updated', 'deleted'];
    }

    public function recordActivity($type)
    {
        $this->activities()->create([
            'description' => $type,
            'changes' => $this->activityChanges(),
            'project_id' => $this instanceof Project ? $this->id : $this->project_id,
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function activityChanges()
    {
        if ($this->wasChanged()) {
            return [
                'before' => array_diff($this->oldAttributes, $this->getAttributes()),
                'after' => Arr::except($this->getChanges(), ['updated_at']),
            ];
        }
        return null;
    }
}
