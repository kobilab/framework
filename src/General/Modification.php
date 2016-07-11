<?php

	namespace KobiLab\Framework\General;

	use Illuminate\Support\Facades\Validator;
	
	use Exception;

	/**
	 * Düzenleme ve inceleme işlemleri için kullanılacak trait
	 */
	trait Modification {

		/**
		 * Bu fonksiyon kullanılacak olan işlemde ID değeri gerektiği belirtiyor ve gerekli kontrolleri yapıyor
		 * 
		 * @param  string $key ID değeri
		 * @return void
		 */
		public function needId($key)
		{
			$this->whichOne = $key==null ? $this->whichOne : $key;

			if($this->whichOne==null) {
				throw new Exception('ID değeri gerekli', 1);
			}
		}

		/**
		 * Yeni veri ekleme işlemini yapan method
		 * 
		 * @return boolean
		 */
		public function autoCreate()
		{
			$this->validate();

			if ($this->validation->fails()) {
				return false;
			} else {
				$this->store();
				
				return true;
			}
		}

		/**
		 * Düzenleme işlemini yapan method
		 * 
		 * @param  string $key ID değeri
		 * @return boolean
		 */
		public function autoUpdate($key=null)
		{
			$this->needId($key);

			$this->validate();

			if ($this->validation->fails()) {
				return false;
			} else {
				$this->updation();

				return true;
			}
		}

	}
