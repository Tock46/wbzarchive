<?php
session_start();
require __DIR__ . '/included/header.php';
if (isset($_SESSION['loggedin'])) {
	echo ('<meta http-equiv="refresh" content="0; url=http://archive.tock.eu/">');
	exit();
}
?>
<!doctype html>
<html>
<head>
   <meta charset="UTF-8">
   <!--<link rel="shortcut icon" href="./.favicon.ico">-->
   <link rel="stylesheet" href="./style.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
   <title>WBZ Archive</title>
</head>

<body>

 <?php
require __DIR__ . '/included/topbar.php'; // Top Login Button
?> 
<div id="container">
	<a href='/?type=files'><h1>WBZ Archive</a> - <a href='/?type=distribution'>Distributions</a></h1>

 <?php 
require __DIR__ . '/included/search.php'; // Search
?>

		<div class="login">
			<h1>Login</h1>
			<form action="scripts/authenticate.php" method="post">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="submit" value="Login">
			</form>
		</div>
		<br>
		</div>
<?php 
require __DIR__ . '/included/footer.php'; // Bottom Footer
?> 
</body>
</html>
