<?php

	namespace KobiLab\Framework\Manufacturing;

	use KobiLab\GeneralProgressOfPartProduction as GPOPP;
	use KobiLab\BomComposedParts;
	use KobiLab\PartBom;
	use KobiLab\ProductionOrderNeededParts as PONP;
	use KobiLab\ProductionOrderComposedParts as POCP;
	use KobiLab\ProductionOrderRotations as POR;
	use KobiLab\ProductionOrders as PO;
	use KobiLab\Lots;

	use Illuminate\Support\Facades\Input;

	/**
	 * Üretimde olan itemden düşme işlemi için kontrol işlemi ve işlemi gerçekleştiren method
	 */
	class ProductionControl
	{

		/**
		 * Kontrol edilecek itemleri taşıyacak olan dizi
		 * 
		 * @var array
		 */
		var $toBeChecked = [];

		/**
		 * Genel ilerlemeyi taşıyan değişken
		 * 
		 * @var array
		 */
		var $generalProgress = [];

		var $resultOfRotations = [];
		var $resultOfNeededParts = [];

		var $total = [];

		var $productionOrderId;

		/**
		 * İşleme start veren method
		 *
		 * @todo   lot inputları 0'dan küçük olamaz 
		 *         numeric olması lazım
		 *         ayrıca istenilen değerden büyükte girilemez 
		 *         bunların kontrolünün yapılması lazım
		 * @param  integer $uretimGerekenId Üretim Gereken Id
		 * @return boolean
		 */
		public function fire($productionOrderId)
		{
			$this->productionOrderId = $productionOrderId;
			# Genel ilerleme kayıt altına alınıyor.
			$this->generalProgress = GPOPP::fromProductionOrderId($productionOrderId);

			foreach ($this->generalProgress as $mainPartId => $subParts) {
				if (!array_key_exists($mainPartId, $this->total)) {
					$this->total[$mainPartId] = [];
				}
				foreach ($subParts as $subPartId) {
					if (!in_array($subPartId, $this->total[$mainPartId])) {
						$this->total[$mainPartId][] = $subPartId;
						if (array_key_exists($subPartId, $this->generalProgress)) {
							$this->workAgain($mainPartId, $subPartId);
						}
					}
				}
			}

			$this->work();
		}


		public function workAgain($mainPartId, $subPartId) {
			foreach ($this->generalProgress[$subPartId] as $subId) {
				if (!array_key_exists($subPartId, $this->total)) {
					$this->total[$subPartId] = [];
				}
				if (array_key_exists($subId, $this->generalProgress)) {
					$this->workAgain($subPartId, $subId);
				}
			}
		}

		public function work() {
			$results = [];
			foreach ($this->total as $anaItemId => $altItemler) {
				$results[$anaItemId] = true;
				foreach ($altItemler as $key => $altItemId) {
					if (PONP::where('production_order_id', $this->productionOrderId)->where('part_id', $altItemId)->where('remainder', 0)->first()!==null) {
						$results[$anaItemId] = true;
					} else {
						$results[$anaItemId] = false;
						break;
					}
				}
			}

			$this->resultOfNeededTableParts = $results;
			$this->rotasyonCheck();
		}

		public function rotasyonCheck() {
			$results = [];
			foreach ($this->total as $anaItemId => $altItemler) {
				$results[$anaItemId] = true;
				foreach (POR::where('production_order_id', $this->productionOrderId)->where('part_id', $anaItemId)->get() as $e) {
					if ($e['status']==2) {
						$results[$anaItemId] = true;
					} else {
						$results[$anaItemId] = false;
						break;
					}
				}
			}

			$this->resultOfRotations = $results;
			$this->degerlendir();
		}

		public function degerlendir() {
			//print_r($this->resultOfNeededTableParts);
			//print_r($this->resultOfRotations);
			
			//dd($this->total);
			foreach ($this->total as $anaItemId => $altItemler) {
				if (($this->resultOfRotations[$anaItemId] == true) and ($this->resultOfNeededTableParts[$anaItemId] == true)) {
					$e = PONP::where('production_order_id', $this->productionOrderId)->where('part_id', $anaItemId)->first();
					$v = [
						'remainder'	=> 0,
						'reserved'	=> $e['quantity']
					];

					$e->update($v);

					# Eğer ana ürünse ve tamamsa
					# o ürünü ve üretim sonucu oluşacakları
					# stok lotlarına ekliyoruz.
					if ($e['upper_part_id']==0) {
						$stogaEkle = [
							'lot_code'	=> 'L-U-'.Lots::all()->count().'-'.str_random(8),
							'part_id'	=> $anaItemId,
							'quantity'	=> $e['quantity']
						];

						Lots::create($stogaEkle);


						PO::where('id', $this->productionOrderId)->update(['status'=>2]);
					}


					$findBomId = PartBom::where('part_id', $anaItemId)->where('default', 2)->first();
					$bomComposedParts = BomComposedParts::where('bom_id', $findBomId['bom_id'])->get();
					foreach($bomComposedParts as $eachComposedPart) {
						$v = [
							'lot_code'	=> 'L-U-'.Lots::all()->count().'-'.str_random(8),
							'part_id'	=> $eachComposedPart['part_id'],
							'quantity'	=> $e['quantity']
						];

						Lots::create($v);
					}


				}
			}
		}
	}
