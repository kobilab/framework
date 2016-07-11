<?php

	namespace KobiLab\Framework\Orders\Orders;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Müşterilerle ilgili işlemleri yürüten sınıf
	 */
	class Orders extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'orders';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'order_code', 'company_id' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		public function store()
		{
			return self::create($this->data);
		}

		public function updation()
		{
			return self::find($this->whichOne)->update($this->data);
		}

		public function getCompany()
		{
			return $this->hasOne('KobiLab\Framework\Orders\Companies\Customers', 'id', 'company_id');
		}
	}