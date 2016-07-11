<?php

	namespace KobiLab\Framework\General;

	use Illuminate\Support\Facades\Input;

	/**
	 * $this->data değikeniyle ilgilenen sınıf
	 */
	trait SetData {

		/**
		 * Sınıf içi dönecek veriler
		 * 
		 * @var null
		 */
		var $data = null;

		/**
		 * Sınıf içi dönecek ID değeri
		 * 
		 * @var null
		 */
		var $whichOne = null;

		/**
		 * Verilen değeri this->data ya eşitleyen method
		 * 
		 * @param array $data
		 */
		public function setData($data)
		{
			$this->data = $data;

			return $this;
		}

		/**
		 * this->data değerine post veya get ile gelen değeri alan method
		 * @param string $localKey this->data için kullanılacak key
		 * @param string $inputKey gelen verinin anahtarı eğer boşsa localKey kullanılır
		 * @param string $default  gelen verideğeri boş ise tanımlanacak değer
		 */
		public function setFromInput($localKey, $inputKey=null, $default = null)
		{
			$this->data[$localKey] = Input::get($inputKey==null ? $localKey : $inputKey, $default);

			return $this;
		}

		/**
		 * this->data değerinin tamamını gelen verilere eşitleyen method
		 */
		public function setFromAllInput()
		{
			$this->data = Input::all();

			return $this;
		}

		/**
		 * this->whichOne değerini tanımlayan method
		 * @param string $data Kullanılacak anahtar değeri
		 */
		public function setId($data)
		{
			$this->whichOne = $data;

			return $this;
		}

	}
