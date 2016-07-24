<?php

namespace App\Api\V1\Controllers;


use Validator;
use Config;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;

use App\Province;
use App\Amphur;
use App\District;
use App\Zipcode;

use App\Event;
use App\EventType;
use App\EventRound;
use App\EventZone;
use App\EventSeat;

use App\EventMasterZone;
use App\EventMasterSeat;

use App\PlaceZone;
use App\PlaceSeat;

class EventController extends Controller
{

    public function zoneByPlace($id){

      return EventMasterZone::select(['id','can_select','name','data_obj','data_seat_obj','price'])
                ->where('place_id',$id)->get()->keyBy('id')->toArray();
    }

    public function seatByZone($id){
      return EventMasterSeat::select('id','data_obj')->where('event_master_zone_id',$id)->get()->keyBy('data_obj')->toArray();
    }



}
