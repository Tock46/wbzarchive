<?php
session_start();
$dbservername = "servername";
$dbusername = "username";
$dbpassword = "password";
$dbname = "databasename";

// Create connection
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
?>