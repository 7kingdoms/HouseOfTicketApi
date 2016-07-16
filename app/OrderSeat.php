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


}
