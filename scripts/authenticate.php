<?php
session_start();
require __DIR__ . '/../included/header.php';
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $conn->prepare('SELECT id, password, enabled FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->bind_result($id, $password, $enabled);
	$stmt->fetch();
	if ($enabled == 0) {
	echo "Account not enabled yet";
	exit;
	}
	// Account exists and is enabled, now we verify the password.
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if (password_verify($_POST['password'], $password)) {
		// Verification success! User has logged-in!
		// Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;
		
		if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
        // If so, create a new hash, and replace the old one
        $newHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Update the user record with the $newHash
		if ($stmt2 = $conn->prepare('UPDATE accounts SET password = ? WHERE username = ?')) {
			// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
			$stmt2->bind_param('ss', $newHash, $_POST['username']);
			$stmt2->execute();
		}
		}
		echo 'Welcome ' . $_SESSION['name'] . '!';
	} else {
		// Incorrect password
		echo 'Incorrect username and/or password!';
	}
} else {
	// Incorrect username
	echo 'Incorrect username and/or password!';
}
	$stmt->close();
}
    echo '<meta http-equiv="refresh" content="0; url=http://archive.tock.eu/">';
?>

