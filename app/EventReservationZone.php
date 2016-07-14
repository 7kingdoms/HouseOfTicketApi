<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventReservationZone extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function reservation(){
    	return $this->belongsTo('App\EventReservation');
    }

    public function zone(){
    	return $this->belongsTo('App\EventZone');
    }
}
