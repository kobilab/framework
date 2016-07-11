<?php

	namespace KobiLab\Framework\Manufacturing;

	class ProductionOrderParts
	{
		function getConsumableTableParts()
		{
			return TableProductionOrderNeededParts::find($productionNeededId);
		}

		function consume()
		{
			$detay = ProductionOrderNeededParts::find($productionNeededId);
			$gelenVeriler = [];
			$kurallar = [];
			$okunakli = [];

			foreach ($detay->getUygunLotlar as $each) {
				$gelenVeriler[$each['id'].'lot'] = Input::get($each['id'].'lot');
				$kurallar[$each['id'].'lot'] = 'required|numeric|between:0,'.$each['adet'];
				$okunakli[$each['id'].'lot'] = $each['lot_code'];
			}

			$validator = Validator::make($gelenVeriler, $kurallar);
			$validator->setAttributeNames($okunakli);

			$toplam = 0;
			foreach ($detay->getUygunLotlar as $e) {
				if (Input::get($e['id'].'lot')!=0) {
					$toplam = $toplam + Input::get($e['id'].'lot');
				}
			}

			if ($validator->fails()) {
				if ($validator->fails()) {
					Messages::error($validator->errors()->all());
				}
				if ($toplam>$detay['quantity']) {
					Messages::error('Girilen adet sipariş için gereken adetten fazla.');
				}

				return redirect()
						->route('consumeProductionNeededTableParts', $productionNeededId)
						->withMessages(Messages::all())
						->withInput(Input::all());
			} else {
				$sonuc = ReduceFromNeededPart::start($productionNeededId);
				if($sonuc) {
					ProductionControl::start($detay['production_order_id']);
					Messages::success('İtemler ayırt edildi.');

					return redirect()
							->route('showProductionOrder', $detay['production_order_id'])
							->withMessages(Messages::all());
				} else {
					return redirect()
							->route('consumeProductionNeededTableParts', $productionNeededId)
							->withMessages(Messages::all())
							->withInput(Input::all());
				}
			}
		}
	}
