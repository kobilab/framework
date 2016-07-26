<?php

	namespace KobiLab\Framework\General;

	use Illuminate\Support\Facades\Validator;

	/**
	 * Doğrulama işlemlerini yapan trait
	 */
	trait Validation {

		/**
		 * Kullanılacak olan kurallar
		 * 
		 * @var array
		 */
		var $rules = [];

		/**
		 * Kullanılacak olan kuralların isimleri
		 * 
		 * @var array
		 */
		var $attributeNames = [];

		/**
		 * Doğrulama işleminin verilerini taşıyan değişken
		 * 
		 * @var null
		 */
		var $validation = null;

		/**
		 * Doğrulama işlemini yapan method
		 * 
		 * @return this
		 */
		public function validate()
		{
			$validator = Validator::make($this->data, $this->rules);
			$validator->setAttributeNames($this->attributeNames);

			$this->validation = $validator;

			return $this;
		}

		/**
		 * Tanımlı tabloyu kullanarak kuralları belirlemeye yarayan method
		 * 
		 * @param  string $table Hangi tablo ayarları
		 * @return this
		 */
		public function setRulesForTable($table)
		{
			$newRules = [];

			foreach(config('zahmetsizce.tableRules.'.$table) as $fieldName => $rules) {
				if($this->whichOne==null) {
					$localId = 'NULL';
				} else {
					$localId = $this->whichOne;
				}
				$newRules[$fieldName] = str_replace(':id', $localId, $rules);
			}

			$this->attributeNames = config('zahmetsizce.tableAttributeNames.'.$table);
			$this->rules = $newRules;

			return $this;
		}

		/**
		 * Doğrulama için attribute isimlerini belirleyen method
		 * 
		 * @param  array $data
		 * @return this
		 */
		public function setAttributeNames($data)
		{
			$this->attributeNames = $data;

			return $this;
		}

		/**
		 * Doğrulama için gerekli olan kuralları belirleyen method
		 * 
		 * @param array $data Kurallar
		 */
		public function setRules($data)
		{
			$this->rules = $data;

			return $this;
		}

		/**
		 * Kontrol sırasında oluşan hataları dönderen method
		 * 
		 * @return Collection
		 */
		public function getErrors()
		{
			return $this->validation->errors()->all();
		}

		public function getOld()
		{
			return $this->data;
		}

	}