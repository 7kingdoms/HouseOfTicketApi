<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public function zones(){
    	return $this->hasMany('App\PlaceZone');
    }


}
