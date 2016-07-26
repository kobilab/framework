<?php

	namespace KobiLab\Framework\Orders\Orders;

	use KobiLab\OrderDetails as OD;
	use KobiLab\Orders as O;

	/**
	 * Müşterilerle ilgili işlemleri yürüten sınıf
	 */
	class CheckOrderStatus
	{
		public function fire($orderDetailId)
		{
			$find = OD::find($orderDetailId);

			$situation = false;

			foreach(OD::where('order_id', $find['order_id'])->get() as $e) {
				if($e['status']==1) {
					$situation = false;
					break;
				} else {
					$situation = true;
				}
			}

			if($situation) {
				O::find($find['order_id'])->update(['status' => 2]);
			}

		}
	}