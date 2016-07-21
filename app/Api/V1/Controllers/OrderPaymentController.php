<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Api\V1\Service\OrderService;
use App\Api\V1\Service\Payment2c2pService;
use App\Api\V1\Service\PaymentTransactionService;
use App\Order;

class OrderPaymentController extends Controller
{
	public function submit(Request $request){
		$order_id = $request->input('order_id');
		$payment_vendor_id = $request->input('payment_vendor_id');
		$shipping_vendor_id = $request->input('shipping_vendor_id');

		$orderServ = new OrderService();
		$order = $orderServ->GetOrderByID($order_id);
		
		$order_price = $orderServ->CalculateOrderPrice($order);
		$shipping_price = $orderServ->GetShippingPrice($order);

		$order->order_no = $orderServ->GenerateOrderNo($order);
		$order->order_price = $order_price;
		$order->shipping_price = $shipping_price;
		$order->total_price = $order_price+$shipping_price;
		$order->invoice_no = $orderServ->GenerateInvoiceNo($order);
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
			return redirect(env('FRONTEND_PAYMENT_SUCCESS').$order->id);
		}
		if($request->input('payment_status') == '003'){
			return redirect(env('FRONTEND_PAYMENT_CANCEL').$order->id);
		}
		if($request->input('payment_status') == '999'){
			return redirect(env('FRONTEND_PAYMENT_ERROR').$order->id);
		}

	}
}