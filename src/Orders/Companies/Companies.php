<?php

	namespace KobiLab\Framework\Orders\Companies;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Müşterilerle ilgili işlemleri yürüten sınıf
	 */
	class Companies extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'companies';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'company_code', 'name' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
	
	}
