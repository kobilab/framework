<?php

	namespace KobiLab\Framework\Production\Parts;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\Validation;
	use KobiLab\Framework\General\SetData;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Parçalarla ilgili işlemleri yapan sınıf
	 */
	class Units extends Model
	{

		use Modification, Validation, SetData, SoftDeletes;

		protected $table = 'units';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'title', 'short_form' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		public function store()
		{
			parent::create($this->data);

			return true;
		}

		public function updation()
		{
			return parent::find($this->whichOne)->update($this->data);
		}

	}
