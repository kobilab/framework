<?php

	namespace KobiLab\Framework\Manufacturing;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class ProductionOrderRotations extends Model {

		protected $table = 'production_order_rotations';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'production_order_id', 'part_id', 'status', 'operation', 'work_center_id', 'work_type_id', 'quantity', 'remainder' ];

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

		public function getWorkType()
		{
			return $this->hasOne('KobiLab\Framework\Production\WorkTypes\WorkTypes', 'id', 'work_type_id');
		}

		public function getWorkCenter()
		{
			return $this->hasOne('KobiLab\Framework\Production\WorkCenters\WorkCenters', 'id', 'work_center_id');
		}
		
	}
