<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
session_start();
header("Content-Security-Policy: default-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dbservername = "dbservername";
$dbusername = "dbusername";
$dbpassword = "dbpassword";
$dbname = "dbname";

// Create connection
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

$sourceDirThump = '/var/www/vhosts/szslibrary.com/httpdocs/thumbnail/';
$destinationDirThump = '/var/www/vhosts/szslibrary.com/httpdocs/library/thumbnailjpg/';
$u8_dir = '/var/www/vhosts/szslibrary.com/httpdocs/szsdecomp/';
$wbz_dir = '/var/www/vhosts/szslibrary.com/httpdocs/library/wbz/';
$base_dir = '/var/www/vhosts/szslibrary.com/httpdocs';

//$sql = "INSERT INTO tracks (trackname)
//VALUES ('besttrackever')";

//if ($conn->query($sql) === TRUE) {
//  echo "New record created successfully";
//} else {
//  echo "Error: " . $sql . "<br>" . $conn->error;
//}
?>