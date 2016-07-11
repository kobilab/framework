<?php

	namespace KobiLab\Framework\Orders\Orders;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
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

		protected $fillable = [ 'id', 'order_id', 'part_id', 'quantity' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
	}