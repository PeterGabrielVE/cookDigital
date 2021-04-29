<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpensesCategory extends Model
{
    protected $fillable = [
        'name', 'created_by', 'description',
    ];


    protected $hidden = [

    ];
}
