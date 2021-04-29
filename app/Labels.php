<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Labels extends Model
{
    protected $fillable = [
        'name','color','pipeline_id','created_by'
    ];

    public static $colors = [
        'bg-purple-medium bg-font-purple-medium',
        'bg-yellow-soft bg-font-yellow-soft',
        'bg-purple-intense bg-font-purple-intense',
        'bg-yellow-casablanca bg-font-yellow-casablanca',
    ];


}
