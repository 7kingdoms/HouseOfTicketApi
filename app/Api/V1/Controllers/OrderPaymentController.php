<?php

namespace App\Api\V1\Controllers;

use JWTAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Api\V1\Service\OrderService;
use App\Api\V1\Service\Payment2c2pService;
use App\Api\V1\Service\PaymentTransactionService;
use App\Order;

use App\Api\V1\Helpers\SimpleCrypt;

class OrderPaymentController extends Controller
{

  public function saveState(Request $request){



		$order_id = SimpleCrypt::decode($request->input('t'));

		$payment_vendor_id = $request->input('payment_vendor_id');
		$shipping_vendor_id = $request->input('shipping_vendor_id');


		$orderServ = new OrderService();
		$order = $orderServ->GetOrderByID($order_id);

		$user = JWTAuth::parseToken()->authenticate();


		if(!$order or $order->user_id != $user->id){
			return "false";
		}

		$order_price = $orderServ->CalculateOrderPrice($order);
		$shipping_price = $orderServ->GetShippingPrice($shipping_vendor_id);
		$order->order_no = $orderServ->GenerateOrderNo($order);
		$order->invoice_no = $orderServ->GenerateInvoiceNo($order);
		$order->payment_vendor_id = $payment_vendor_id;
		$order->shipping_vendor_id = $shipping_vendor_id;
		$order->order_price = $order_price;
		$order->shipping_price = $shipping_price;
		$order->total_price = $order_price+$shipping_price;
		$order->save();

	}

	public function submit(Request $request){

		$order_id = SimpleCrypt::decode($request->input('t'));

		$payment_vendor_id = $request->input('payment_vendor_id');
		$shipping_vendor_id = $request->input('shipping_vendor_id');


		$orderServ = new OrderService();
		$order = $orderServ->GetOrderByID($order_id);

		// $user = JWTAuth::parseToken()->authenticate();
		//
		//
		// if(!$order or $order->user_id != $user->id){
		// 	return "false";
		// }

		$order_price = $orderServ->CalculateOrderPrice($order);
		$shipping_price = $orderServ->GetShippingPrice($shipping_vendor_id);
		$order->order_no = $orderServ->GenerateOrderNo($order);
		$order->invoice_no = $orderServ->GenerateInvoiceNo($order);
		$order->payment_vendor_id = $payment_vendor_id;
		$order->shipping_vendor_id = $shipping_vendor_id;
		$order->order_price = $order_price;
		$order->shipping_price = $shipping_price;
		$order->total_price = $order_price+$shipping_price;
		$order->save();

		if($orderServ->IsExpired($order))
		{
			$order = $orderServ->SetStatusExpired($order);
			$order_id_enc = SimpleCrypt::ecode($order->id);

			return redirect(env('FRONTEND_PAYMENT_2C2P_EXPIRED').'?t='.$order_id_enc);
		}

		if($order->payment_vendor_id == 2){

			$pay2c2pServ = new Payment2c2pService();
			$order = $pay2c2pServ->CreatePayment($order);
		}
		else{


      $params = [
         'headers' => ['authorization' => 'Bearer '.$request->input('token')]
        ,'body' => '{
            "data": {
                "tel": "0823433522",
                "cust_name": "May",
                "cust_lastname": "Jii",
                "price": '.$order->total_price.',
                "ref_id": "'.$order->order_no.'",
                "valid_day": 0,
                "valid_hour": 1
            }
        }'
      ];

      $params = [
         'headers' => ['authorization' => 'Bearer '.$request->input('token')]
        ,'body' => '{
          "tel": "0823433522", "cust_name": "May", "cust_lastname": "Jii",  "ref_id": "D1607270000000254", "valid_day": 0, "valid_hour": 1 }'
      ];

			$client = new \GuzzleHttp\Client();
      $response = $client->request('POST', env('MVAPI_URL') . 'order', $params);



      $resp = json_decode($response->getBody(),true);
      echo $response->getBody();
      print_r($resp);
      print_r($params);
		}
	}

  public function testBoonterm(Request $request){
    $data = '{"tel":"0823433522","cust_name":"May","cust_lastname":"Jii"}';
    $params = [
       'headers' => ['authorization' => $request->header('authorization')]
      
    ];
    echo json_encode($params);
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', env('MVAPI_URL') . 'order/3242342342', $params);

    $resp = json_decode($response->getBody(),true);
    echo $response->getBody();
    print_r($resp);

  }

	public function response_front2c2p(Request $request){
		$order_id = $request->input('order_id');
		$orderServ = new OrderService();
		$order = $orderServ->GetOrderByOrderNo($order_id);

		if(!is_null($order)){
			$transServ = new PaymentTransactionService();
			$transServ->SaveResponseFrontPayment($order, $request->all());
		}

		$order_id_enc = SimpleCrypt::ecode($order->id);

		if($request->input('payment_status') == '000'){
			return redirect(env('FRONTEND_PAYMENT_2C2P_SUCCESS').'?t='.$order_id_enc);
		}
		if($request->input('payment_status') == '003'){
			return redirect(env('FRONTEND_PAYMENT_2C2P_CANCEL').'?t='.$order_id_enc);
		}
		if($request->input('payment_status') == '999'){
			return redirect(env('FRONTEND_PAYMENT_2C2P_ERROR').'?t='.$order_id_enc);
		}

	}

}
