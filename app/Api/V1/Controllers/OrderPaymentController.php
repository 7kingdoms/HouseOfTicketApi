<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Api\V1\Service\OrderService;
use App\Api\V1\Service\Payment2c2pService;
use App\Order;

class OrderPaymentController extends Controller
{
	public function submit(Request $request){
		$order_id = $request->input('order_id');
		$payment_vendor_id = $request->input('payment_vendor_id');
		$shipping_vendor_id = $request->input('shipping_vendor_id');

		$orderServ = new OrderService();
		$order = $orderServ->GetOrderByID($order_id);

		if($orderServ->IsExpired($order))
		{
			$order = $orderServ->SetStatusExpired($order);

			return redirect(env('FRONTEND_URL').'payment_expired');
		}

		$order = $orderServ->SetStatusWaiting($order);


		$total_price = $orderServ->CalculateOrderPrice($order);

		$order->price = $total_price;
		$order->invoice_no = $orderServ->GenerateInvoiceNo($order);
		$order->save();

		if($order->payment_vendor_id == 1){

			
			$pay2c2pServ = new Payment2c2pService();
			$pay2c2pServ->CreatePayment($order);
		}



	}
}