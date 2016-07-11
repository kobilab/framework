<?php

	namespace KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter;

	use KobiLab\Framework\General\General;

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

		function getRoutesForDefiningBom()
		{
			return TableRoutes::all();
		}

		function routeDefine($bomId)
		{
			$veriler = [
				'bom_id'	=> $bomId,
				'route_id'	=> Input::get('routeId')
			];

			$kurallar = [
				'route_id'	=> 'required'
			];

			$okunakli = [
				'route_id'	=> 'Rota'
			];

			$validator = Validator::make($veriler, $kurallar);
			$validator->setAttributeNames($okunakli);

			if ($validator->fails()) {
				return false;
			} else {
				TableBomRoute::firstOrCreate($veriler);

				return true;
			}
		}

		function getPartsForDefiningBom()
		{
			return TableParts::all();
		}

		function partDefine()
		{
			$veriler = [
				'bom_id'	=> $bomId,
				'part_id'	=> Input::get('partId')
			];

			$kurallar = [
				'part_id'	=> 'required'
			];

			$okunakli = [
				'part_id'	=> 'İtem'
			];

			$validator = Validator::make($veriler, $kurallar);
			$validator->setAttributeNames($okunakli);

			if ($validator->fails()) {
				return false;
			} else {
				TablePartBom::firstOrCreate($veriler);

				return true;
			}
		}

		function removeItem()
		{
			TablePartBom::find($TablePartBomId)->delete();

			return true;
		}

		function removeRoute()
		{
			TableBomRoute::find($TableBomRouteId)->delete();
		}

		function defaultRoute()
		{
			$makeFree = [
				'default' => 1
			];

			TableBomRoute::where('bom_id', $bomId)->update($makeFree);

			$makeDependent = [
				'default' => 2
			];

			TableBomRoute::where('bom_id', $bomId)->where('route_id', $routeId)->update($makeDependent);

			return true;
		}


		public function define()
		{
			$veriler = [
				'route_id'	=> $routeId,
				'bom_id'	=> Input::get('bomId')
			];

			$kurallar = [
				'bom_id'	=> 'required'
			];

			$okunakli = [
				'bom_id'	=> 'BOM'
			];

			$validator = Validator::make($veriler, $kurallar);
			$validator->setAttributeNames($okunakli);

			if ($validator->fails()) {
				return false;
			} else {
				TableBomRoute::firstOrCreate($veriler);

				return true;
			}
		}

		public function remove()
		{
			TableBomRoute::find($TableBomRouteId)->delete();

			return true;
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