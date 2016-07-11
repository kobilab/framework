<?php

	namespace KobiLab\Framework\Production\PartBomRouteWorkTypeWorkCenter;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class PartBom extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'part_bom';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'part_id', 'bom_id', 'default' ];

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

		public function getPart()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id', 'part_id');
		}

		public function getBom()
		{
			return $this->hasOne('KobiLab\Framework\Production\Boms\Boms', 'id', 'bom_id');
		}
	}