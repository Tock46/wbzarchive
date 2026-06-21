<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo '<meta http-equiv="refresh" content="0; url=/../">';
    exit();
}

// Redirect if any required POST data is missing
$requiredFields = [
    'dist_name', 'dist_version', 'dist_author', 'dist_release', 'dist_pre', 'dist_suc',
    'dist_region', 'dist_url', 'dist_info'
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        echo '<meta http-equiv="refresh" content="0; url=/../">';
        exit();
    }
}

// Retrieve form data
$dist_id     = $_POST['dist_id'];

$dist_name    = $_POST['dist_name'];
$dist_version = $_POST['dist_version'];
$dist_author  = $_POST['dist_author'];
$dist_release = $_POST['dist_release'];

$dist_pre     = isset($_POST['dist_pre']) ? $_POST['dist_pre'] : null;
$dist_suc     = isset($_POST['dist_suc']) ? $_POST['dist_suc'] : null;
$dist_region  = $_POST['dist_region'];
$dist_url     = $_POST['dist_url'];
$dist_info    = $_POST['dist_info'];

// Update the Distribution table directly (no revision)
$sql = "UPDATE Distribution SET 
            dist_name = ?, 
            dist_version = ?, 
            dist_author = ?, 
            dist_release = ?, 
            dist_pre = ?, 
            dist_suc = ?, 
            dist_region = ?, 
            dist_url = ?, 
            dist_info = ?
        WHERE dist_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param(
    'sssssssssi',
    $dist_name,
    $dist_version,
    $dist_author,
    $dist_release,
    $dist_pre,
    $dist_suc,
    $dist_region,
    $dist_url,
    $dist_info,
    $dist_id
);

if (!$stmt->execute()) {
    die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
}

$stmt->close();
$conn->close();

// Redirect after successful update
echo "<meta http-equiv='refresh' content='0; url=https://szslibrary.com/distribution.php?id={$dist_id}'>";
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./style.css">
    <title>SZS Library - Admin</title>
</head>
<body>
</body>
</html>
