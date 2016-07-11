<?php

	namespace KobiLab\Framework\Auth;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Depo işlemlerini yapan sınıf
	 */
	class Users extends Model
	{
		use SoftDeletes;

		protected $table = 'users';

		protected $primaryKey = 'id';

		protected $fillable = [ 'id', 'username', 'password' ];

		public $timestamps = true;

		protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

	}
