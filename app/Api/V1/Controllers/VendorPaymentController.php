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

use App\Ebiz;

class VendorPaymentController extends Controller
{
  public function ebizCallback(Request $request){
    $ebiz = new Ebiz;
    $ebiz->data = $request->all();

    $ebiz->save();

    return  [
      'order_id' => $request->input('order_id')
      ,'response_code' => 0
      ,'detail' => 'success'
    ];
  }
  public function ebizViewCallback(Request $request){


    return  Ebiz::all();
  }

}
