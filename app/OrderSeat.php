<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSeat extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function order(){
    	return $this->belongsTo('App\Order');
    }
    public function zone(){
    	return $this->belongsTo('App\EventZone', 'event_zone_id');
    }

    public function seat(){
    	return $this->belongsTo('App\EventSeat', 'event_seat_id');
    }

    public function event(){
    	return $this->belongsTo('App\Event', 'event_id');
    }
    
    public function round(){
    	return $this->belongsTo('App\EventRound', 'event_round_id');
    }



}
