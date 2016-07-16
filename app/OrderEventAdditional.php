<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderEventAdditional extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function additional(){
    	return $this->belongsTo('App\EventAdditional', 'event_additional_id');
    }
}
