<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    public function mediafiles(){
        return $this->hasMany('App\ContentMedia');
    }
  
    public function category(){
      return $this->hasOne('App\ContentCategory');
    }
}
