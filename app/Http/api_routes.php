<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

	$api->group(['middleware' => 'cors'],function($api){

		$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');

		$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');

		$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
		$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');

		$api->post('auth/check','App\Api\V1\Controllers\AuthController@check');

		//$api->post('me','App\Api\V1\Controllers\AuthController@me');

		$api->post('user/checkemail','App\Api\V1\Controllers\AuthController@checkemail');
		$api->post('user/check_id_card','App\Api\V1\Controllers\AuthController@check_id_card');
		$api->post('user/forgot','App\Api\V1\Controllers\AuthController@recovery');

		$api->group(['middleware' => 'api.auth'],function($api){
			$api->post('me','App\Api\V1\Controllers\AuthController@me');
			$api->post('user/update','App\Api\V1\Controllers\AuthController@update');

			$api->post('zone-seat/{id}' ,'App\Api\V1\Controllers\EventController@seatByZone');
			$api->post('event/booking/packageZone' ,'App\Api\V1\Controllers\EventController@eventPackageBook');
			$api->post('event/booking/packageSeat' ,'App\Api\V1\Controllers\EventController@eventPackageSeatBook');

			$api->post('order/saveState' ,'App\Api\V1\Controllers\OrderPaymentController@saveState');
			$api->post('event/zone/{id}' ,'App\Api\V1\Controllers\EventController@zoneByPlace');


		});

		$api->get('sendmail','App\Api\V1\Controllers\AuthController@sendmail');


	});

	$api->get('location/province' ,'App\Api\V1\Controllers\LocationController@province');
	$api->get('location/amphur' ,'App\Api\V1\Controllers\LocationController@amphur');
	$api->get('location/district' ,'App\Api\V1\Controllers\LocationController@district');
	$api->get('location/zipcode' ,'App\Api\V1\Controllers\LocationController@zipcode');

	$api->get('event/hotprovince' ,'App\Api\V1\Controllers\EventController@hotProvince');




	$api->post('ebiz/callback' ,'App\Api\V1\Controllers\VendorPaymentController@ebizCallback');
	$api->get('ebiz/view_callback' ,'App\Api\V1\Controllers\VendorPaymentController@ebizViewCallback');


	// $api->post('payment2c2b/submit' ,'App\Api\V1\Controllers\Payment2c2bController@submit');
	// $api->post('payment2c2b/success' ,'App\Api\V1\Controllers\Payment2c2bController@success');
	// $api->post('payment2c2b/callback' ,'App\Api\V1\Controllers\Payment2c2bController@callback');


// 	$app->group(['prefix' => 'payment', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
// 	$app->get('payment2c2p/{order_id}', [
// 	    'as' => 'payment.payment2c2p', 'uses' => 'PaymentController@payment2c2p'
// 	]);
// });


		$api->post('order/test' ,'App\Api\V1\Controllers\OrderPaymentController@testBoonterm');
		$api->post('order/payment' ,'App\Api\V1\Controllers\OrderPaymentController@submit');
		$api->post('order/payment/response_front2c2p' ,'App\Api\V1\Controllers\OrderPaymentController@response_front2c2p');

		$api->post('user/order_history/booking' ,'App\Api\V1\Controllers\UserController@getOrderHistoryBooking');
		$api->post('user/order_history/payment' ,'App\Api\V1\Controllers\UserController@getOrderPayment');

		$api->post('testBt' ,'App\Api\V1\Controllers\EventController@callbackBoonterm');

	//
	// // example of protected route
	// $api->get('protected', ['middleware' => ['api.auth'], function () {
	// 	return \App\User::all();
  //   }]);
	//
	// // example of free route
	 //$api->get('free', function() {
	//  	return \App\User::all();
	//  });

});
