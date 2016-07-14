<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function type(){
        return $this->belongsTo('App\EventType');
    }

    public function rounds(){
        return $this->hasMany('App\EventRound');
    }
    public function zones(){
    	return $this->hasMany('App\EventZone');
    }



    public function reservations(){
        return $this->hasMany('App\EventReservation');
    }


}
