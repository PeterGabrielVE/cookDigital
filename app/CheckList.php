<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    protected $fillable = [
        'name', 'task_id','created_by', 'status',
    ];
}
