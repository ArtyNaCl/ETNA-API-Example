<?php
	use Symfony\Component\HttpFoundation\Request;

	$app->get('/clients', function () use ($app, $database) {
	    return $app->json($database->select("clients", "*"), 200);
	});

	$app->get('/clients/{id}', function ($id) use ($app, $database) {
			$data = $database->select("clients", "*", ["id" => $id]);
			$ddbAge = $data[0]['age'];
			$ddbAge = explode("/", $ddbAge);
			$data[0]['age'] = strval((date("md", date("U", mktime(0, 0, 0, $ddbAge[1], $ddbAge[2], $ddbAge[0]))) > date("md") ? ((date("Y") - $ddbAge[0]) - 1) : (date("Y") - $ddbAge[0])));
		    return $app->json($data, 200);
		//return $app->json($database->select("clients", "*", ["id" => $id]), 200);
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

	$app->put('/clients/{id}', function (Request $request) use ($app, $database) {
		$data = array('email' => $request->get('email'));
		if ($request->get('passwd'))
			$data['passwd'] = $request->get('passwd');
	    return $app->json($database->update('clients', $data, ['id' => $request->get('id')]), 200);
	});