<?php

	namespace KobiLab\Framework\Orders\Companies;

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

		public function store()
		{
			return self::create($this->data);
		}

		public function updation()
		{
			return self::find($this->whichOne)->update($this->data);
		}

		public function getOrders()
		{
			return $this->hasMany('KobiLab\Framework\Orders\Orders\Orders', 'company_id', 'id');
		}
	
	}
