<?php

	namespace KobiLab\Framework\Manufacturing;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\PredefineProductionOrder;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Üretim emirleri ile ilgili işlemleri yapan sınıf
	 */
	class ProductionOrders extends Model
	{
		use SetData, Validation, Modification;

		protected $table = 'production_orders';

		protected $primaryKey = 'id';

		protected $fillable = [ 'production_order_code', 'part_id', 'quantity' ];

		public $timestamps = true;

		use SoftDeletes;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
		
		/**
		 * Üretim emri için gerekli olan parçaları dönderen method
		 * 
		 * @return Collection
		 */
		public function getPartsForNewProductionOrder()
		{
			return TablePartBom::where('default', 2)->groupBy('part_id')->get();
		}

		/**
		 * Üretim emrini kaydetmeye yarayan method
		 * 
		 * @return Collection
		 */
		public function store()
		{
			$id = self::create($this->data);
			
			//PredefineProductionOrder::fire($id['id']);

			return $id;
		}

		/**
		 * Üretim emrindeki gerekli olan parçaları döndermeye yarayan method
		 * 
		 * @return array
		 */
		public function showNeededParts()
		{
			$find = TableProductionOrderNeededParts::where('production_order_id', $this->whichOne)->get();

			$sonuclar = [];

			foreach ($find as $e) {
				$sonuclar[$e['upper_part_id']][] = $e;
			}

			return $sonuclar;
		}

		/**
		 * Üretim emrindeki rotasyonları dönderen method
		 * 
		 * @return array
		 */
		public function showRotations()
		{
			$rotasyonIslemler = TableProductionOrderRotations::where('production_order_id', $this->whichOne)->get();
			$rotasyonlar = [];
			foreach ($rotasyonIslemler as $e) {
				$rotasyonlar[$e['part_id']][] = $e;
			}

			return $rotasyonlar;
		}

		/**
		 * Üretim emrinin sonucu oluşacak olan parçaları dönderen method
		 * 
		 * @return array
		 */
		public function showComposedParts()
		{
			return TableProductionOrderComposedParts::where('production_order_id', $this->whichOne)->get();
		}

		/**
		 * Üretim emrini incelemek için gerekli olan itemleri dönderen method
		 * 
		 * @return array
		 */
		public function take()
		{
			$data['gerekenMalzemeler'] = $this->showNeededParts();
			$data['islemler'] = $this->showRotations();
			$data['olusacaklar'] = $this->showComposedParts();
			$data['detay'] = $this->get();

			return $data;
		}

		/**
		 * Üretim emrini silmeye yarayan method
		 * 
		 * @return boolean
		 */
		public function wipe()
		{
			return TableProductionOrders::find($this->whichOne)->delete();
		}

		public function getPart()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id', 'part_id');
		}
	}
