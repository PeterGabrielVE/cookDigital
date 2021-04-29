<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    protected $fillable = [
        'file','name','extension','file_size','created_by','task_id','user_type'
    ];
}
