<?php
session_start();
session_destroy();
// Remove the cookie by setting an expiration date in the past
setcookie('remember_me', '', time() - 3600, "/");
// Redirect to the login page:
header('Location: /../');
?>