<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMasterZone extends Model
{
    //use SoftDeletes;

    protected $dates = ['deleted_at'];

    // public function place(){
    // 	return $this->belongsTo('App\Place');
    // }
    //
    // public function seats(){
    // 	return $this->hasMany('App\PlaceSeat');
    // }

}
