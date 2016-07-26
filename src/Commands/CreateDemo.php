<?php

	namespace KobiLab\Framework\Commands;

	use Illuminate\Console\Command;

	use KobiLab\PredefineProductionOrder;
	use KobiLab\BomComposedParts;
	use KobiLab\BomNeededParts;
	use KobiLab\BomRoute;
	use KobiLab\Boms;
	use KobiLab\Users;
	use KobiLab\Lots;
	use KobiLab\WorkCenters;
	use KobiLab\WorkTypes;
	use KobiLab\WorkTypeWorkCenter;
	use KobiLab\RouteWorkType;
	use KobiLab\PartBom;
	use KobiLab\Parts;
	use KobiLab\Routes;
	use KobiLab\ProductionOrders;
	use KobiLab\ProductionOrderNeededParts;
	use KobiLab\ProductionOrderRotations;
	use KobiLab\Units;
	use KobiLab\ProductionOrderComposedParts;
	use KobiLab\Orders;
	use KobiLab\OrderDetails;
	use KobiLab\Companies;

	/**
	 * Demo veritabanı oluşturmaya yarayan komut
	 */
	class CreateDemo extends Command
	{
		/**
		 * Komutun adı
		 * @var string
		 */
    	protected $signature = 'zahmetsizce:demo';

    	var $emirSayisi = 50;

    	var $parts = [];

    	var $musteriler = [];

    	var $siparisler = [];

    	var $kac = 0;

    	var $boms = [];

    	var $workTypes = [];

    	var $routes = [];

    	var $lotSayisi = 1000;

    	var $adetler = [ 1,2,3,4,5,10,20,25,30,50,100,250,500,600,1000,15,35,40,45,75,85,80,300,350 ];


    	public function createCompanies()
    	{
			$faker = \Faker\Factory::create();
			$n = [];
    		for($i=0;$i<50;$i++) {
    			$rand = rand(1, 99999999);
    			$a = [
    				'id' => $rand,
    				'company_code' => 'C-'.$rand,
    				'name' => $faker->name
    			];

    			Companies::create($a);
				$this->kac++;
				$this->info($this->kac);

    			$n[$rand] = $a;
    		}

    		$this->musteriler = $n;
    	}

    	public function createUnits()
    	{
    		$as = [
    			[
    				'title' => 'Santimetre',
    				'short_form' => 'cm'
    			],
    			[
    				'title' => 'Metre',
    				'short_form' => 'm'
    			],
    			[
    				'title' => 'Kilogram',
    				'short_form' => 'kg'
    			],
    			[
    				'title' => 'Gram',
    				'short_form' => 'g'
    			],
    			[
    				'title' => 'Takım',
    				'short_form' => 'tk'
    			],
    			[
    				'title' => 'Adet',
    				'short_form' => 'ad'
    			]
    		];

    		foreach($as as $e) {
    			Units::create($e);
				$this->kac++;
				$this->info($this->kac);
    		}
    	}

    	public function createOrders()
    	{
    		$n = [];
    		for($i = 0; $i<100; $i++) {
    			$rand = rand(1, 99999999);
    			$a = [
    				'id' => $rand,
    				'order_code' => 'O-'.$rand,
    				'company_id' => array_column($this->musteriler, 'id')[rand(0, count($this->musteriler)-1)]
    			];

    			Orders::create($a);
				$this->kac++;
				$this->info($this->kac);

    			$n[$rand] = $a;
    		}

    		$this->siparisler = $n;
    	}

    	public function createOrderDetails()
    	{
    		$n = [];
    		for($i=0;$i<250;$i++) {
    			$rand = rand(1, 99999999);
    			$a = [
    				'id' => $rand,
    				'order_id' => array_column($this->siparisler, 'id')[rand(0, count($this->siparisler)-1)],
    				'part_id' => array_column($this->parts, 'id')[rand(0, count($this->parts)-1)],
    				'quantity' => $this->adetler[rand(0, count($this->adetler)-1)],
    				'status' => 1
    			];

    			OrderDetails::create($a);
				$this->kac++;
				$this->info($this->kac);
    		}
    	}

    	/**
    	 * Parçaları oluşturmaya yarayan method
    	 * 
    	 * @return void
    	 */
    	public function createParts()
    	{
    		$globalParts = [];
			$parts = [
				'A' => [],
				'B' => [],
				'C' => [],
				'D' => [],
				'E' => [],
				'F' => [],
				'G' => [],
				'H' => [],
				'J' => [],
				'K' => [],
				'L' => [],
				'M' => [],
				'N' => [],
				'O' => [],
				'P' => [],
				'R' => [],
				'S' => [],
				'T' => [],
				'V' => [],
				'Y' => [],
				'Z' => [],
				'X' => []
			];

			foreach ($parts as $key => $value) {
				$rand = rand(1, 99999999);
				$valuee = [
					'id'		=> $rand,
					'part_code'	=> 'P-' . $rand,
					'title'		=> 'Parça ' . $key,
					'unit_id' => 6
				];
				$parts[$key] = $valuee;
			}

			foreach($parts as $key => $value) {
				$ee = Parts::create($value);
				$this->kac++;
				$this->info($this->kac);
				$globalParts[$key] = $ee;
			}

			$this->parts = $globalParts;
    	}

    	/**
    	 * Ürün ağaçlarını oluştmaya yarayan method
    	 * 
    	 * @return void
    	 */
    	public function createBoms()
    	{
    		$globalBoms = [];
			# Ürün Ağacı aşağıdaki gibi olacaktır.
			# A1 => B2, C3
			# B1 => D2, E4, F4
			# C1 => G1, F3
			# G1 => H2, J3, K5
			# K1 => L1, M3
			# D1 => N3, O4
			# E1 => P1, R3

			$bomlar = [
				'A' => [],
				'B' => [],
				'C' => [],
				'G' => [],
				'K' => [],
				'D' => [],
				'E' => []
			];

			foreach ($bomlar as $key => $value) {
				$rand = rand(1, 99999999);
				$valuee = [
					'id'		=> $rand,
					'bom_code'	=> 'B-'.$rand,
					'title'		=> $key . ' için Ürün Ağacı'
				];
				$bomlar[$key] = $valuee;
			}

			foreach($bomlar as $key => $value) {
				$ee = Boms::create($value);
				$this->kac++;
				$this->info($this->kac);
				$globalBoms[$key] = $ee;
			}

			$this->boms = $globalBoms;
    	}

    	/**
    	 * Ürün ağacı için gerekli olan parçaları ekleyen method
    	 * 
    	 * @return void 
    	 */
    	public function createBomNeededParts()
    	{
			# Ürün Ağacı aşağıdaki gibi olacaktır.
			# A1 => B2, C3
			# B1 => D2, E4, F4
			# C1 => G1, F3
			# G1 => H2, J3, K5
			# K1 => L1, M3
			# D1 => N3, O4
			# E1 => P1, R3
			$BomNeededParts = [
				[
					'bom'	=> 'A',
					'item'	=> 'B',
					'adet'	=> 2
				],
				[
					'bom'	=> 'A',
					'item'	=> 'C',
					'adet'	=> 3
				],
				[
					'bom'	=> 'B',
					'item'	=> 'D',
					'adet'	=> 2
				],
				[
					'bom'	=> 'B',
					'item'	=> 'E',
					'adet'	=> 4
				],
				[
					'bom'	=> 'B',
					'item'	=> 'F',
					'adet'	=> 4
				],
				[
					'bom'	=> 'C',
					'item'	=> 'G',
					'adet'	=> 1
				],
				[
					'bom'	=> 'C',
					'item'	=> 'F',
					'adet'	=> 3
				],
				[
					'bom'	=> 'G',
					'item'	=> 'H',
					'adet'	=> 2
				],
				[
					'bom'	=> 'G',
					'item'	=> 'J',
					'adet'	=> 3
				],
				[
					'bom'	=> 'G',
					'item'	=> 'K',
					'adet'	=> 5
				],
				[
					'bom'	=> 'K',
					'item'	=> 'L',
					'adet'	=> 1
				],
				[
					'bom'	=> 'K',
					'item'	=> 'M',
					'adet'	=> 3
				],
				[
					'bom'	=> 'D',
					'item'	=> 'N',
					'adet'	=> 3
				],
				[
					'bom'	=> 'D',
					'item'	=> 'O',
					'adet'	=> 4
				],
				[
					'bom'	=> 'E',
					'item'	=> 'P',
					'adet'	=> 1
				],
				[
					'bom'	=> 'E',
					'item'	=> 'R',
					'adet'	=> 3
				],
			];

			$eklenecekBomNeededParts = [];
			foreach ($BomNeededParts as $e) {
				$rand = rand(1, 99999999);
				$eklenecekBomNeededParts[] = [
					'id' => $rand,
					'bom_id' => $this->boms[$e['bom']]['id'],
					'part_id' => $this->parts[$e['item']]['id'],
					'quantity' => $e['adet']
				];
			}

			foreach ($eklenecekBomNeededParts as $e) {
				BomNeededParts::create($e);
				$this->kac++;
				$this->info($this->kac);
			}
    	}

    	/**
    	 * Ürün ağacı için gerekli parçaları ekleyen method
    	 * 
    	 * @return void
    	 */
    	public function createBomComposedParts()
    	{
			# Üretim sonucu oluşanlar
			# A => S, T
			# C => V, Y, Z
			# E => X
			$bomSonucuOlusacaklar = [
				[
					'bom' 	=> 'A',
					'item'	=> 'S'
				],
				[
					'bom' 	=> 'A',
					'item'	=> 'T'
				],
				[
					'bom' 	=> 'C',
					'item'	=> 'V'
				],
				[
					'bom' 	=> 'C',
					'item'	=> 'Y'
				],
				[
					'bom' 	=> 'C',
					'item'	=> 'Z'
				],
				[
					'bom' 	=> 'E',
					'item'	=> 'X'
				]
			];

			$eklenecekBomSonucuOlusanlar = [];
			foreach ($bomSonucuOlusacaklar as $e) {
				$rand = rand(1, 99999999);
				$eklenecekBomSonucuOlusanlar[] = [
					'id' => $rand,
					'bom_id' => $this->boms[$e['bom']]['id'],
					'part_id' => $this->parts[$e['item']]['id']
				];
			}

			foreach ($eklenecekBomSonucuOlusanlar as $e){
				BomComposedParts::create($e);
				$this->kac++;
				$this->info($this->kac);
			}
    	}

    	/**
    	 * Lot girişini yapan method
    	 * 
    	 * @return void
    	 */
    	public function createLots()
    	{
    		$lotlar = [];
			for ($i=0;$i<$this->lotSayisi;$i++) {
				$rand = rand(1, 99999999);
				$veriler = [
					'id' => $rand,
					'lot_code' => 'L-'.$rand,
					'part_id' => array_column($this->parts, 'id')[rand(0, count($this->parts)-1)],
					'quantity' => $this->adetler[rand(0, count($this->adetler)-1)]
				];
				$lotlar[] = $veriler;
				Lots::create($veriler);
				$this->kac++;
				$this->info($this->kac);
			}
    	}

    	public function truncateAll()
    	{
			BomComposedParts::truncate();
			BomNeededParts::truncate();
			BomRoute::truncate();
			Boms::truncate();
			Lots::truncate();
			WorkCenters::truncate();
			WorkTypes::truncate();
			WorkTypeWorkCenter::truncate();
			RouteWorkType::truncate();
			PartBom::truncate();
			Parts::truncate();
			Routes::truncate();
			ProductionOrders::truncate();
			ProductionOrderNeededParts::truncate();
			ProductionOrderRotations::truncate();
			ProductionOrderComposedParts::truncate();
    	}

    	public function createRoutes()
    	{
    		$bos = [];
			# A1 => B2, C3
			# B1 => D2, E4, F4
			# C1 => G1, F3
			# G1 => H2, J3, K5
			# K1 => L1, M3
			# D1 => N3, O4
			# E1 => P1, R3
			$Routes = [
				'A' => [],
				'B' => [],
				'C' => [],
				'G' => [],
				'D' => [],
				'E' => []
			];

			foreach ($Routes as $k => $e) {
				$rand = rand(1, 99999999);
				$ee = [
					'id' => $rand,
					'route_code' => 'R-'.$rand,
					'title' => $k. ' için Rotasyon'
				];
				$Routes[$k] = $ee;
				$bos[$k] = $ee;
			}

			foreach($Routes as $k => $e) {
				Routes::create($e);
				$this->kac++;
				$this->info($this->kac);
			}

			$this->routes = $bos;
    	}

    	public function createBomRoute()
    	{
			foreach($this->routes as $k => $e) {
				$vv = [
					'id' => rand(1,99999999),
					'bom_id' => $this->boms[$k]['id'],
					'route_id' => $this->routes[$k]['id'],
					'default' => 2
				];
				BomRoute::create($vv);
				$this->kac++;
				$this->info($this->kac);
			}
    	}

    	public function createPartBom()
    	{
			foreach ($this->boms as $k => $e) {
				$vvv = [
					'id' => rand(1, 99999999),
					'part_id' => $this->parts[$k]['id'],
					'bom_id' => $e['id'],
					'default' => 2
				];
				PartBom::create($vvv);
				$this->kac++;
				$this->info($this->kac);
			}
    	}
		/**
		 * Komutta çalışan method
		 *
		 * @return mixed
		 */
		public function fire()
		{
			$start = microtime(true);

			$this->truncateAll();

			$this->createUnits();
			$this->createParts();
			$this->createCompanies();
			$this->createOrders();
			$this->createOrderDetails();
			$this->createBoms();
			$this->createBomNeededParts();
			$this->createBomComposedParts();
			$this->createLots();
			$this->createRoutes();
			$this->createBomRoute();
			$this->createPartBom();
			$this->createWorkTypes();
			$this->createWorkCenters();
			$this->createProduction();

			$firstUser = [
				'username' => 'nuhorun',
				'password' => '123123'
			];

			Users::create($firstUser);

			$end = microtime(true);
			$farki = $end-$start;
			$dakika = floor($farki/60);
			if ($farki<60) $saniye = $farki; else $saniye = $farki%60;

			$this->info($dakika . ' dakika,'. (integer) $saniye . ' saniye');
		}

		public function createWorkCenters()
		{
			$ss = [
				'Operasyon 1' => ['İstasyon 1', 'İstasyon 2'],
				'Operasyon 3' => ['İstasyon 3'],
				'Operasyon 8' => ['İstasyon 4', 'İstasyon 5'],
				'Operasyon 11' => ['İstasyon 6', 'İstasyon 7', 'İstasyon 8'],
				'Operasyon 14' => ['İstasyon 9', 'İstasyon 10']
			];

			foreach($ss as $op => $st) {
				foreach($st as $title) {
					$rand = rand(1, 99999999);
					$vv = [
						'id' => $rand,
						'title' => $title
					];

					$ii = WorkCenters::create($vv);
					$this->kac++;
					$this->info($this->kac);

					$as = [
						'work_type_id' => $this->workTypes[$op]['id'],
						'work_center_id' => $ii['id']
					];

					WorkTypeWorkCenter::create($as);
					$this->kac++;
					$this->info($this->kac);
				}
			}
		}

		public function createWorkTypes()
		{
			$types = [
				'A' => [
					'Operasyon 1', 'Operasyon 2', 'Operasyon 3'
				],
				'B' => [
					'Operasyon 4'
				],
				'C' => [
					'Operasyon 5', 'Operasyon 6'
				],
				'G' => [
					'Operasyon 7', 'Operasyon 8', 'Operasyon 9', 'Operasyon 10'
				],
				'D' => [
					'Operasyon 11', 'Operasyon 12'
				],
				'E' => [
					'Operasyon 13', 'Operasyon 14'
				]
			];

			foreach( $types as $part => $operation) {
				foreach($operation as $op) {
					$rand = rand(1, 99999999);
					$vv = [
						'id' => $rand,
						'title' => $op
					];

					$dd = WorkTypes::create($vv);
					$this->kac++;
					$this->info($this->kac);

					$this->workTypes[$op] = $dd;

					$ss = [
						'route_id' => $this->routes[$part]['id'],
						'work_type_id' => $dd['id']
					];

					$kk = RouteWorkType::create($ss);
					$this->kac++;
					$this->info($this->kac);
				}
			}
		}

		public function createProduction()
		{
			$emirler = [];
			for ($i=0;$i<$this->emirSayisi;$i++) {
				$rand = rand(1, 99999999);
				$okunakli = [
					'id' => $rand,
					'production_order_code'	=> 'E-'.$rand,
					'quantity'		=> $this->adetler[rand(0, count($this->adetler)-1)],
					'part_id' => $this->parts[array_rand($this->boms, 1)]['id'],
					'status' => 0
				];
				$emirler[] = $okunakli;
				ProductionOrders::create($okunakli);
				$this->kac++;
				$this->info($this->kac);
			}

			foreach(ProductionOrders::all() as $emem) {
				PredefineProductionOrder::fire($emem['id']);
			}
		}
	}
