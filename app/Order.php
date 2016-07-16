<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public function user(){
    	return $this->belongsTo('App\User');

    }
    
    public function seats(){
    	return $this->hasMany('App\OrderSeat');
    }


    public function shipping_vendor(){
    	return $this->belongsTo('App\ShippingVendor');

    }
}
