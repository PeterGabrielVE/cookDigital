<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leadstages extends Model
{
    protected $fillable = [
        'name',
        'created_by',
        'order',
        'color',
    ];


    protected $hidden = [

    ];

    public function leads()
    {
        return $this->hasMany('App\Leads', 'stage', 'id')->orderBy('item_order');
    }

    public function user_leads(){
//        return Leadstages::select('leads.*','leadstages.name as stage_name' )->leftjoin('leads','leads.stage','=','leadstages.id')->where('leads.owner','=',\Auth::user()->id)->get();
        return Leads::where('stage','=',$this->id)->where('owner','=',\Auth::user()->id)->get();
    }
}
