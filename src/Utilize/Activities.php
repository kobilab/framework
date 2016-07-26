<?php

	namespace KobiLab\Framework\Utilize;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\Validation;
	use KobiLab\Framework\General\SetData;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Parçalarla ilgili işlemleri yapan sınıf
	 */
	class Activities extends Model
	{

		use Modification, Validation, SetData, SoftDeletes;

		protected $table = 'activities';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'where', 'which_id', 'desc' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		public function store()
		{
			parent::create($this->data);

			return true;
		}

	}
