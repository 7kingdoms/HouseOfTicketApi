<?php
namespace App\Api\V1\Service;

use App\Api\V1\Service\PaymentTransactionService;

class PaymentBoontermService{

	public function CreatePayment($order){
      // $params = [
      //    'headers' => ['authorization' => 'Bearer '.$request->input('token')]
      //   ,'body' => '{
      //       "data": {
      //           "tel": "0823433522",
      //           "cust_name": "May",
      //           "cust_lastname": "Jii",
      //           "price": '.$order->total_price.',
      //           "ref_id": "'.$order->order_no.'",
      //           "valid_day": 0,
      //           "valid_hour": 1
      //       }
      //   }'
      // ];

      $params = [
         'headers' => ['authorization' => 'Bearer '.$request->input('token')]
        ,'body' => '{
          "tel": "0823433522", "cust_name": "May", "cust_lastname": "Jii",  "ref_id": "D1607270000000254", "valid_day": 0, "valid_hour": 1 }'
      ];

			$client = new \GuzzleHttp\Client();
      $response = $client->request('POST', env('MVAPI_URL') . 'order', $params);



      $resp = json_decode($response->getBody(),true);
      echo 'expire_time = '.$resp['expire_time'].'<br><br>';
      echo 'order_code = '.$resp['order_code'].'<br><br>';

      // echo $response->getBody();
      // print_r($resp);
      // print_r($params);
	}

}