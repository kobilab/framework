<?php

	namespace KobiLab\Framework\Manufacturing;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class ProductionOrderComposedParts extends Model {

		protected $table = 'production_order_composed_parts';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'production_order_id', 'part_id', 'quantity' ];

		public $timestamps = true;

		use SoftDeletes;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		public function getPart()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id', 'part_id');
		}

		public function getProductionOrder()
		{
			return $this->hasOne('KobiLab\Framework\Manufacturing\ProductionOrders', 'id', 'production_order_id');
		}
	}
