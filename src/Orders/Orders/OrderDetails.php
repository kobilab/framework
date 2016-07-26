<?php

	namespace KobiLab\Framework\Orders\Orders;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\CheckOrderStatus;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Müşterilerle ilgili işlemleri yürüten sınıf
	 */
	class OrderDetails extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'order_details';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'order_id', 'part_id', 'quantity', 'status', 'production_order_id' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		public function store()
		{
			return self::create($this->data);
		}

		public function updation()
		{
			CheckOrderStatus::fire($this->whichOne);
			return self::find($this->whichOne)->update($this->data);
		}

		public function getOrder()
		{
			return $this->hasOne('KobiLab\Framework\Orders\Orders\Orders', 'id', 'order_id');
		}

		public function getPart()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id' ,'part_id');
		}

		public function getProductionOrder()
		{
			return $this->hasOne('KobiLab\Framework\Manufacturing\ProductionOrders', 'id', 'production_order_id');
		}
	}