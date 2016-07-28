<?php
namespace App\Api\V1\Service;

use App\Api\V1\Service\PaymentTransactionService;
use App\Api\V1\Service\OrderService;

class PaymentBoontermService{

	public function CreatePayment($order, $request){
		// echo $order->user->name;
      $params = [
         'headers' => ['authorization' => 'Bearer '.$request->input('token')], 
         'body' => '{
          	"tel": "'.$order->user->phone.'", 
          	"cust_name": "'.$order->user->name.'", 
          	"cust_lastname": "'. $order->user->surname.'",  
          	"ref_id": "'.$order->order_no.'", 
          	"valid_day": 0, 
          	"valid_hour": 1 
          }'
      ];

      $transServ = new PaymentTransactionService();
      $transServ->SaveRequestPayment($order, $params);

			$client = new \GuzzleHttp\Client();
      $response = $client->request('POST', env('MVAPI_URL') . 'order', $params);

      $resp = json_decode($response->getBody(),true);

      $transServ_resp = new PaymentTransactionService();
      $transServ_resp->SaveResponseFrontPayment($order, $resp);

      $orderServ = new OrderService();
      $order = $orderServ->SetStatusPaymentBoonterm($order);

      return $resp;
	}

}