<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BugStatus extends Model
{
    protected $fillable = [
        'title',
        'created_by',
        'order',
    ];

    public function bugs($project_id)
    {
        return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->orderBy('order')->get();
    }

    public function assign_bugs($project_id)
    {
        return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->where('assign_to', '=', \Auth::user()->id)->orderBy('order')->get();
    }
}
