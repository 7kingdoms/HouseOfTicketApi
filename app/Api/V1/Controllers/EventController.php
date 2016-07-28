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

use App\ProvinceHotDb;

use App\Api\V1\Helpers\SimpleCrypt;

class EventController extends Controller
{

  use Helpers;

    public function hotProvince(){

      $hotprovinces = ProvinceHotDb::all();

      $result = array();

      foreach($hotprovinces as $item){
        $result[$item->province][] = $item;
      }

      return $result;
    }
    public function zoneByPlace(Request $request,$id){

      $params = [
         'headers' => ['authorization' => $request->header('authorization')]
      ];

    //  return $params;
      $client = new \GuzzleHttp\Client();
      $response = $client->request('GET', env('MVAPI_URL') . 'get_zone_available/package/' . $id,$params);
      $resp = json_decode($response->getBody(),true);

      $allZone = EventMasterZone::select(['id','can_select','name','data_obj','data_seat_obj','price'])
                ->where('place_id',$id)->get()->keyBy('id')->toArray();

      $result = array();

      foreach($resp['data']['zones'] as $item){
        $result[$item['zoneID']] = $allZone[$item['zoneID']];
      }

      return $result;
    }

    public function eventPackageSeatBook(Request $request){

      $params = [
         'headers' => ['authorization' => $request->header('authorization')]
        ,'body' => '{
              "data": {
                  "packageID": '.$request->input('package_id').',
                  "seatID": ['.$request->input('seat').']
              }
          }'
      ];

    //  return $params;
      $client = new \GuzzleHttp\Client();
      $response = $client->request('POST', env('MVAPI_URL') . 'book_seat/package',$params);
      $resp = json_decode($response->getBody(),true);


      if(isset($resp['status']) and $resp['status'] == 1){
        return array(
           'status' => 1
          ,'t'      => SimpleCrypt::ecode($resp['data']['orderID'])
        );
      }

      return $resp;


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
      //return $resp;
      if($resp['status'] == 1){
        return array(
           'status' => 1
          ,'t'      => SimpleCrypt::ecode($resp['data']['orderID'])
        );
      }

      return $resp;

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
