<?php

	namespace KobiLab\Framework;

	class ServiceProvider extends \Illuminate\Support\ServiceProvider
	{

		public function register() {
			$this->registerCommands();
		}

		public function boot() {
			$this->loadConfig();
			//$this->loadHttp();
			//$this->loadView();
			if(file_exists(__DIR__.'/helpers.php'))
				require __DIR__.'/helpers.php';
		}

		function registerCommands()
		{
	        $this->app['command.zahmetsizce.setup'] = $this->app->share(
	            function ($app) {
	                return new Commands\SetupDatabase();
	            }
	        );

	        $this->app['command.zahmetsizce.demo'] = $this->app->share(
	            function ($app) {
	                return new Commands\CreateDemo();
	            }
	        );

	        $this->commands([
	        	'command.zahmetsizce.setup',
	        	'command.zahmetsizce.demo'
	        ]);
		}


		public function loadConfig()
		{
			$this->mergeConfigFrom(__DIR__.'/../config/tableRules.php', 'zahmetsizce.tableRules');
			$this->mergeConfigFrom(__DIR__.'/../config/tableAttributeNames.php', 'zahmetsizce.tableAttributeNames');
			$this->mergeConfigFrom(__DIR__.'/../config/general.php', 'zahmetsizce.general');
		}

		public function loadHttp()
		{
			$this->app->router->group(['namespace' => 'Zahmetsizce\Controllers', 'middleware' => 'web'], function() {
				require __DIR__.'/../config/routes.php';
			});
		}

		public function loadView()
		{
		\Blade::setRawTags('{{', '}}');
		\Blade::setContentTags('{{', '}}');
		\Blade::setEscapedContentTags('{{{', '}}}');

		\Blade::extend(function($value)
		{
			return preg_replace('/(?<!\w)(\s*)@set\s*\(\s*\${0,1}[\'\"\s]*(.*?)[\'\"\s]*,\s*([\W\w^]*?)\)\s*$/m', 
<<<'EOT'
$1<?php \$$2 = $3; $__data['$2'] = $3; ?>
EOT
, $value);
		});
			if(file_exists(__DIR__.'/Themes/'.config('zahmetsizce.general.theme').'/config.php')) {
				$compose  = require __DIR__.'/Themes/'.config('zahmetsizce.general.theme').'/config.php';
				view()->composers($compose);
			}
			
			$this->loadViewsFrom(__DIR__.'/Themes/'.config('zahmetsizce.general.theme').'/resources', 'zahmetsizce');

			if(file_exists(__DIR__.'/Themes/'.config('zahmetsizce.general.theme').'/helpers.php'))
				require __DIR__.'/Themes/'.config('zahmetsizce.general.theme').'/helpers.php';


		}

	}