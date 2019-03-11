<?php
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
	}

	//GETMOVIE
	//Here we set up the parameters of the JSON query  and the perform the query to the omdbapi URL.
	//As a result we get a JSON list which we print on the page.
	function getMovie() {
		header('Content-Type: application/json');
		$params = array('apikey' => '87520499',
				't' => htmlentities($_POST['t']),
				'y' => htmlentities($_POST['y']),
				'plot' => htmlentities($_POST['plot']),
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

	//VALIDATE FORM
	//Quick validation for the form. Checks:
	//1. If the user entered a valid title.
	//2. If the year is a proper integer.
	//3. If the plot choice is not "" or "full".
	//Return an array of found errors.
	function validate() {
		$errors = array();

		//Validate title
		if (!isset($_POST['t'])) {
			$errors['empty_title'] = "Please enter a title.";
		}

		//Validate year
		$year_ok = filter_input(INPUT_POST, 'y', FILTER_VALIDATE_INT);
		if (is_null($year_ok) || ($year_ok === false)) {
			$errors['valid_year'] = "Please enter a valid integer for year.";
		}

		//Validate plot choice
		if (!isset($_POST['plot'])) {
			$errors['valid_plot'] = "Please enter a valid plot choice ('short' or 'full').";
		} else {
			if ($_POST['plot'] != "short") {
				if ($_POST['plot'] != "full") {
					$errors['valid_plot'] = "Please enter a valid plot choice ('short' or 'full').";
				}
			}
		}
		return $errors;
	}
?>
