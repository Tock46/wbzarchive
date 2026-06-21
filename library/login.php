<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
if (isset($_SESSION['loggedin'])) {
	echo ('<meta http-equiv="refresh" content="0; url=/../">');
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
   <title>SZS Library</title>
</head>

<body>

 <?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; // Top Login Button
?> 
<div id="container">
	<a href='/?type=files'><h1>SZS Library</a> - <a href='/?type=distribution'>Distributions</a></h1>

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
				
				<input type="checkbox" name="remember" id="remember" style="margin-right:5px">        Remember Me
				
				<input type="submit" value="Login">
			</form>
		</div>
		<br>
		</div>
<?php 
require '/var/www/vhosts/szslibrary.com/httpdocs/included/footer.php'; // Bottom Footer
?> 
</body>
</html>
