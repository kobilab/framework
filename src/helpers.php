<?php
	
	if(!function_exists('redirectTo')) {
		/**
		 * Direk olarak adrese yönlendirmeye yarayan fonksiyon
		 * 
		 * @param  string $route  Kayıtlı olan yönlendirme adı
		 * @param  string|array $detail Yönlendirme detayları
		 * @return redirect
		 */
		function redirectTo($route, $detail = null) {
			return redirect()->route($route, $detail);
		}
	}

	if(!function_exists('numberFormat')) {
		/**
		 * Ondalıklı sayılarda ondalık kısmını değiştirmeye yarayan method
		 * 
		 * @param  string $number Gelecek sayı
		 * @return string         Ondalıklı kısmı güncellenmiş sayı
		 */
		function numberFormat($number) {
			return number_format($number, config('zahmetsizce.general.decimal'), '.', ',');
		}
	}
