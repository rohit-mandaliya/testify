<?php

namespace App\Models;

use App\Enums\taskType;
use App\Enums\priorityType;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'type' =>  taskType::class,
        'priority' =>  priorityType::class,
    ];

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'assignee');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'title', 'description', 'status', 'type', 'priority', 'app_version', 'due_date', 'is_active'])
            ->useLogName('Ticket');
    }
}
