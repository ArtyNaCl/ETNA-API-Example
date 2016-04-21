<?php
	
	error_reporting(-1);				//DEBUG
	ini_set('display_errors', 'On');	//DEBUG
	ini_set('precision', '9');

	require_once __DIR__.'/../sources/vendor/autoload.php';
	require_once __DIR__.'/../sources/database.php';
	require_once __DIR__.'/geoloc.php';

	use Symfony\Component\HttpFoundation\Request;
	use \geoloc;

	$app = new Silex\Application();
	$app['debug'] = true;				//DEBUG

	/*---------------- GENERAL ---------------*/

	$app->post('/connexion', function (Request $request) use ($app, $database) {
		$email = $request->get('email');
		$passwd = $request->get('passwd');
		if (isset($email) && $email != NULL && isset($passwd))
		{
			if ($database->select('clients', 'email', ['email' => $email]))
				$userType = 'clients';
			else if ($database->select('providers', 'email', ['email' => $email]))
				$userType = 'providers';
			if (isset($userType) && $database->select($userType, 'passwd', ['email' => $email])[0] == $request->get('passwd'))
			{
				$database->update($userType, ['lastConnexion' => date('Y-m-d H:i:s')], ['email' => $email]);
				return $app->json(['token' => 'TOKEN TA MERE'],
				 200);
			}
		}		
		return $app->json([], 401);
	});

	/*---------------- CLIENTS ---------------*/

	$app->get('/clients', function () use ($app, $database) {
	    return $app->json($database->select("clients", "*"), 200);
	});

	$app->get('/clients/{id}', function ($id) use ($app, $database) {
		    return $app->json($database->select("clients", "*", ["clientId" => $id]), 200);
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
		$debug = $database->insert('clients', $neoUser);
	    return $app->json(['status' => "Success id = $debug"], 201);
	});

	/*---------------- PROVIDERS ---------------*/

	$app->get('/providers', function (Request $request) use ($app, $database) {
		if ($request->get('profession') && $request->get('userLat') && $request->get('userLon'))
		{
			$answer = array();
			$request->get('range') ? $range = $request->get('range') : $range = 2000;
			$geo = new \geoloc\Geoloc($request->get('userLat'), $request->get('userLon'), $range);
			$data = $database->select('providers', ['providerId', 'posLat', 'posLon'], ['profession' => $request->get('profession')]);
			foreach ($data as $providerData)
			{	
				$controler = $geo->isInRange($providerData['posLat'], $providerData['posLon'], $range);
				if ($controler)
					array_push($answer, $database->select('providers', ['providerId', 'posLat', 'posLon', 'name', 'firstName', 'socialReason', 'description'], ['providerId' => $providerData['providerId']]));
			}
			return $app->json($answer, 200);
		}
		if ($request->get('profession'))
			return $app->json($database->select('providers', '*', ['profession' => $request->get('profession')]), 200);
	    return $app->json($database->select("providers", '*'), 200);
	});

	$app->get('/providers/{id}', function ($id) use ($app, $database) {
		    return $app->json($database->select("providers", "*", ['providerId' => $id]), 200);
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
		$database->insert('providers', $neoUser);
	    return $app->json(['status' => 'Success'], 201);
	});	

	$app->run();