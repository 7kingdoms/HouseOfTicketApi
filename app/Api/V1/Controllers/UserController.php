<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Api\V1\Helpers\SimpleCrypt;

use App\Order;


class UserController extends Controller
{
	public function getOrderHistoryBooking(Request $request){
		$user_id = $request->input('user_id');

		$cond_status = [config('payment.order_status.booking')];

		$date1 = date('Y-m-d H:i:s');
		$date2 = date('Y-m-d H:i:s', strtotime('-'.config('payment.order_expired_minute').' minute',strtotime($date1)));
		$orders = Order::where('user_id', '=', $user_id)
									->whereIn('status', $cond_status)
									->where('created_at', '>=', $date2)
									->get();

		$datas = [];
		foreach($orders as $order){
			$data = [
				'id' => SimpleCrypt::ecode($order->id), 
				'order_no' => $order->order_no, 
				'invoice_no' => $order->invoice_no, 
				'user_id' => $order->user_id, 
				'event_package_id' => $order->event_package_id, 
				'payment_vendor_id' => $order->payment_vendor_id, 
				'shipping_vendor_id' => $order->shipping_vendor_id, 
				'shipping_price' => $order->shipping_price, 
				'order_price' => $order->order_price, 
				'total_price' => $order->total_price, 
				'status' => $order->status, 
				'paided_at' => $order->paided_at, 
				'created_at' => date('Y-m-d H:i:s', strtotime($order->created_at)), 
				'updated_at' => date('Y-m-d H:i:s', strtotime($order->updated_at)), 
			];
			array_push($datas, $data);
		}
		return $datas;

	}

	public function getOrderPayment(Request $request){
		$user_id = $request->input('user_id');

		$cond_status = [config('payment.order_status.paided'),config('payment.order_status.payment_boonterm'),config('payment.order_status.payment_2c2p')];

		$orders = Order::where('user_id', '=', $user_id)
									->whereIn('status', $cond_status)
									->get();

		$datas = [];
		foreach($orders as $order){
			$data = [
				'id' => SimpleCrypt::ecode($order->id), 
				'order_no' => $order->order_no, 
				'invoice_no' => $order->invoice_no, 
				'user_id' => $order->user_id, 
				'event_package_id' => $order->event_package_id, 
				'payment_vendor_id' => $order->payment_vendor_id, 
				'shipping_vendor_id' => $order->shipping_vendor_id, 
				'shipping_price' => $order->shipping_price, 
				'order_price' => $order->order_price, 
				'total_price' => $order->total_price, 
				'status' => $order->status, 
				'paided_at' => $order->paided_at, 
				'created_at' => date('Y-m-d H:i:s', strtotime($order->created_at)), 
				'updated_at' => date('Y-m-d H:i:s', strtotime($order->updated_at)), 
			];
			array_push($datas, $data);
		}
		return $datas;
	}

}