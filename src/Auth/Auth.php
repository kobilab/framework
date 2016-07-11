<?php

	namespace KobiLab\Framework\Auth;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Depo işlemlerini yapan sınıf
	 */
	class Auth extends Model
	{
		use SoftDeletes;

		public function isLoggedIn()
		{
			return session('loggedIn', false);
		}

	}
