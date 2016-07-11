<?php

	namespace KobiLab\Framework\Production\Boms;

	use KobiLab\BomRoute;
	use KobiLab\Routes;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Ürün ağaçları ile ilgili işlemleri yapmaya yarayan method
	 */
	class Boms extends Model
	{
		use SetData, Validation, Modification;

		protected $table = 'boms';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'bom_code', 'title' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		/**
		 * Ürün ağacını kaydeden method
		 * 
		 * @return Collection
		 */
		public function store()
		{
			$id = self::create([
				'bom_code' 	=> $this->data['bom_code'],
				'title'		=> $this->data['title']
			]);

			if ($this->data['default_route']!='' AND $this->data['default_route']!='0') {
				BomRoute::create([
					'bom_id'	=> $id['id'],
					'route_id'	=> $this->data['default_route'],
					'default'	=> 2
				]);
			}

			return $id;
		}

		public function updation()
		{
			return self::find($this->whichOne)->update($this->data);
		}

		public function getComposedParts()
		{
			return $this->hasMany('KobiLab\Framework\Production\Boms\Details\BomComposedParts', 'bom_id', 'id');
		}

		public function getNeededParts()
		{
			return $this->hasMany('KobiLab\Framework\Production\Boms\Details\BomNeededParts', 'bom_id', 'id');
		}

		public function getConnectedParts()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\PartBom', 'bom_id', 'id');
		}

		public function getConnectedRoutes()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\BomRoute', 'bom_id', 'id');
		}

	}
