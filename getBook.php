<?php
	//This is the basic setup of the page. If we have stuff in the POST, we go to validate it.
	//If the data passess validation, we go to process it. If it doesn't we print the errors.
	//For this we use a series of if/else sentences and $_SERVER array.
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($form_errors = validate()) {
			getError($form_errors);
		} else {
			getBook();
		}
	} else {
	}

	//GETBOOK
	//Here we set up the parameters of the JSON query  and the perform the query to the openLibrary URL.
	//As a result we get a JSON list which we print on the page.
	function getBook() {
		header('Content-Type: application/json');
		$params = array('bibkeys' => htmlentities($_POST['isbn']),
				'format' => 'json',
				'jscmd' => 'data');
		$url = "http://www.openlibrary.org/api/books?" . http_build_query($params);
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
	//Quick validation for the request. Checks:
	//1. If the user entered a valid ISBN number.
	//Return an array of found errors.
	function validate() {
		$errors = array();

		//Validate title
		if (!isset($_POST['isbn'])) {
			$errors['empty_title'] = "Please enter a valid isbn.";
		}

		return $errors;
	}
?>
