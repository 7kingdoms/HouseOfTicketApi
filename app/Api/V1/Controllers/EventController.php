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

  use Helpers;

    private function ecode($value){
      $key = env('APP_KEY');
      return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $value, MCRYPT_MODE_CBC, md5(md5($key))));
    }

    private function decode($value){
      $key = env('APP_KEY');
      return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    }

    public function zoneByPlace($id){

      return EventMasterZone::select(['id','can_select','name','data_obj','data_seat_obj','price'])
                ->where('place_id',$id)->get()->keyBy('id')->toArray();
    }

    public function eventPackageBook(Request $request){

      $client = new \GuzzleHttp\Client();
      $response = $client->request('POST', env('MVAPI_URL') . 'book_zone/package',[
         'headers' => ['authorization' => $request->header('authorization')]
        ,'body' => '{
              "data": {
                  "packageID": '.$request->input('package_id').',
                  "zoneID": '.$request->input('zone_id').',
                  "quantity": '.$request->input('quantity').'
              }
          }'
      ]);
      $resp = json_decode($response->getBody(),true);
      return $resp;
      //return $this->ecode($resp['data']['orderID']);
    }

    public function seatByZone(Request $req,$id){


      $client = new \GuzzleHttp\Client();
      $response = $client->request('GET', env('MVAPI_URL') . 'get_seat_available/package/1/' . $id,[
        'headers' => ['authorization' => $req->header('authorization')]
      ]);

      $seats = EventMasterSeat::select('id','data_obj')->where('event_master_zone_id',$id)->get();
      $seatsKeyId = array();

      foreach($seats as $seat){
        $seatsKeyId[$seat->id] = $seat;
        $seatsKeyId[$seat->id]['class'] = 'disable';

      }

      $resp = json_decode($response->getBody(),true);

      //echo $response->getBody();

      //
      foreach($resp['data']['seats'] as $id){
        if(isset($seatsKeyId[$id])){
          $seatsKeyId[$id]['class'] = 'available';
        }
      }

      //echo "a";

      $seatKeyByObj = array();
      $l = "";
      foreach($seatsKeyId as $seat){
        $l = $seat->data_obj;
        $seatKeyByObj[$seat->data_obj] = $seat;
      }
      return $seatKeyByObj;

    }



}
