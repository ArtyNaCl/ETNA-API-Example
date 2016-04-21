<?php
	use Symfony\Component\HttpFoundation\Request;

	$app->get('/services', function (Request $request) use ($app, $database) {
		$providerId = $request->get('providerId');
		if ($providerId)
			return $app->json($database->select('services', '*', ['providerId' => $providerId]), 200);
	    return $app->json($database->select('services', '*'), 200);
	});

	$app->get('/services/{id}', function ($id) use ($app, $database) {
		    return $app->json($database->select("services", "*", ["id" => $id]), 200);
		});

	$app->post('/services', function (Request $request) use ($app, $database) {
		$neo = array(
			'label' => $request->get('label'),
			'providerId' => $request->get('providerId'),
			'price' => $request->get('price'),
			'description' => $request->get('description')
		);
		$id = $database->insert('services', $neo);
	    return $app->json(['id' => $id], 201);
	});