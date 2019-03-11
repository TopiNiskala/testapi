<?php
	//This is the basic setup of the page. If we have stuff in the POST, we go to validate it.
	//Otherwise we print the form.
	//If the data passess validation, we go to process it. If it doesn't we print the errors.
	//For this we use a series of if/else sentences and $_SERVER array.
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($form_errors = validate_form()) {
			show_form($form_errors);
		} else {
			process_form();
		}
	} else {
		show_form();
	}

	//PROCESS FORM
	//Here we set up the parameters of the JSON query  and the perform the query to the omdbapi URL.
	//As a result we get a JSON list which we print on the page.
	function process_form() {
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

	//SHOW FORM
	function show_form($errors = array()) {
		if ($errors) {
			print "<br>Errors in the form: <ul style='color:red;'><li>";
			print implode('</li><li>', $errors);
			print "</li></ul>";
		}
		print<<<_HTML_
<!DOCTYPE html>
<html lang='en'>
<head>
<title>Test Api</title>
	<meta charset='utf-8'>
	<meta name='description' content='Test Api'>
	<meta name='author' content='Topi Niskala'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link rel='stylesheet' type='text/css' href='css/bootstrap.min.css'>
	<script src='js/jquery-3.3.1.min.js'></script>
	<script src='js/bootstrap.min.js'></script>
</head>
<body>
	<div class="container">
		<h1>TEST API - Movies</h1>
		<p>Here you can search for movies from the OMDB database.</p>
		<div class="row">
			<div class="col-lg-12">
				<div class="bs-component">
					<form class="well form-search" id="search-by-title-form" method="POST">
						<fieldset>
							<legend>Search movies by title</legend>
						</fieldset>
						<div>
							<label class="control-label" for="t">Title:</label>
							<input type="text" id="t" name="t" class="input-small">
							&nbsp;&nbsp;
							<label class="control-label" for="y">Year:</label>
							<input type="text" id="y" name="y" class="input-small" style="width: 100px;">
							&nbsp;&nbsp;
							<label class="control-label">Plot:</label>
							<select name="plot" style="width: 100px;">
								<option value="short" selected="">Short</option>
								<option value="full">Full</option>
							</select>
							&nbsp;&nbsp;
							<button id="search-by-title-button" type="submit" class="btn-sm btn-success">Search</button>
							<button id="search-by-title-reset" type="reset" class="btn-sm">Reset</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
_HTML_;
	}

	//VALIDATE FORM
	//Quick validation for the form. Checks:
	//1. If the user entered a valid title.
	//2. If the year is a proper integer (only if it is set).
	//3. If the plot choice is not "" or "full".
	//Return an array of found errors.
	function validate_form() {
		$errors = array();

		//Validate title
		if (!isset($_POST['t'])) {
			$errors['empty_title'] = "Please enter a title.";
		}

		//Validate year
		if (isset($_POST['y']) && $_POST['y'] != "") {
			$year_ok = filter_input(INPUT_POST, 'y', FILTER_VALIDATE_INT);
			if (is_null($year_ok) || ($year_ok === false)) {
				$errors['valid_year'] = "Please enter a valid year.";
			}
		}

		//Validate plot choice
		if (!isset($_POST['plot'])) {
			$errors['valid_plot'] = "Please enter a valid plot choice.";
		} else {
			if ($_POST['plot'] != "short") {
				if ($_POST['plot'] != "full") {
					$errors['valid_plot'] = "Please enter a valid plot choice.";
				}
			}
		}
		return $errors;
	}
?>
