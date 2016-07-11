<?php

	namespace KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Rotasyonlarla ilgili işlemleri yapan method
	 */
	class RouteWorkType extends Model
	{
		use SetData, Validation, Modification;

		protected $table = 'route_work_type';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'route_id', 'work_type_id' ];

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

		public function getRoute()
		{
			return $this->hasOne('KobiLab\Framework\Production\Routes\Routes', 'id', 'route_id');
		}

		public function getWorkType()
		{
			return $this->hasOne('KobiLab\Framework\Production\WorkTypes\WorkTypes', 'id' ,'work_type_id');
		}

	}
