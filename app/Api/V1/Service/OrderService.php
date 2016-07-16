<?php
namespace App\Api\V1\Service;

use App\Order as ThisModel;
use App\OrderEventAdditional;

	class OrderService{
    public function __construct(){
      $this->model      =  new ThisModel;

   }

		public function IsExpired($order){
			if($order->status == 'OE'){
				return true;
			}
			$expired = date('Y-m-d H:i:s', strtotime('+'.config('payment.order_expired_in'), strtotime($order->created_at)));
			if($expired < date('Y-m-d H:i:s')){

				return true;
			}
			return false;
		}

		public function SetStatusExpired($order){
				$order->status = 'OE';
				$order->save();

				return $order;
		}
		public function SetStatusWaiting($order){
				$order->status = 'W';
				$order->save();

				return $order;
		}

		public function GetOrderByID($order_id){
			return $this->model->with([ 'user', 'seats', 'seats.zone', 'shipping_vendor'])->where('id', '=', $order_id)->first();
		}

		public function GetAdditionalPrice($order_seat){
			// $addit = OrderEventAdditional::where('order_id', '=', $order_seat->order_id)
																// ->where('event_seat_id', '=', $order_seat->event_seat_id)->first();
			$addit = OrderEventAdditional::with('additional')->where('order_id', '=', $order_seat->order_id)
																->where('event_seat_id', '=', $order_seat->event_seat_id)->first();
																// print_r($addit);
																// echo $order_seat->event_seat_id;exit;
			if(is_null($addit))
			{
				return 0;
			}				
			if(is_null($addit->additional))	{
				return 0;
			}

			return $addit->additional->price;										
		}

		public function GetShippingPrice($order){
			return $order->shipping_vendor->price;
		}

		public function CalculateOrderPrice($order){
			$total = 0;
			$order_seats = $order->seats;
			foreach($order_seats as $order_seat){
				$c_seat_price = 0;
				$zone_price = $order_seat->zone->price;

				$c_seat_price+= $zone_price;

				$addition_price = $this->GetAdditionalPrice($order_seat);
				$c_seat_price+= $addition_price;

				$total+= $c_seat_price;
			}

			$shipping_fee = $this->GetShippingPrice($order);
			$total += $shipping_fee;

			return $total;
		}

		public function GenerateOrderNo($order){
			if(env('APP_ENV') != 'local'){
				return $order->id;
			}

			return config('payment.2c2p.prefix_orderid').date('mds').'_'.$order->id;
		}

	}

?>