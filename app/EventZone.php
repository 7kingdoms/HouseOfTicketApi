<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventZone extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function event(){
    	return $this->belongsTo('App\Event');
    }

    public function seats(){
    	return $this->hasMany('App\EventSeat');
    }

}
