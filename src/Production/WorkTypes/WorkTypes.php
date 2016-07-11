<?php

	namespace KobiLab\Framework\Production\WorkTypes;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class WorkTypes extends Model
	{
		use SetData, Validation, Modification;

		protected $table = 'work_types';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'title' ];

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

		public function getStations()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\WorkTypeWorkCenter', 'work_type_id', 'id');
		}

	}
