<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Order;

class Payment2c2bController extends Controller
{
	protected $merchant_id;
	protected $secret;
	protected $paymenturl;
	protected $prefix_orderid;
	protected $version;

  public function __construct(){
  	$this->merchant_id = config('2c2p.merchant_id');
  	$this->secret = config('2c2p.secret');
  	$this->paymenturl = config('2c2p.paymenturl');
  	$this->prefix_orderid = config('2c2p.prefix_orderid');
  	$this->version = config('2c2p.version');

  }

	public function submit(Request $request){

		$version = $this->version;
		$merchant_id = $this->merchant_id;
		$secret_key = $this->secret;

		$order = new Order();
		$order->total_price = $request->input('total_price');
		$order->save();


		$order_id = $order->id;

      $payment_description = 'payment_desc';
      $invoice_no = $this->prefix_orderid.$order_id;
      // $currency = 'payment_desc';
      // $amount = $order->total_price;
      $amount = $this->generateAmountStr($order->total_price);
      // echo $amount;exit;
      $customer_email = 'mayjii555@gmail.com';

      $strSignatureString = $version.$merchant_id.$payment_description.$order_id.$invoice_no.$amount.$customer_email;
      $HashValue = hash_hmac('sha1', $strSignatureString, $secret_key, false);

		echo "<form action='https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment' method='POST' name='authForm'>";
   echo "<input type='hidden' id='version' name='version' value='" .$version. "'/>"; 
   echo "<input type='hidden' id='merchant_id' name='merchant_id' value='" .$merchant_id. "'/>"; 
   echo "<input type='hidden' id='payment_description' name='payment_description' value='" .$payment_description. "' /> "; 
   echo "<input type='hidden' id='order_id' name='order_id' value='" .$order_id. "' />    ";                     
   echo "<input type='hidden' id='invoice_no' name='invoice_no' value='" .$invoice_no. "' />"; 
   echo "<input type='hidden' id='amount' name='amount' value='" .$amount. "'/>"; 
   echo "<input type='hidden' id='customer_email' name='customer_email' value='" .$customer_email. "'/>"; 
   echo "<input type='hidden' id='hash_value' name='hash_value' value='" .$HashValue. "'/>";
   echo "</form>";

   echo "<script language='JavaScript'>";
   echo "document.authForm.submit();";     //submit form to 2c2p Redirect 
	echo "</script>";

		return config('2c2p.merchant_id');
		// return $order_id;
	}

	private function generateAmountStr($amount){
		return substr('000000000000'.str_replace('.', '', number_format((float)$amount, 2, '.', '')), -12);;
	}

	public function success(){
		return 'thank you for payment';
	}

	public function callback(){
    if(env('APP_ENV') == 'local'){
      $response = $_POST;
      $path = public_path().'/temp/2c2bcallback.txt';

      $file = fopen($path,"a");
      fwrite($file,json_encode($response));
      fwrite($file, "\n");
      fwrite($file, '----------------'."\n\n");
      fclose($file);

    }
	}
}