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
	<form action="search.php" method="post">
<input type="checkbox" <?php if(isset($_POST['btnControl1'])) echo "checked"; ?> name="btnControl1" id="btnControl1"/><label class="btn1" for="btnControl1">Custom Track</label><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl2'])) echo "checked"; ?> name="btnControl2" id="btnControl2"/><label class="btn2" for="btnControl2">Custom Arena</label><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl3'])) echo "checked"; ?> name="btnControl3" id="btnControl3"/><label class="btn3" for="btnControl3">Track Change</label><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl4'])) echo "checked"; ?> name="btnControl4" id="btnControl4"/><label class="btn4" for="btnControl4">Texture Hack</label><br><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl5'])) echo "checked"; ?> name="btnControl5" id="btnControl5"/><label class="btn5" for="btnControl5">Custom Character</label><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl6'])) echo "checked"; ?> name="btnControl6" id="btnControl6"/><label class="btn6" for="btnControl6">Custom Vehicle</label><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl7'])) echo "checked"; ?> name="btnControl7" id="btnControl7"/><label class="btn7" for="btnControl7">Custom Competition</label><?php
?><input type="checkbox" <?php if(isset($_POST['btnControl8'])) echo "checked"; ?> name="btnControl8" id="btnControl8"/><label class="btn8" for="btnControl8">PEGI 18 Custom Tracks</label>
	<input id="searchbox" type="text" placeholder=<?php echo("Search...")?> name="search" required><button id="searchbutton" type="submit" name="submit">Search</button>
	</form>
		<div class="register">
			<h1>Register</h1>
			<ul>
				<li>A password has to be at least 12 characters long.</li>
				<li>The Email address currently is unused.</li>
				<li>Before you can submit files, you have to be enabled by the owner of the site.</li>
			</ul> 
			<form action="registering.php" method="post" autocomplete="off">
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
