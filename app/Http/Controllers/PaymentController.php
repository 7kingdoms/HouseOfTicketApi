<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Content;

class PaymentController extends Controller
{
	public function payment2c2p($order_id){
		return config('2c2p.merchant_id');
		// return $order_id;
	}
}