<?php
	use Symfony\Component\HttpFoundation\Request;

	$app->post('/connexion', function (Request $request) use ($app, $database) {
		$email = $request->get('email');
		$passwd = $request->get('passwd');
		$step = 0;
		if (isset($email) && $email != NULL && isset($passwd))
		{

			if ($database->select('clients', 'email', ['email' => $email]))
			{
				$step++;
				$userType = 'clients';
			}
			else if ($database->select('providers', 'email', ['email' => $email]))
				$userType = 'providers';
			$userType == 'clients' ? $index = 'userId' : $index = 'providerId';
			$data = $database->select($userType, ['passwd', 'id'], ['email' => $email]);
			if (isset($userType) && $data[0]['passwd'] == $passwd)
			{
				$database->update($userType, ['lastConnexion' => date('Y-m-d H:i:s')], ['email' => $email]);
				return $app->json(['type' => $userType, 'id' => $data[0]['id'], 'token' => 'TOKEN TA MERE'], 200);
			}
		}		
		return $app->json([],401);
	});