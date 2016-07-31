<?php
namespace App\Api\V1\Service;
use App\PaymentTransaction as ThisModel;

class PaymentTransactionService{
  public function __construct(){
    $this->model      =  new ThisModel;

 }

	public function SaveRequestPayment($order, $param){
		$trans = $this->model;
		$trans->order_id = $order->id;
		$trans->payment_vendor_id = $order->payment_vendor_id;
		$trans->type = config('payment.transaction_type.request');
		$trans->param = json_encode($param);

		$trans->save();
	}

	public function SaveResponseFrontPayment($order, $param){
		$trans = $this->model;
		$trans->order_id = $order->id;
		$trans->payment_vendor_id = $order->payment_vendor_id;
		$trans->type = config('payment.transaction_type.response_front');
		$trans->param = json_encode($param);

		$trans->save();
	}

	// public function SaveResponseBackPayment($order, $param){
	// 	$trans = $this->model;
	// 	$trans->order_id = $order->id;
	// 	$trans->payment_vendor_id = $order->payment_vendor_id;
	// 	$trans->type = config('payment.transaction_type.response_back');
	// 	$trans->param = json_encode($param);

	// 	$trans->save();
	// }

}

