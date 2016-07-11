<?php

	namespace KobiLab\Framework\Manufacturing;

	use KobiLab\ProductionOrders as PO;
	use KobiLab\ProductionOrderRotations as POR;
	use KobiLab\BomNeededParts;
	use KobiLab\ProductionOrderNeededParts as PONP;
	use KobiLab\PartBom;
	use KobiLab\BomComposedParts;
	use KobiLab\ProductionOrderComposedParts as POCP;
	use KobiLab\BomRoute;
	use KobiLab\RouteDetails;
	use KobiLab\RouteWorkType;

	/**
	 * Üretilmek istenilen item için gerekli olan işlemleri yapan sınıf
	 */
	class PredefineProductionOrder
	{

		/**
		 * Üretim Emir Id
		 * 
		 * @var null|integer
		 */
		var $productionOrderId = null;

		/**
		 * Üretim emri satırı
		 * 
		 * @var array
		 */
		var $productionOrderDetail = [];

		/**
		 * Üretim emri için gerekli işlemleri başlatan method
		 *
		 * @todo   Güncel bom yapısı 1 a itemi için 5 tane b itemi lazım
		 *         gibi ilerliyor. Ama bu tarz gelişmiş yazılımlarda 
		 *         1 a itemi için değil. 0.5 a itemi için 2.5 b itemi lazım
		 *         şeklinde ilerliyebiliyor. Burdaki kast ettiğim durum "1"
		 *         nihai ürünün oluşma sayısı ve buna bağlı katsayılar
		 * @todo   Üretim sonucu oluşan itemlerde sadece ana item sayısı kadar oluşabiliyor.
		 *         Bu kısım içinde kat sayı mantığı eklenebilir.
		 * @param  integer $id Üretim Emir Id
		 * @return void
		 */
		public function fire($productionOrderId)
		{

			# Üretim emri ile ilgili satırı alıyoruz. ve sınıf içine aktarıyoruz.
			$detail = PO::find($productionOrderId);
			$this->productionOrderId = $productionOrderId;
			$this->productionOrderDetail = $detail;

			# Üretim için gerekli itemler için ilk olarak üretilmesi istenilen itemin
			# kendisini ekliyoruz.
			$veriler = [
				'production_order_id'	=> $this->productionOrderId,
				'part_id'				=> $detail['part_id'],
				'quantity'				=> $detail['quantity'],
				'reserved'				=> 0,
				'remainder'				=> $detail['quantity'],
				'upper_part_id'			=> 0, # Ana item olduğu için 0
				'coefficient'			=> 1, # İlk item olduğu için katsayısı 0
				'is_lower_part'			=> 1  # Alt item mi? Hayır
			];
			PONP::create($veriler);

			# Parçaya tanımlı bom'u buluyoruz.
			# Bu kısma TableBomsuz parça gelemiyor. seçilen itemler ona göre listeleniyor.
			$bomOfPart = PartBom::where('part_id', $detail['part_id'])
								->where('default', 2)
								->first();

			# Üretim emrinde kullanılmak istenilen bom gereken itemlerin detaylarını alıyoruz
			# Ve tek tek inceliyoruz. Eğer gerekli olan itemlere tanımlı bir bom varsa onu da 
			# o bom ile birlikte üretime alıyoruz.
			$TableBomNeededTableParts = BomNeededParts::where('bom_id', $bomOfPart['bom_id'])->get();

			foreach ($TableBomNeededTableParts as $neededPart) {
				$veriler = [
					'production_order_id'	=> $this->productionOrderId,
					'part_id'				=> $neededPart['part_id'], # İtemin kendisi
					'quantity'				=> $detail['quantity']*$neededPart['quantity'], # İstenilen ana item sayısı ile bu üründen 1 ürün için kaç tane lazımsa onla çarpımı
					'reserved'				=> 0,
					'remainder'				=> $detail['quantity']*$neededPart['quantity'], # İstenilen ana item sayısı ile bu üründen 1 ürün için kaç tane lazımsa onla çarpımı
					'upper_part_id'			=> $detail['part_id'], # Ana item
					'coefficient'			=> $neededPart['quantity'], # 1 ürün için kaç quantity gerekli, ana itemden geldiği için çarpım yok
					'is_lower_part'			=> 1 # Ana itemden geldikleri için alt item değiller (ön tanımlı)
				];

				# Bu iteme bağlı bom var mı?
				$check = PartBom::where('part_id', $neededPart['part_id'])
								->where('default', 2)
								->first();
				if ($check !== null) {
					# Eğer bom varsa bu demektir ki bu item alt item
					$veriler['is_lower_part'] = 2;
					$isLowerPart = true;
				} else {
					$isLowerPart = false;
				}

				# Kontroller sonrası ekleniyor
				PONP::create($veriler);

				# Eğer alt itemse tekrar inceleme işlemine alınıyor.
				if ($isLowerPart) {
					$this->checkAgain($neededPart['part_id'], $neededPart['quantity']);
				}
			}

			# Boma tanımlı rotasyon var mı varsa ekle
			# @see Burdaki döngüde eğer değer yoksa null dönüyor 
			# ve biz bu değeri döngüye alıyoruz. null kontrolü yapılırsa
			# hızda iyileştirme sağlanabilir mi?
			$routeOfBom = BomRoute::where('bom_id', $bomOfPart['bom_id'])
								  ->where('default', 2)
								  ->first();

			# Üretim emri için tanımlanan ana itemin rota detaylarını veritabanına ekliyoruz.
			$TableRouteDetails = RouteWorkType::where('route_id', $routeOfBom['route_id'])->get();
			foreach ($TableRouteDetails as $routeDetail) {
				$veriler = [
					'production_order_id'	=> $this->productionOrderId,
					'part_id'				=> $detail['part_id'], # Ana item
					'remainder'				=> $detail['quantity'],
					'quantity'				=> $detail['quantity'],
					'status'				=> 1, # durumu daha bitmedi
					'work_type_id'			=> $routeDetail['work_type_id'], # İşlem
					'work_center_id'		=> 0
				];

				# Ekleme
				POR::create($veriler);
			}

			# Üretim emri sonucu nihai itemle birlikte birden fazla item
			# oluşabiliyor. Bunlarında sorgulanıp eklenmesi gerekliyor.
			$TableBomComposedTableParts = BomComposedParts::where('bom_id', $bomOfPart['bom_id'])->get();

			foreach ($TableBomComposedTableParts as $composedPart) {
				$verilrr = [
					'production_order_id'	=> $this->productionOrderId,
					'part_id'				=> $composedPart['part_id'],
					'quantity'				=> $detail['quantity'] # İtem sadece nihai item kadar oluşabiliyor.
				];

				# Ekleme
				POCP::create($verilrr);
			}

		}

		/**
		 * Alt itemleri tekrardan inceleme yarayan method
		 * @param  integer 	$itemId 	İncelenecek Item Id
		 * @param  decimal	$katSayi	Kullanılanacak kat sayı
		 * @return void
		 */
		protected function checkAgain($partId, $coefficient=1)
		{
			# İtem için eklenmiş olan bomu alıyoruz.
			$bomOfPart = PartBom::where('part_id', $partId)
							 ->where('default', 2)
							 ->first();

			/**
			 * Bu kısımda tekrar item eklenmiyor.
			 * Çünkü geldiği fonksiyonda bu item
			 * eklenmiş olacaktır.
			 */

			# Bom detayları
			$bomDetails = BomNeededParts::where('bom_id', $bomOfPart['bom_id'])->get();

			# Detaylar tektek inceleniyor
			foreach ($bomDetails as $bomDetail) {
				$veriler = [
					'production_order_id'	=> $this->productionOrderId,
					'part_id'				=> $bomDetail['part_id'],
					'quantity'				=> $this->productionOrderDetail['quantity']*$bomDetail['quantity']*$coefficient,
					'reserved'				=> 0,
					'remainder'				=> $this->productionOrderDetail['quantity']*$bomDetail['quantity']*$coefficient,
					'upper_part_id'			=> $bomOfPart['part_id'],
					'coefficient'			=> $bomDetail['quantity']*$coefficient,
					'is_lower_part'			=> 1
				];

				# Alt itemi varsa veriler düzenleniyor ve ekleniyor.
				$check = PartBom::where('part_id', $bomDetail['part_id'])->first();
				if ($check !== null) {
					$veriler['is_lower_part'] = 2;
					$isLowerPart = true;
				} else {
					$bomDetail = false;
				}

				PONP::create($veriler);

				# Alt itemi varsa onuda incelemeye alıyoruz.
				if ($bomDetail) {
					$this->checkAgain($bomDetail['part_id'], $bomDetail['quantity']*$coefficient);
				}
			}

			# Üretim emri sonucu nihai itemle birlikte birden fazla item
			# oluşabiliyor. Bunlarında sorgulanıp eklenmesi gerekliyor.
			$TableBomComposedTableParts = BomComposedParts::where('bom_id', $bomOfPart['bom_id'])->get();

			# @see burdaki bomdetay['quatity'] hata olabilir.
			foreach ($TableBomComposedTableParts as $composedPart) {
				$verilrr = [
					'production_order_id'	=> $this->productionOrderId,
					'part_id'				=> $composedPart['part_id'],
					'quantity'				=> $this->productionOrderDetail['quantity']*$coefficient # İtem sadece nihai item kadar oluşabiliyor.
				];

				# Ekleme
				POCP::create($verilrr);
			}

			# Bom'a tanımlı rotasyonu varsa onu kontrol edip ekliyoruz.
			$bomOfPart = BomRoute::where('bom_id', $bomOfPart['bom_id'])->where('default', 2)->first();
			$TableRouteDetails = RouteWorkType::where('route_id', $bomOfPart['route_id'])->get();
			foreach ($TableRouteDetails as $routeDetail) {
				$veriler = [
					'production_order_id'	=> $this->productionOrderId,
					'part_id'				=> $partId, # Ana item
					'status'				=> 1, # durumu daha bitmedi
					'remainder' 			=> $this->productionOrderDetail['quantity']*$coefficient,
					'quantity'				=> $this->productionOrderDetail['quantity']*$coefficient,
					'work_type_id'			=> $routeDetail['work_type_id'], # İşlem
					'work_center_id'		=> 0
				];

				# Ekleme
				POR::create($veriler);
			}
		}
	}
