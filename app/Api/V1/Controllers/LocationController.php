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

class LocationController extends Controller
{

    public function province(Request $request){
      return Province::select(['id','name_en'])->orderBy('name_en')->get();

    }
    public function amphur(Request $request){

      $rows = Amphur::orderBy('name_en')->get();

      $resp = [];

      foreach($rows as $row){
        $resp[$row->province_id][] = array( 'id'=>$row->id , 'name_en'=>$row->name_en);
      }

        return $resp;
    }

    public function district(Request $request){
        $rows = District::orderBy('name_en')->get();
        $resp = [];

        foreach($rows as $row){
          $resp[$row->amphur_id][] = array( 'id'=>$row->id , 'name_en'=>$row->name_en);
        }

        return $resp;
    }

    public function zipcode(Request $request){
        $rows = Zipcode::select(['district_id','zipcodes'])->get();
        $resp = [];

        foreach($rows as $row){
          $resp[$row->district_id] = $row->zipcodes;
        }

        return $resp;
    }


}
