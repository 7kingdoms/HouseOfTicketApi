<?php

namespace App\Api\V1\Controllers;

use JWTAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Api\V1\Service\OrderService;
use App\Api\V1\Service\Payment2c2pService;
use App\Api\V1\Service\PaymentBoontermService;
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

		$order_price = $orderServ->CalculateOrderPrice($order);
		$shipping_price = $orderServ->GetShippingPrice($shipping_vendor_id);
		$order->order_no = $orderServ->GenerateOrderNo($order);
		$order->invoice_no = $orderServ->GenerateInvoiceNo($order);
		$order->payment_vendor_id = $payment_vendor_id;
		$order->shipping_vendor_id = $shipping_vendor_id;
		$order->order_price = $order_price;
		$order->shipping_price = $shipping_price;
		$order->total_price = $order_price+$shipping_price;
    $order->shipping_vendor_params = $request->input('shipping_vendor_params');
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
			$payBoontermServ = new PaymentBoontermService();
			$reponse = $payBoontermServ->CreatePayment($order, $request);
			$order_id_enc = SimpleCrypt::ecode($order->id);

      if($reponse['status'] == 1){
      	$expire_time = $reponse['data']['order']['expire_time'];
      	$order_code = $reponse['data']['order']['order_code'];

      	$order->boonterm_order_code = $order_code;
      	$order->boonterm_order_id = $reponse['data']['order']['order_id'];
      	$order->save();

      	$redirect_url = env('FRONTEND_PAYMENT_BOONTERM_SUCCESS').'?t='.$order_id_enc.'&code='.$order_code.'&expire='.date('Y-m-d h:i', strtotime($expire_time)).'&phone='.$order->user->phone.'&price='.$order->total_price;

      }
      else{
      	$redirect_url = env('FRONTEND_PAYMENT_BOONTERM_ERROR').'?t='.$order_id_enc;
      }
    	return redirect($redirect_url);

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
