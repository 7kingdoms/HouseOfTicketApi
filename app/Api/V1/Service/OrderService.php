<?php
namespace App\Api\V1\Service;

use App\Order as ThisModel;
use App\OrderSeat;
use App\OrderEventAdditional;
use App\ShippingVendor;

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
			$status = config('payment.order_status.expired');
				$order->status = $status;
				$order->save();

				OrderSeat::where('order_id', '=', $order->id)->update(['status' => config('payment.order_status.expired')]);
				return $order;
		}

		public function SetStatusPayment2c2p($order){
			$status = config('payment.order_status.payment_2c2p');
				$order->status = $status;
				$order->save();

				OrderSeat::where('order_id', '=', $order->id)->update(['status' => $status]);
				return $order;

		}

		public function SetStatusPaided($order){
			$status = config('payment.order_status.paided');
			if(is_null($order->paided_at)){
				$order->paided_at = date('Y-m-d H:i:s');
			}
			$order->status = $status;
			$order->save();

			OrderSeat::where('order_id', '=', $order->id)->update(['status' => $status]);
				
			return $order;

		}


		public function GetOrderByID($order_id){
			return $this->model->with([ 'user', 'seats', 'seats.zone', 'shipping_vendor'])->where('id', '=', $order_id)->first();
		}

		public function GetOrderByOrderNo($order_no){
			return $this->model->where('order_no', '=', $order_no)->first();
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

		public function GetShippingPrice($shipping_vendor_id){
			$shipping_vendor = ShippingVendor::find($shipping_vendor_id);
			if(is_null($shipping_vendor))
			{
				return 0;
			}
			return $shipping_vendor->price;
		}

		public function CalculateOrderPrice($order){
			$total = 0;
			$order_seats = $order->seats;
			foreach($order_seats as $order_seat){
				$c_seat_price = 0;
				$zone_price = $order_seat->zone->price;

				$c_seat_price+= $zone_price;

				// $addition_price = $this->GetAdditionalPrice($order_seat);
				// $c_seat_price+= $addition_price;

				$total+= $c_seat_price;
			}


			return $total;
		}

		public function GenerateInvoiceNo($order){

			return env('ORDER_NO_PREFIX', '').config('payment.order_invoice_prefix').date('ymd').substr('000000000000'.$order->id, -12);
		}

		public function GenerateOrderNo($order){

			return env('ORDER_NO_PREFIX', '').date('ymd').substr('000000000000'.$order->id, -12);
		}

	}

?>