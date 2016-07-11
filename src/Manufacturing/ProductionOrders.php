<?php

	namespace KobiLab\Framework\Manufacturing;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\PredefineProductionOrder;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class ProductionOrders extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'production_orders';

		protected $primaryKey = 'id';

		protected $fillable = [ 'production_order_code', 'part_id', 'quantity' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
		
		public function store()
		{
			$id = self::create($this->data);
			
			//PredefineProductionOrder::fire($id['id']);

			return $id;
		}

		public function showNeededParts()
		{
			$find = TableProductionOrderNeededParts::where('production_order_id', $this->whichOne)->get();

			$sonuclar = [];

			foreach ($find as $e) {
				$sonuclar[$e['upper_part_id']][] = $e;
			}

			return $sonuclar;
		}

		public function showRotations()
		{
			$rotasyonIslemler = TableProductionOrderRotations::where('production_order_id', $this->whichOne)->get();
			$rotasyonlar = [];
			foreach ($rotasyonIslemler as $e) {
				$rotasyonlar[$e['part_id']][] = $e;
			}

			return $rotasyonlar;
		}

		public function getPart()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id', 'part_id');
		}
	}
