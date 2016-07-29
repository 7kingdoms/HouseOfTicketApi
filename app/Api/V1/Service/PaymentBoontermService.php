<?php
namespace App\Api\V1\Service;

use App\Api\V1\Service\PaymentTransactionService;
use App\Api\V1\Service\OrderService;

class PaymentBoontermService{

	public function CreatePayment($order, $request){
    $valid_day = config('payment.boonterm.valid_day');
    $valid_hour = config('payment.boonterm.valid_hour');

		// echo $order->user->name;
      $params = [
         'headers' => ['authorization' => 'Bearer '.$request->input('token')], 
         'body' => '{
          	"tel": "'.$order->user->phone.'", 
          	"cust_name": "'.$order->user->name.'", 
            "cust_lastname": "'. $order->user->surname.'",  
          	"price": "'. ($order->total_price*100).'",  
          	"ref_id": "'.$order->order_no.'", 
          	"valid_day": '.$valid_day.', 
          	"valid_hour": '.$valid_hour.'
          }'
      ];

      $transServ = new PaymentTransactionService();
      $transServ->SaveRequestPayment($order, $params);

			$client = new \GuzzleHttp\Client();
      $response = $client->request('POST', config('payment.boonterm.api_url') . 'order', $params);

      $resp = json_decode($response->getBody(),true);

      $transServ_resp = new PaymentTransactionService();
      $transServ_resp->SaveResponseFrontPayment($order, $resp);

      $orderServ = new OrderService();
      $order = $orderServ->SetStatusPaymentBoonterm($order);

      return $resp;
	}

}