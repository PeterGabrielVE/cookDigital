<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstimationProduct extends Model
{
    protected $fillable = [
        'estimation_id',
        'name',
        'price',
        'quantity',
        'description',
    ];
}
