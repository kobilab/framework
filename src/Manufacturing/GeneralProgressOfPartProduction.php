<?php

	namespace KobiLab\Framework\Manufacturing;

	use KobiLab\ProductionOrderNeededParts as PONP;

	/**
	 * Üretimin genel ilerlemesini çıkaran sınıf
	 */
	class GeneralProgressOfPartProduction
	{
		/**
		 * Bu işlemi üretim emir değerinden tetikleyen method
		 * 
		 * @param  integer $emirId Üretim emir Id
		 * @return array
		 */
		public function fromProductionOrderId($emirId)
		{
			return $this->work($emirId);
		}

		/**
		 * Bu işlemi üretim için gerekli olan parça değerinden tetikleyen method
		 * 
		 * @param  integer $uretimGerekenId Üretim gereken parça ID
		 * @return array
		 */
		public function fromProductionOrderNeededId($uretimGerekenId)
		{
			return $this->work(PONP::find($uretimGerekenId)['emir_id']);
		}

		/**
		 * Genel ilerlemeyi derleyip dönderen method
		 * 
		 * @param  integer $emirId Üretim Emir ID
		 * @return array
		 */
		public function work($emirId)
		{
			$genelIlerleme = [];
			# Bu döngüde ikili olarak üretimi kaydediyoruz.
			# Örneğin
			# 
			#	Array
			#	(
			#	[1] => Array
			#			(
			#				[0] => 2
			#				[1] => 3
			#			)
			#		[3] => Array
			#			(
			#				[0] => 4
			#				[1] => 5
			#		)
			#	)
			# 
			# Bu dizideki gibi ana ürün 1
			# 1, 2 ve 3 ten oluşuyor
			# 2 de 4 ve 5 ten oluşuyor
			foreach (PONP::where('production_order_id', $emirId)->get() as $e) {
				$detaylar[$e['part_id']] = $e;
				if ($e['upper_part_id']==0) {
					$genelIlerleme[$e['part_id']] = [];
				} else {
					$genelIlerleme[$e['upper_part_id']][] = $e['part_id'];
				}
			}

			return $genelIlerleme;
		}
	}