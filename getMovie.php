<?php
	//Require the ReallySimpleJWT token.
	require_once('vendor/autoload.php');
	use ReallySimpleJWT\Token;
	//This is the basic setup of the page. If we have stuff in the POST, we go to validate it.
	//If the data passess validation, we go to process it. If it doesn't we print the errors.
	//For this we use a series of if/else sentences and $_SERVER array.
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($form_errors = validate()) {
			getError($form_errors);
		} else {
			getMovie();
		}
	} else {
		$form_errors['error'] = "Invalid query";
		getError($form_errors);
	}

	//GETMOVIE
	//Here we set up the parameters of the JSON query  and the perform the query to the omdbapi URL.
	//As a result we get a JSON list which we print on the page. As the parameters are within the JWT
	//token they need a bit of disassembly.
	function getMovie() {
		$tokenstring = $_POST['token'];
		$secret = 'sec!ReT423*&'; //Bad token, should be more abstract.
		$token = Token::getPayload($tokenstring, $secret);
		$data = $token['data'];
		parse_str($data, $data);
		header('Content-Type: application/json');
		$params = array('apikey' => '87520499',
				't' => $data['t'],
				'y' => $data['y'],
				'plot' => $data['plot'],
				'r' => 'json');
		$url = "http://www.omdbapi.com/?" . http_build_query($params);
		$response = file_get_contents($url);
		print $response;
	}

	//SHOW ERRORS
	//If the POST call did not pass validation, we send a json list of errors to the user.
	function getError($errors = array()) {
		if ($errors) {
			header('Content-Type: application/json');
			print json_encode($errors);
		}
	}

	//VALIDATION
        //Quick validation for the request. Uses token validation from the ReallySimpleJWT.
        //We could have additional validation here also for the data in the token, but we already do that on
        //the client.
	function validate() {
		$errors = array();
		if (!isset($_POST['token'])) {
			$errors['error'] = "Invalid query";
		} else {
			$token = $_POST['token'];
			$secret = 'sec!ReT423*&';
			$result = Token::validate($token, $secret);
			if ($result != TRUE) {
				$errors['error'] = "Invalid query";
			}
		}
		return $errors;
	}
?>
