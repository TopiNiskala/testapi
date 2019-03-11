<!DOCTYPE html>
<html lang="en">
<head>
	<!-- BASIC STUFF -->
	<title>TEST API</title>
	<meta charset="utf-8">
	<meta name="description" content="Test Api">
	<meta name="keywords" content="PHP program for job application">
	<meta name="author" content="Topi Niskala">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- BOOTSTRAP, JQUERY, ETC. -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<h1>TEST API</h1>
		<p>Simple example URLs to test the API.</p>
		<h3>/getMovie</h3>
		<ul>
			<li><code>curl -d "t=Dracula&y=1931&plot=full" -X POST http://www.topiniskala.com/~topi/testapi/getMovie.php</code></li>
			<li><code>curl -d "t=Suspiria&y=1977&plot=short" -X POST http://www.topiniskala.com/~topi/testapi/getMovie.php</code></li>
		</ul>
		<h3>/getBook</h3>
		<ul>
			<li><code>curl -d "isbn=9780870540370" -X POST http://www.topiniskala.com/~topi/testapi/getBook.php</code></li>
			<li><code>curl -d "isbn=" -X POST http://www.topiniskala.com/~topi/testapi/getBook.php</code></li>
		</ul>
	</div>
</body>
</html>
