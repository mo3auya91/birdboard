<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property int $project_id
 * @property boolean $is_completed
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task query()
 * @method static Builder|Task whereBody($value)
 * @method static Builder|Task whereCreatedAt($value)
 * @method static Builder|Task whereId($value)
 * @method static Builder|Task whereProjectId($value)
 * @method static Builder|Task whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Project $project
 * @method static Builder|Task whereIsCompleted($value)
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 */
class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_completed' => 'boolean'
    ];

    protected $touches = ['project'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return route('projects.tasks.show', [
            'project' => $this->project_id,
            'task' => $this->id,
        ]);
    }

    public function complete()
    {
        $this->update(['is_completed' => 1]);
    }

    public function inComplete()
    {
        $this->update(['is_completed' => 0]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function recordActivity($type)
    {
        $this->activities()->create([
            'project_id' => $this->project_id,
            'description' => $type,
        ]);
    }
}
