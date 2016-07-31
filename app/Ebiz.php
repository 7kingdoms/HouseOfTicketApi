<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ebiz extends Model
{
  protected $casts = [
      'data' => 'array'
  ];
}
