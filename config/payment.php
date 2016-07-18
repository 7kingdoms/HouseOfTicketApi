<?php

return [
	'2c2p' => [
		'version' => '6.9',
	  'merchant_id' => '764764000000014',  
	  'secret' => '1JA8CNahHfzs', 
	  'paymenturl' => 'https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment', 

	  'prefix_orderid' => 't',

		], 

		'order_status' => [
			'booking' => 'B0', 
			'payment_boonterm' => 'P0', 
			'payment_2c2p' => 'P1', 
			'payment_complete' => '01', 
			'expired' => 'OE', 
		],


	'order_expired_in' => '10 minute',
	'order_invoice_prefix' => 'INV',

	
	'transaction_type' => [
		'request' => 'request', 
		'response_front' => 'response_front', 
		'response_back' => 'response_back', 
	]


	];
	