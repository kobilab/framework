<?php

	namespace KobiLab\Framework\Production\Routes;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class Routes extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'routes';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'route_code', 'title' ];

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

		public function getRouteDetails()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\RouteWorkType', 'route_id', 'id');
		}

		public function getDefinedBoms()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\BomRoute', 'route_id', 'id');
		}

	}
