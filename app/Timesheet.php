<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'task_id',
        'date',
        'hours',
        'remark',
    ];

    public function task()
    {
        return Task::where('id', '=', $this->task_id)->first();
    }

    public function user()
    {
        return User::where('id', '=', $this->user_id)->first();
    }

    public function project()
    {
        return $this->hasOne('App\Projects', 'id', 'project_id');
    }
}
