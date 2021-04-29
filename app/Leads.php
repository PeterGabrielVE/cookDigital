<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    protected $fillable = [
        'id',
        'name',
        'price',
        'stage',
        'owner',
        'client',
        'source',
        'created_by',
    ];
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'owner')->first();
    }
    public function client()
    {
        return $this->hasOne('App\User', 'id', 'client')->first();
    }

    public function removeProjectLead($lead_id){
        return Projects::where('lead','=',$lead_id)->update(array('lead' => 0));
    }
}
