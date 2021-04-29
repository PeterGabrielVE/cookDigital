<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userprojects extends Model
{
    protected $fillable = [
        'user_id', 'project_id'
    ];

    public function project_assign_user(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function project_users(){
        return $this->hasMany('App\User','id','user_id');
    }


}
