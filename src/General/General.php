<?php

	namespace KobiLab\Framework\General;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Bu sınıf bağlanıldığı sınıflarda kullanılacak
	 * olan temel işlemleri gerçekleştirmekte kullanılacaktır.
	 */
	class General extends Model {

		/**
		 * VERSION
		 */
		const VERSION = '1.0.0';

		use SoftDeletes;

		/**
		 * __call
		 * 
		 * @param  string $method	  Çağrılan methodun adı
		 * @param  mixed  $parameters Çağrılan method için kullanılan parametreler
		 * @return mixed
		 */
		public function __call($method, $parameters)
		{
		}

	}