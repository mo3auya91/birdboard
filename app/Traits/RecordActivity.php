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
            'user_id' => ($this->project ?? $this)->owner->id,
            'project_id' => $this instanceof Project ? $this->id : $this->project_id,
            'description' => $type,
            'changes' => $this->activityChanges(),
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function activityChanges()
    {
        if ($this->wasChanged()) {
            $getChanges = [];
            foreach ($this->getChanges() as $key => $change) {
                $getChanges[$key] = is_json($change) ? json_decode($change, true) : $change;
            }
            $getAttributes = [];
            foreach ($this->getAttributes() as $key => $attribute) {
                $getAttributes[$key] = is_json($attribute) ? json_decode($attribute, true) : $attribute;
            }

            $diff = array_diff(array_map('json_encode', $this->oldAttributes), array_map('json_encode', $getAttributes));

            // Json decode the result
            $diff = array_map('json_decode', $diff);
            // Json decode the result
            return [
                'before' =>  $diff,
                'after' => Arr::except($getChanges, ['updated_at']),
            ];
        }
        return null;
    }
}
