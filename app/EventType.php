<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventType extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function events(){
        return $this->hasMany('App\Event');
    }
}
