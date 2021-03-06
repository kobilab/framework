<?php

	namespace KobiLab\Framework\Inventory;

	use KobiLab\Framework\General\General;

	use KobiLab\Framework\General\Modification;
	use KobiLab\Framework\General\SetData;
	use KobiLab\Framework\General\Validation;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Depo işlemlerini yapan sınıf
	 */
	class Lots extends Model
	{
		use SetData, Validation, Modification, SoftDeletes;

		protected $table = 'lots';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'lot_code', 'part_id', 'quantity' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

		/**
		 * Lotu kaydeden method
		 * 
		 * @return Collection
		 */
		public function store()
		{
			return self::create($this->data);
		}

		/**
		 * Depoyu dönderen method
		 * 
		 * @return Collection
		 */
		public function inventoryData()
		{
			return self::selectRaw('*, sum(`quantity`) as sumOf')
							->groupBy('part_id')
							->get();
		}

		/**
		 * Parçaya ait lotları dönderen method
		 * 
		 * @param  string $partId Parça ID değeri
		 * @return Collection
		 */
		public function lotsOfPart($partId)
		{
			return self::where('part_id', $partId)->where('quantity', '!=', 0)->get();
		}

		public function getPart()
		{
			return $this->hasOne('KobiLab\Framework\Production\Parts\Parts', 'id', 'part_id');
		}
	}
