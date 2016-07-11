<?php

	namespace KobiLab\Framework\Manufacturing;

	/**
	 * Üretim emrindeki operasyonlarla ilgili işlemleri yapan sınıf
	 */
	class ProductionOrderOperation extends General
	{
		use SetData;
		/**
		 * Gerekli olan işlemi bitirmeye yarayan method
		 * 
		 * @return true
		 */
		public function workFinish($key=null)
		{
			$this->needId($key);

			$detay = TableProductionOrderRotations::find($this->whichOne);

			$veriler = [
				'status' => 2
			];

			$detay->update($veriler);
			ProductionControl::start($detay['production_order_id']);

			return true;
		}
	}
