<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAdditional extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


}
