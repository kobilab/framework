<?php

	namespace KobiLab\Framework\Production\Routes;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Rotasyonlarla ilgili işlemleri yapan method
	 */
	class Routes extends Model
	{
		use SetData, Validation, Modification;

		protected $table = 'routes';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'route_code', 'title' ];

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

		public function getRotaDetaylari()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\RouteWorkType', 'route_id', 'id');
		}

		public function getTanimliBomlar()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\BomRoute', 'route_id', 'id');
		}

	}
