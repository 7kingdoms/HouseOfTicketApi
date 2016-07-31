<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaceSeat extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function zone(){
    	return $this->belongsTo('App\PlaceZone');
    }

}
