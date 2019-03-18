<?php
	//Require the ReallySimpleJWT and set up other basics.
	require_once('vendor/autoload.php');
	use ReallySimpleJWT\Token;
	$result = NULL;
	$result2 = NULL;

	//Here we catch the form inputs arriving in the POST. If the POST array is empty we move straight to
	//the page without further PHP, otherwise we move to the validation. If the $_POST data passes validation,
	//we send it forward. Othewise we print errors to the page.
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($form_errors = validate_form()) {
			//Print errors.
			//This happens below in the .html so this just serves as an empty space...
		} else {
			//If $_POST contains 't', 'y' and 'plot' we  know it's a movie and thus we send that
			//data to the /getMovie endpoint.
			if (isset($_POST['t']) && isset($_POST['y']) && isset($_POST['plot'])) {
				$url = "http://topiniskala.com/~topi/testapi/getMovie.php";
				$data = array('t' => htmlentities($_POST['t']), 'y' => htmlentities($_POST['y']), 'plot' => htmlentities($_POST['plot']));
				$data_string = http_build_query($data);
				//Here we build the JWT payload from the stringified data array. The payload has a
				//limited timespan to make it more secure.
				$payload = [
					'iat' => time(),
					'uid' => 1,
					'exp' => time() + 10,
					'data' => $data_string
				];
				$secret = 'sec!ReT423*&';
				//Here we use ReallySimpleJWT to build the payload and secret into a JWT token
				//and further on we turn it into a string through http_build_query().
				$token = Token::customPayload($payload, $secret);
				$tokendata = array('token' => $token);
				$token_string = http_build_query($tokendata);
				//Here we send the token using cUrl to the REST API
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $token_string);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($curl);
			//Similarly here we check if we have 'isbn' in the $_POST[]. Otherwise this is similar to
			//the previous cUrl POST request.
			} else if (isset($_POST['isbn'])) {
				$url = "http://topiniskala.com/~topi/testapi/getBook.php";
				$data = array('isbn' => htmlentities($_POST['isbn']));
				$data_string = http_build_query($data);
				$payload = [
					'iat' => time(),
					'uid' => 1,
					'exp' => time() + 10,
					'data' => $data_string
				];
				$secret = 'sec!ReT423*&';
				$token = Token::customPayload($payload, $secret);
				$tokendata = array('token' => $token);
				$token_string = http_build_query($tokendata);
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $token_string);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$result2 = curl_exec($curl);
			} else {
			}
		}
	}

	//Validation for the form.
	function validate_form() {
		$errors = array();
		if (!isset($_POST['isbn']) || $_POST['isbn'] == NULL) {
			if (!isset($_POST['t']) || $_POST['t'] == "" || $_POST['t'] == NULL) {
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
		} else {
			if (strlen($_POST['isbn']) != 13) {
				$errors['valid_isbn'] = "Please enter a valid isbn.";
			}
			$isbn_ok = filter_input(INPUT_POST, 'isbn', FILTER_VALIDATE_INT);
			if (! $isbn_ok) {
				$errors['valid_isbn'] = "Please enter a valid isbn.";
			}
		}
		return $errors;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>testapi</title>
	<meta charset="utf-8">
	<meta name="description" content="Test Api">
	<meta name="author" content="Topi Niskala">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- BOOTSTRAP, JQUERY, ETC. -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<h1>TEST API</h1>
		<p>Simple client to test the test REST API<br>
		Here are some test searches you can try: </p>
		<ul>
			<li>Dracula, 1931</li>
			<li>Breathless, 1960</li>
			<li>Batman, 1989</li>
			<li>ISBN: 9780870540370</li>
			<li>ISBN: 9780596520106</li>
			<li>ISBN: 9780192804730</li>
		</ul>
		<div class="row">
				<div class="col-lg-12">
				<div class="bs-component">
				<form class="well form-search" id="search-by-title-form" method="POST">
				<fieldset>
					<legend>/getMovie</legend>
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
						<option value="short" selected="short">Short</option>
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
		<div id="movie" name="movie">
			<?php
				//Here we print either results of query or returned errors for movies.
				if ($result != NULL) {
					print "<code>" . $result . "</code>";
				} else if (isset($form_errors['empty_title'])) {
					print "<div style='color:red'>" . $form_errors['empty_title'] . "</div>";
				} else if (isset($form_errors['valid_year'])) {
					print "<div style='color:red'>" . $form_errors['valid_year'] . "</div>";
				} else if (isset($form_errors['valid_plot'])) {
					print "<div style='color:red'>" . $form_errors['valid_plot'] . "</div>";
				}
			?>
		</div>
		<hr>
		<div class="row">
			<div class="col-lg-12">
				<div class="bs-component">
				<form class="well form-search" id="search-by-title-form" method="POST">
				<fieldset>
					<legend>/getBook</legend>
				</fieldset>
				<div>
					<label class="control-label" for="t">ISBN13:</label>
					<input type="text" id="isbn" name="isbn" class="input-small">
					&nbsp;&nbsp;
					<button id="search-by-title-button" type="submit" class="btn-sm btn-success">Search</button>
					<button id="search-by-title-reset" type="reset" class="btn-sm">Reset</button>
				</div>
				</form>
				</div>
			</div>
		</div>
		<div id="book" name="book">
			<?php
				//Here we print either results of query or returned errors for books.
				if ($result2 != NULL) {
					print "<code>" . $result2 . "</code>";
				} else if (isset($form_errors['valid_isbn'])) {
					print "<div style='color:red'>" . $form_errors['valid_isbn'] . "</div>";
				}
			?>
		</div>
	</div>
</body>
</html>
