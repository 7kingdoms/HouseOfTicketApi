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
		$trans->type = config('payment.transaction_request_type');
		$trans->param = json_encode($param);

		$trans->save();
	}

}

