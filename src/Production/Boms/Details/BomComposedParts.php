<?php

	namespace KobiLab\Framework\Production\Boms\Details;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;


	/**
	 * Ürün ağacı oluşan parçalarla ilgilenen sınıf
	 */
	class BomComposedParts extends Model
	{
		use Modification, SetData, Validation, SoftDeletes;

		protected $table = 'bom_composed_parts';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'bom_id', 'part_id' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		/**
		 * Ürün ağacına oluşan malzeme ekleme işlemini yapan method
		 * 
		 * @return boolean
		 */
		public function creation()
		{
			/*$veriler = [
				'bom_id'	=> $bomId,
				'part_id'	=> Input::get('partId')
			];*/

			$this->rules = [
				'part_id'	=> 'required'
			];

			$this->attributeNames = [
				'part_id'	=> 'Parça'
			];

			$this->validate();

			if ($this->validation->fails()) {
				return false;
			} else {
				self::create($this->data);

				return true;
			}
		}

		public function getItem()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id', 'part_id');
		}
	}
