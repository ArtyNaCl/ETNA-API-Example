<?php
	use Symfony\Component\HttpFoundation\Request;

	$app->get('/providers', function (Request $request) use ($app, $database) {
		if ($request->get('profession') && $request->get('userLat') && $request->get('userLon'))
		{
			$answer = array();
			$request->get('range') ? $range = $request->get('range') : $range = 2000;
			$geo = new \geoloc\Geoloc($request->get('userLat'), $request->get('userLon'), $range);
			$data = $database->select('providers', ['id', 'posLat', 'posLon'], ['profession' => $request->get('profession')]);
			foreach ($data as $providerData)
			{	
				$controler = $geo->isInRange($providerData['posLat'], $providerData['posLon'], $range);
				if ($controler)
					array_push($answer, $database->select('providers', ['id', 'posLat', 'posLon', 'name', 'firstName', 'socialReason', 'description'], ['id' => $providerData['id']]));
			}
			return $app->json($answer, 200);
		}
		if ($request->get('profession'))
			return $app->json($database->select('providers', '*', ['profession' => $request->get('profession')]), 200);
	    return $app->json($database->select("providers", '*'), 200);
	});

	$app->get('/providers/{id}', function ($id) use ($app, $database) {
		    return $app->json($database->select("providers", "*", ['id' => $id]), 200);
		});

	$app->post('/providers', function (Request $request) use ($app, $database) {
		if ($database->select('clients', 'email', ['email' => $request->get('email')]) &&
			$database->select('providers', 'email', ['email' => $request->get('email')]))
			return $app->json(['error' => 'Email already exist'], 409);
		$neoUser = array(
			'email' => $request->get('email'),
			'passwd' => $request->get('passwd'),
			'name' => $request->get('name'),
			'firstName' => $request->get('firstName'),
			'age' => $request->get('age'),
			'socialReason' => $request->get('socialReason'),
			'profession' => $request->get('profession'),
			'phoneNumber' => $request->get('phoneNumber'),
			'iban' =>$request->get('iban'),
			'confirmed' => 0,
			'cniState' => 'nocni',
			'kbisState' => 'waitforcni',
			'posLat' => 0.0,
			'posLon' => 0.0,
			'state' => 'online',
			'lastConnexion' => date('Y-m-d H:i:s'),
			'creationDate' => date('Y-m-d H:i:s')
		);
		$id = $database->insert('providers', $neoUser);
	    return $app->json(['id' => $id], 201);
	});	