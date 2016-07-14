<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventReservation extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function zones(){
    	return $this->hasMany('App\EventReservationZone');
    }

    public function event(){
        return $this->belongsTo('App\Event');
    }

}
