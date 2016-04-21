<?php
	use Symfony\Component\HttpFoundation\Request;

	$app->get('/orders', function (Request $request) use ($app, $database) {
		$providerId = $request->get('providerId');
		$clientId = $request->get('clientId');
		if ($clientId || $providerId)
		{
			if ($clientId && $providerId)
				return $app->json($database->select('orders', '*',  ["AND" => ["providerId" => $providerId, "clientId" => $clientId]]), 200);
			else if ($providerId)
				return $app->json($database->select('orders', '*',  ["providerId" => $providerId]), 200);
			else
				return $app->json($database->select('orders', '*',  ["clientId" => $clientId]), 200);
		}	
	    return $app->json($database->select('orders', '*'), 200);
	});

	$app->get('/orders/{id}', function ($id) use ($app, $database) {
		    return $app->json($database->select("orders", "*", ["id" => $id]), 200);
		});

	$app->post('/orders', function (Request $request) use ($app, $database) {
		$neo = array(
			'clientId' => $request->get('clientId'),
			'providerId' => $request->get('providerId'),
			'price' => $request->get('price'),
			'state' => 'requested',
			'serviceId' => $request->get('serviceId'),
			'lastUpdate' => date('Y-m-d H:i:s'),
			'creationDate' => date('Y-m-d H:i:s')
		);
		$id = $database->insert('orders', $neo);
	    return $app->json(['id' => $id], 201);
	});