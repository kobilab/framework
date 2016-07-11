<?php

	namespace KobiLab\Framework\Commands;

	use Illuminate\Console\Command;

	use Illuminate\Support\Facades\Schema;
	use Illuminate\Support\Facades\DB;

	/**
	 * Veritabanı kurulumunu yapan sınıfı
	 */
	class SetupDatabase extends Command
	{

    	protected $signature = 'setup';
    	
		/**
		 * Sınıfın ana methodu
		 *
		 * @return boolean Eğer yükleme başarıyla tamamlanırsa true
		 *         hata olursa false
		 */
		public function fire()
		{
			$tables = [
				'parts',
				'lots',
				'boms',
				'part_bom',
				'bom_composed_parts',
				'bom_needed_parts',
				'routes',
				'bom_route',
				'production_orders',
				'production_order_needed_parts',
				'production_order_composed_parts',
				'production_order_rotations',
				'work_types',
				'work_centers',
				'work_type_work_center', 
			];

			$work = true;

			foreach($tables as $table) {
				if(Schema::hasTable($table)) {
					$work = false;
					break;
				}
			}

			if($work) {
				Schema::create('parts', function($table){
					$table->increments('id');
					$table->string('part_code', 16);
					$table->string('title', 128);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('lots', function($table) {
					$table->increments('id');
					$table->string('lot_code', 16);
					$table->integer('part_id');
					$table->decimal('quantity', 65, 5);
					$table->timestamps();
					$table->softDeletes();
				});


				Schema::create('boms', function($table) {
					$table->increments('id');
					$table->string('bom_code', 16);
					$table->string('title', 64);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('part_bom', function($table) {
					$table->increments('id');
					$table->integer('part_id');
					$table->integer('bom_id');
					$table->tinyInteger('default')->default('1');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('bom_composed_parts', function($table) {
					$table->increments('id');
					$table->integer('bom_id');
					$table->integer('part_id');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('bom_needed_parts', function($table) {
					$table->increments('id');
					$table->integer('bom_id');
					$table->integer('part_id');
					$table->decimal('quantity', 65, 5);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('routes', function($table) {
					$table->increments('id');
					$table->string('route_code', 16);
					$table->string('title', 64);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('bom_route', function($table) {
					$table->increments('id');
					$table->integer('bom_id');
					$table->integer('route_id');
					$table->tinyInteger('default')->default('1');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('production_orders', function($table) {
					$table->increments('id');
					$table->string('production_order_code', 16);
					$table->integer('part_id');
					$table->decimal('quantity', 65, 5);
					$table->tinyInteger('status');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('production_order_needed_parts', function($table) {
					$table->increments('id');
					$table->integer('production_order_id');
					$table->integer('part_id');
					$table->decimal('quantity', 65, 5);
					$table->decimal('reserved', 65, 5);
					$table->decimal('remainder', 65, 5);
					$table->integer('upper_part_id');
					$table->decimal('coefficient', 65, 5);
					$table->tinyInteger('is_lower_part');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('production_order_composed_parts', function($table) {
					$table->increments('id');
					$table->integer('production_order_id');
					$table->integer('part_id');
					$table->decimal('quantity', 65, 5);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('production_order_rotations', function($table) {
					$table->increments('id');
					$table->integer('production_order_id');
					$table->integer('part_id');
					$table->integer('quantity');
					$table->integer('remainder');
					$table->tinyInteger('status');
					$table->integer('work_type_id');
					$table->integer('work_center_id');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('route_work_type', function($table){
					$table->increments('id');
					$table->integer('route_id');
					$table->integer('work_type_id');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('work_types', function($table){
					$table->increments('id');
					$table->string('title', 128);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('work_centers', function($table){
					$table->increments('id');
					$table->string('title', 128);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('work_type_work_center', function($table){
					$table->increments('id');
					$table->integer('work_type_id');
					$table->integer('work_center_id');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('users', function($table){
					$table->increments('id');
					$table->string('username', 32);
					$table->string('password', 64);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('orders', function($table) {
					$table->increments('id');
					$table->string('order_code', 16);
					$table->integer('company_id');
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('companies', function($table) {
					$table->increments('id');
					$table->string('company_code', 16);
					$table->string('name', 128);
					$table->timestamps();
					$table->softDeletes();
				});

				Schema::create('order_details', function($table) {
					$table->increments('id');
					$table->integer('order_id');
					$table->integer('part_id');
					$table->decimal('quantity', 65, 5);
					$table->timestamps();
					$table->softDeletes();
				});
			}

			return $work;
		}
	}
