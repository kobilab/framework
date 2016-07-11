<?php

	namespace KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class BomRoute extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'bom_route';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'bom_id', 'route_id', 'default' ];

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

		public function getBom()
		{
			return $this->hasOne('KobiLab\Framework\Production\Boms\Boms', 'id', 'bom_id');
		}

		public function getRoute()
		{
			return $this->hasOne('KobiLab\Framework\Production\Routes\Routes', 'id', 'route_id');
		}
	}