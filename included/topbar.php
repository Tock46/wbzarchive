 <?php // Top Login Button

if (!isset($_SESSION['loggedin'])) {
 echo '<a href="/login.php"><button class="loginbutton"><h2>Login</h2></button></a><br><br>';
}
 else {
 echo '<a href="/scripts/logout.php"><button class="loginbutton"><h2>Logout</h2></button></a><a href="/submit.php"><button class="loginbutton"><h2>Submit</h2></button></a><a href="/missingfiles.php"><button class="loginbutton"><h2>Missing Files</h2></button></a><br><br>';
 }
?> 