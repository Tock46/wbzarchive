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
	<a href='/'><h1>SZS Library</a> - <a href='/?type=distribution'>Distributions</a></h1>
		<div class="register">
			<h1>Register</h1>
			<ul>
				<li>A password has to be at least 12 characters long.</li>
				<li>The Email address currently is unused.</li>
				<li>Before you can submit files, you have to be enabled by the owner of the site.</li>
			</ul> 
			<form action="/scripts/registering.php" method="post" autocomplete="off">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="email" name="email" placeholder="Email" id="email" required>
				<input type="submit" value="Register">
			</form>
			<br>Remember, if the owner wanted to, he could save your password instead of the hash. It is recommended to not reuse passwords from other sites.
		</div>
		<br>
</body>
</html>
