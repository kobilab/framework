<?php

	namespace KobiLab\Framework\Production\WorkTypes;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Rotasyonlarla ilgili işlemleri yapan method
	 */
	class WorkTypes extends Model
	{
		use SetData, Validation, Modification;

		protected $table = 'work_types';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'title' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
		
		/**
		 * Rotasyonu kaydetmeye yarayan method
		 * 
		 * @return Collection
		 */
		public function store()
		{
			return self::create($this->data);
		}

		/**
		 * Rotasyonu güncellemeye yarayan method
		 * 
		 * @return Collection
		 */
		public function updation()
		{
			return self::find($this->whichOne)->update($this->data);
		}

		public function getRoute()
		{
			return $this->hasOne('KobiLab\Framework\Production\Routes\Routes', 'id', 'route_id');
		}

		public function getStations()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\WorkTypeWorkCenter', 'work_type_id', 'id');
		}

	}
