<?php
	use Symfony\Component\HttpFoundation\Request;

	$app->get('/clients', function () use ($app, $database) {
	    return $app->json($database->select("clients", "*"), 200);
	});

	$app->get('/clients/{id}', function ($id) use ($app, $database) {
		    return $app->json($database->select("clients", "*", ["id" => $id]), 200);
		});

	$app->post('/clients', function (Request $request) use ($app, $database) {
		if ($database->select('clients', 'email', ['email' => $request->get('email')]) || $database->select('providers', 'email', ['email' => $request->get('email')]))
			return $app->json(['error' => 'Email already exist'], 409);
		$neoUser = array(
			'email' => $request->get('email'),
			'passwd' => $request->get('passwd'),
			'name' => $request->get('name'),
			'firstName' => $request->get('firstName'),
			'age' => $request->get('age'),
			'socialReason' => $request->get('socialReason'),
			'confirmed' => 0,
			'lastConnexion' => date('Y-m-d H:i:s'),
			'creationDate' => date('Y-m-d H:i:s')
		);
		$id = $database->insert('clients', $neoUser);
	    return $app->json(['id' => $id], 201);
	});