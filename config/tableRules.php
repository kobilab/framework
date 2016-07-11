<?php

	return [
		'lots' => [
				'lot_code'	=> 'required|min:1|max:16|unique:lots,lot_code,NULL,id,deleted_at,NULL',
				'part_id'	=> 'required',
				'quantity'	=> 'required|numeric|min:0'
			],
		'boms' => [
				'bom_code'		=> 'required|min:1|max:16|unique:boms,bom_code,:id,id,deleted_at,NULL',
				'title'			=> 'required|min:3|max:64',
				'default_route'	=> ''
			],
		'parts' => [
				'part_code'	=> 'required|max:16|min:1|unique:parts,part_code,:id,id,deleted_at,NULL',
				'title'		=> 'required|max:128|min:3'
			],
		'routes' => [
				'route_code'=> 'required|max:16|min:1|unique:routes,route_code,:id,id,deleted_at,NULL',
				'title'		=> 'required|max:64|min:3'
			],
		'route_details' => [
				'operation'	=> 'required|max:64|min:3'
			],
		'production_order' => [
				'production_order_code'=> 'required|max:16|unique:production_orders,production_order_code,NULL,id,deleted_at,NULL',
				'quantity'	=> 'required|numeric',
				'part_id'	=> 'required'
			],
		'part_bom' => [
				'bom_id' => 'required|numeric',
				'part_id'	=> 'required|numeric'
		],
		'bom_route'	=> [
				'bom_id'	=> 'required|numeric',
				'route_id'	=> 'required|numeric'
		],
		'companies' => [
				'company_code'	=> 'required|max:16|min:1|unique:companies,company_code,:id,id,deleted_at,NULL',
				'name'			=> 'required|max:128|min:3'
		],
		'order_details' => [
				'quantity'	=> 'required|numeric',
				'part_id'	=> 'required'
		],
		'orders' => [
				'order_code'	=> 'required|min:1|max:16|unique:Orders,order_code,:id,id,deleted_at,NULL',
				'company_id'	=> 'required'
		],
		'order_detail_edit' => [
				'quantity' => 'required|numeric'
		]
	];