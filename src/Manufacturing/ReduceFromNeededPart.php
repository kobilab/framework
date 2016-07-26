<?php

	namespace KobiLab\Framework\Manufacturing;

	use KobiLab\ProductionOrderNeededParts as PONP;
	use KobiLab\ProductionOrderRotations as POR;
	use KobiLab\ProductionOrderComposedParts as POCP;
	use KobiLab\PartBom;
	use KobiLab\BomComposedParts;

	use KobiLab\GeneralProgressOfPartProduction as GPOPP;

	use Illuminate\Support\Facades\Input;

	/**
	 * Üretimde olan itemden düşme işlemi için kontrol işlemi ve işlemi gerçekleştiren method
	 */
	class ReduceFromNeededPart
	{

		/**
		 * Kontrol edilecek itemleri taşıyacak olan dizi
		 * 
		 * @var array
		 * @example [ '{$partId}:{ProductionOrderNeededPartsId}' => null ]
		 */
		var $toBeChecked = [];

		var $productionOrderId = null;

		/**
		 * Genel ilerlemeyi taşıyan değişken
		 * 
		 * @var array
		 */
		var $generalProgress = [];

		/**
		 * İşleme start veren method
		 *
		 * @todo   lot inputları 0'dan küçük olamaz 
		 *         numeric olması lazım
		 *         ayrıca istenilen değerden büyükte girilemez 
		 *         bunların kontrolünün yapılması lazım
		 * @param  integer $uretimGerekenId Üretim Gereken Id
		 * @see    Parçadan bir tane bile üretilse alt parçalar direk kaldırılıyor olabilir.
		 * @return boolean
		 */
		public function start($productionOrderId)
		{
			# Üretim gereken itemler tablosundan işlem yapacağımız satırı alıyoruz.
			$detail = PONP::find($productionOrderId);
			$this->productionOrderId = $detail['production_order_id'];

			# Genel ilerleme kayıt altına alınıyor.
			$this->generalProgress = GPOPP::fromProductionOrderId($detail['production_order_id']);

			# Bu parçayı kontrol edilecekler listesine ekliyoruz
			# Burdaki üst part id kullanıyoruz. ilk part olduğu için
			$this->toBeChecked($detail['part_id'], $detail['upper_part_id']);
			//print_r($this->generalProgress);
			//print_r($this->toBeChecked);
			
			# Girlen değerin toplam değerini bulmamız lazım
			# çünkü bu değer kat sayılarıyla çarpılarak doğruluğu kontrol edilecek
			$total = 0;
			foreach ($detail->getAvailableLots as $e) {
				# Değer 0 değilse toplama ekleniyor.
				if (Input::get($e['id'].'lot')!=0) {
					$total = $total + Input::get($e['id'].'lot');
				}
			}

			# Kontrol edilecekler işlemi tamamlandı. Şimdi
			# toBeChecked dizisini elde ettik. Bu dizinin keyi olan
			# part id değerleri sabit tutularak multipliedReducedValue yani girilen toplam değerle
			# o parçanın kat sayısı çarpılarak elde edilen hedeflenen düşme sayısını tutuyoruz.
			# available ise mevcut durumdaki kalan adeti taşıyor.
			//echo $toplam;
			foreach ($this->toBeChecked as $ikiNoktali => $value) {
				$re = "/(\d*):(\d*)/"; 
				$str = $ikiNoktali; 
				$subst = ""; 
				 
				$result = preg_match_all($re, $str, $r);
				$itemId = $r[1][0];
				$this->toBeChecked[$ikiNoktali]['multipliedReducedValue'] = (double) PONP::find($r[2][0])['remainder'];
				$this->toBeChecked[$ikiNoktali]['available'] = PONP::find($r[2][0])['coefficient']/$detail['coefficient']*$total;
			}
			//print_r($this->toBeChecked);
			# İşlemin yapılıp yapılamıyacağını belirliyoruz.
			# Öncelikli olarak işlemi yapılabilir olarak atıyoruz.
			# ve kontrollere başlıyoruz.
			//dd($this->toBeChecked);
			$resultOfJob = true;
			foreach ($this->toBeChecked as $itemId => $each) {
				# Hedeflenen kalandan büyükse işlem yapılamaz ve durdurur.
				if ($each['available']>$each['multipliedReducedValue']) {
					$resultOfJob = false;
					break;
				}
			}


			//dd($this->toBeChecked);

			# İşlem sonucumuzu biliyoruz. 
			# Eğer işlem yapılamazsa hata ekleyip false dönderiyoruz.
			# Eğer işlem yapılabilirse üretim gereken itemler tablosundan
			# itemin altparçası olsun olmasın döngü içerisine alarak tek tek düzenleme yapıyoruz
			# ayrıca stok lotlarından da düşme işlemini gerçekleştiriyoruz ve true dönderiyoruz.
			if (!$resultOfJob) {
				session()->put('asd', 'error');
			} else {
				foreach ($this->toBeChecked as $withColon => $null) {
					//dd($null);
					# Gelen veriyi parçalarına ayırıyoruz.
					# Part ıd ve gereken parça id değerini alıyoruz.
					$patternOfToBeChecked = "/(\d*):(\d*)/"; 
					$separated = preg_match_all($patternOfToBeChecked, $withColon, $r);
					$partId = $r[1][0];
					$productionNeededPartId = $r[2][0];

					# Üretim gerekenler tablosunda düşme işlemi yapılıyor.
					$control = PONP::find($productionNeededPartId);
					$n = [
						'remainder'	=> $control['remainder']-$null['available'],
						'reserved'	=> $null['available']+$control['reserved']
					];
					$control->update($n);

					# Aynı zamanda bu gereken malzeme tamamlandığı zaman alt parçaların üretimide tamamlanıyor.
					$rotationControl = POR::where('production_order_id', $detail['production_order_id'])->where('part_id', $r[1][0])->get();
					foreach ($rotationControl as $eachRotation) {
						$asddddd = $control['remainder']-$null['available'];
						
						if(($asddddd==(double) 0) or ($asddddd<0)) {
							$eachRotation->update(['status'=>2]);
						} else {
							$eachRotation->update(['status'=>1]);
						}
					}

					# Ayrıca parça düşme işlemi yapıldığı zaman işlem
					# sonucu oluşacak olan parçalardan düşme işlemi yapmamız lazım ;)
					$findBomId = PartBom::where('part_id', $r[1][0])->where('default', 2)->first();
					$bomComposedParts = BomComposedParts::where('bom_id', $findBomId['bom_id'])->get();
					foreach($bomComposedParts as $eachComposedPart) {
						$productionOrderComposedPart = ProductionOrderComposedParts::where('production_order_id', $this->productionOrderId)
																				   ->where('part_id', $eachComposedPart['part_id'])
																				   ->first();
						$values = [
							'quantity' => $productionOrderComposedPart['quantity']-$null['available']
						];
						//$productionOrderComposedPart->update($values);
					}


				}

				# Son olarak lotlardan düşme işlemi yapıyoruz.
				foreach ($detail->getAvailableLots as $e) {
					if (Input::get($e['id'].'lot')!=0) {
						$e->update(['quantity'=>$e['quantity']-Input::get($e['id'].'lot')]);
					}
				}
			}

			return $resultOfJob;
		}

		/**
		 * İşlemde kullanılacak olan temel itemleri tek bir sıraya yerleştirmeye yarayan method
		 * 
		 * @param  string $key Item Id
		 * @return void
		 */
		protected function toBeChecked($partId, $upperPartId) {
			$g = PONP::where('upper_part_id', $upperPartId)
										   ->where('part_id', $partId)
										   ->where('production_order_id', $this->productionOrderId)
										   ->first();
			# Gelen item değeri kontrol edilecekler dizisinde yoksa 
			# bu değere ait key tanımlanıyor değeri null olan
			# Sonuç olarak elde edilecek olan dizi
			# [
			# 	$itemId1 => null,
			# 	$itemId2 => null
			# ]
			# gibi
			if (!array_key_exists($partId, $this->toBeChecked)) {
				$this->toBeChecked[$partId.':'.$g['id']] = null;
			}

			# Eğer daha önce tanımlanmışsa bu item bu demektir ki
			# genel ilerlemede alt itemi de var veya olabilir. bu
			# itemi tekrardan bu fonksiyon için çağırabiliriz.
			if (array_key_exists($partId, $this->generalProgress)) {
				foreach ($this->generalProgress[$partId] as $k => $value ) {
					$this->toBeChecked($value, $partId);
				}
			}
		}
	}
