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

			return redirect(env('FRONTEND_PAYMENT_EXPIRED'));
		}

		if($order->payment_vendor_id == 2){

			$pay2c2pServ = new Payment2c2pService();
			$order = $pay2c2pServ->CreatePayment($order);
		}
		else{
			$client = new \GuzzleHttp\Client();
      $response = $client->request('POST', env('MVAPI_URL') . 'order',[
         'headers' => ['authorization' => $request->header('authorization')]
        ,'body' => '{
              "data": {
                  "tel": "0823433522",
                  "cust_name": "May",
                  "cust_lastname": "Jii",
                  "price": 100,
                  "ref_id": "a000001",
                  "valid_day": 0,
                  "valid_hour": 1
              }
          }'
      ]);
      $resp = json_decode($response->getBody(),true);			

      // print_r($resp);
		}
	}

	public function response_front2c2p(Request $request){
		$order_id = $request->input('order_id');
		$orderServ = new OrderService();
		$order = $orderServ->GetOrderByOrderNo($order_id);

		if(!is_null($order)){
			$transServ = new PaymentTransactionService();
			$transServ->SaveResponseFrontPayment($order, $request->all());
		}

		if($request->input('payment_status') == '000'){
			return redirect(env('FRONTEND_PAYMENT_SUCCESS'));
		}
		if($request->input('payment_status') == '003'){
			return redirect(env('FRONTEND_PAYMENT_CANCEL'));
		}
		if($request->input('payment_status') == '999'){
			return redirect(env('FRONTEND_PAYMENT_ERROR'));
		}

	}

}
