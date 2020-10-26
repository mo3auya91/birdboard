<?php

namespace App\Models;

use App\Traits\RecordActivity;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $notes
 * @property int $owner_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Project newModelQuery()
 * @method static Builder|Project newQuery()
 * @method static Builder|Project query()
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereDeletedAt($value)
 * @method static Builder|Project whereDescription($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereTitle($value)
 * @method static Builder|Project whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read User $owner
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static Builder|Project whereOwnerId($value)
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @method static Builder|Project whereNotes($value)
 */
class Project extends Model
{
    use HasFactory;
    use RecordActivity;

    /** @var array */
    protected $guarded = [];

    public function path()
    {
        return route('projects.show', ['project' => $this->id]);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function addTask(array $data)
    {
        return $this->tasks()->create($data);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->latest();
    }
}
