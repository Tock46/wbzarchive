<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
	echo ('<meta http-equiv="refresh" content="0; url=/../">');
    exit();
}

// Redirect if any required POST data is missing
$requiredFields = [
    'trackname', 'track_version', 'track_author',
    'track_created', 'track_music', 'track_prop', 'downloadlink',
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
	echo "hmm3";
	echo ('<meta http-equiv="refresh" content="0; url=/../">');
        exit();
    }
}

$sql = "SELECT min(id_first + 1) AS next_available_id 
        FROM tracks 
        WHERE id_first + 1 NOT IN (SELECT id_first FROM tracks) 
        AND id_first + 1 >= 20600";

// Execute query
$result = $conn->query($sql);

// Fetch the result
if ($result) {
    $row = $result->fetch_assoc();
    $nextAvailableId = $row['next_available_id'];
	}

// Retrieve form data
$id_first = $nextAvailableId;
$track_wiimm = $id_first;
$username = $_SESSION['name'];
$track_family = $_POST['track_family'] == 0 ? NULL : $_POST['track_family'];
$track_clan = $_POST['track_clan'] == 0 ? NULL : $_POST['track_clan'];
$prefix = $_POST['prefix'];
$trackname = $_POST['trackname'];
$track_version = $_POST['track_version'];
$track_version_extra = $_POST['track_version_extra'];
$track_author = $_POST['track_author'];
$track_editor = $_POST['track_editor'];
$track_created = $_POST['track_created'];
$track_music = $_POST['track_music'];
$track_prop = $_POST['track_prop'];
if (isset($_POST['track_type'])) {
$track_type = $_POST['track_type'] == 0 ? NULL : $_POST['track_type'];
}
else {
    $track_type = 2; // Replace 'default_value' with your default data
}
$track_download = $_POST['downloadlink'];

// Check optional checkboxes
$id_enabled = 		 isset($_POST['id_enabled']) 		? 1 : 0;
$track_customtrack = isset($_POST['track_customtrack']) ? 1 : 0;
$track_customarena = isset($_POST['track_customarena']) ? 1 : 0;
$track_texturehack = isset($_POST['track_texturehack']) ? 1 : 0;
$track_boost = 		 isset($_POST['track_boost'])		? 1 : 0;
$track_competition = isset($_POST['track_competition']) ? 1 : 0;
$track_change = 	 isset($_POST['track_change']) 		? 1 : 0;
$track_nintendo = 	 isset($_POST['track_nintendo']) 	? 1 : 0;

// Insert new track data

$track_unique = sha1($id_first);

$sql = 'INSERT INTO tracks (id_enabled, id_first, track_wiimm, track_family, track_clan, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_created, track_music, track_prop, track_customtrack, track_customarena, track_texturehack, track_boost, track_competition, track_change, track_nintendo, last_mod, track_download) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';

$stmt = $conn->prepare($sql);

if (!$stmt) {
	echo "hmm1";
    die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param('iiiiisssssssiiiiiiiiiss', 
    $id_enabled, $id_first, $track_wiimm, $track_family, $track_clan, $prefix, 
    $trackname, $track_version, $track_version_extra, $track_author, $track_editor, 
    $track_created, $track_music, $track_prop,
    $track_customtrack, $track_customarena, 
    $track_texturehack, $track_boost, $track_competition, $track_change, $track_nintendo,
    $username, $track_download
);

// Execute the statement
if (!$stmt->execute()) {
	echo "hmm";
    die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
}

$sql3 = "UPDATE tracks SET track_version_extra = NULL WHERE track_version_extra = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET track_clan = NULL WHERE track_clan = '0'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET track_music = NULL WHERE track_music = 0";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET track_prop = NULL WHERE track_prop = -1";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET prefix = NULL WHERE prefix = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET track_version = NULL WHERE track_version = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET track_editor = NULL WHERE track_editor = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE tracks SET track_family = NULL WHERE track_family = '-'";
$result4 = $conn->query($sql3);

// Insert new track data

	$sql = 'INSERT INTO revision (id_enabled, id_first, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_family, track_clan, track_created, track_customtrack, track_customarena, track_competition, track_texturehack, track_boost, track_change, track_nintendo, track_music, track_prop, last_mod) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
	}

	$stmt->bind_param('iissssssiisiiiiiiiiis', 
		$id_enabled, $id_first, $prefix, $trackname, $track_version, $track_version_extra, 
		$track_author, $track_editor, $track_family, $track_clan, $track_created, 
		$track_customtrack, $track_customarena, $track_competition, $track_texturehack, $track_boost, $track_change, $track_nintendo, 
		$track_music, $track_prop, $username
	);

// Execute the statement
if (!$stmt->execute()) {
    die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
}

$sql3 = "UPDATE revision SET track_version_extra = NULL WHERE track_version_extra = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET track_clan = NULL WHERE track_clan = '0'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET track_music = NULL WHERE track_music = 0";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET track_prop = NULL WHERE track_prop = -1";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET prefix = NULL WHERE prefix = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET track_version = NULL WHERE track_version = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET track_editor = NULL WHERE track_editor = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE revision SET track_family = NULL WHERE track_family = '-'";
$result4 = $conn->query($sql3);

$stmt->close();
$conn->close();

// Redirect after successful operation
	echo ('<meta http-equiv="refresh" content="0; url=/../">');

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <!--<link rel="shortcut icon" href="./.favicon.ico">-->
    <link rel="stylesheet" href="./style.css">
    <title>WBZ Archive - Admin</title>
</head>
<body>
</body>
</html>