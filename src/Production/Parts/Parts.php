<?php

	namespace KobiLab\Framework\Production\Parts;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\Validation;
	use KobiLab\Framework\General\SetData;

	use KobiLab\Boms;
	use KobiLab\PartBom;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Parçalarla ilgili işlemleri yapan sınıf
	 */
	class Parts extends Model
	{

		use Modification, Validation, SetData, SoftDeletes;

		protected $table = 'parts';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'part_code', 'title' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		public function store()
		{
			$id = parent::create($this->data);

			if ($this->data['default_bom']!='' AND $this->data['default_bom']!='0') {
				PartBom::create([
					'part_id'	=> $id['id'],
					'bom_id'	=> $this->data['default_bom'],
					'default'	=> 2
				]);
			}

			return true;
		}

		public function updation()
		{
			return parent::find($this->whichOne)->update($this->data);
		}

		public function getBoms()
		{
			return $this->hasMany('KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter\PartBom', 'part_id', 'id');
		}

		public function getTotal()
		{
			return $this->hasOne('KobiLab\Framework\Inventory\Lots', 'part_id', 'id')
						->selectRaw('SUM(`lots`.`quantity`) as sumOf');
		}

	}
