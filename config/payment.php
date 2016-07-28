<?php

return [
	'2c2p' => [
		'version' => env('2C2P_VERSION', '6.9'),
	  'merchant_id' => env('2C2P_MERCHANTID', '764764000000014'),  
	  'secret' => env('2C2P_SECRET', '1JA8CNahHfzs'), 
	  'paymenturl' => env('2C2P_PAYMENTURL', 'https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment'), 
	], 

	'boonterm' => [
		'api_url' => env('MVAPI_URL', 'http://dev.houseofticket.com:8080/'), 
		'valid_day' => env('BOONTERM_API_VALID_DAY', 0), 
		'valid_hour' => env('BOONTERM_API_VALID_HOUR', 3), 
	], 


	'order_status' => [
		'booking' => 'B0', 
		'payment_boonterm' => 'P0', 
		'payment_2c2p' => 'P1', 
		'paided' => '01', 
		'canceled' => 'C1', 
		'expired' => 'OE', 
		'payment_expired' => 'PE', 
	],


	// 'order_expired_in' => '10 minute',
	'order_expired_minute' => '10',
	'order_invoice_prefix' => 'INV',

	
	'transaction_type' => [
		'request' => 'request', 
		'response_front' => 'response_front', 
		'response_back' => 'response_back', 
	]


	];
	