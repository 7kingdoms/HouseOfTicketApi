<?php
namespace App\Api\V1\Service;

use App\Api\V1\Service\PaymentTransactionService;

class Payment2c2pService{

	 public function __construct(){
	  	$this->c2p_version = config('payment.2c2p.version');
	  	$this->c2p_merchant_id = config('payment.2c2p.merchant_id');
	  	$this->c2p_secret = config('payment.2c2p.secret');
	  	$this->c2p_paymenturl = config('payment.2c2p.paymenturl');

   }

		public function CreatePayment($order){
			$orderServ = new OrderService();
      $order_id = $order->order_no;
      $invoice_no = $order->invoice_no;
      // echo $invoice_no;exit;
      // echo $invoice_no;exit;

      // $currency = 'payment_desc';
      // $amount = $order->total_price;
      $amount = $this->GenerateAmountStr($order->total_price);
      // echo $amount;exit;
      $customer_email = $order->user->email;

      $strSignatureString = $this->c2p_version.$this->c2p_merchant_id.$order_id.$invoice_no.$amount.$customer_email;
      $HashValue = hash_hmac('sha1', $strSignatureString, $this->c2p_secret, false);

      $data = [
      	'version' => $this->c2p_version, 
      	'merchant_id' => $this->c2p_merchant_id, 
         'order_id' => $order_id, 
      	'invoice_no' => $invoice_no, 
      	'amount' => $amount, 
      	'customer_email' => $customer_email, 
      	'hash_value' => $HashValue, 
      ];

    $transServ = new PaymentTransactionService();
    $transServ->SaveRequestPayment($order, $data);
    
    $order = $orderServ->SetStatusPayment2c2p($order);

		echo "<form action='".$this->c2p_paymenturl."' method='POST' name='authForm'>";
    echo "<input type='hidden' id='version' name='version' value='" .$data['version']. "'/>"; 
    echo "<input type='hidden' id='merchant_id' name='merchant_id' value='" .$data['merchant_id']. "'/>"; 
    echo "<input type='hidden' id='order_id' name='order_id' value='" .$data['order_id']. "' />    ";                     
    echo "<input type='hidden' id='invoice_no' name='invoice_no' value='" .$data['invoice_no']. "' />    ";                     
    echo "<input type='hidden' id='amount' name='amount' value='" .$data['amount']. "'/>"; 
    echo "<input type='hidden' id='customer_email' name='customer_email' value='" .$data['customer_email']. "'/>"; 
    echo "<input type='hidden' id='hash_value' name='hash_value' value='" .$data['hash_value']. "'/>";
    echo "</form>";

    echo "<script language='JavaScript'>";
    echo "document.authForm.submit();";     //submit form to 2c2p Redirect 
   	echo "</script>";


    // return $order;
	}

	private function GenerateAmountStr($amount){
		return substr('000000000000'.str_replace('.', '', number_format((float)$amount, 2, '.', '')), -12);;
	}

}