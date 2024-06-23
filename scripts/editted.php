<?php
session_start();
require __DIR__ . '/../included/header.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo '<meta http-equiv="refresh" content="0; url=http://archive.tock.eu/">';
    exit();
}

// Redirect if any required POST data is missing
$requiredFields = [
    'trackname', 'track_version', 'track_author', 'track_family', 'track_clan', 
    'track_wiki', 'track_created', 'track_music', 'track_prop',
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        echo '<meta http-equiv="refresh" content="0; url=http://archive.tock.eu/">';
        exit();
    }
}

// Retrieve form data
$id_first = $_POST['id_first'];
$username = $_SESSION['name'];
$track_family = $_POST['track_family'];// == 0 ? NULL : $_POST['track_family'];
$track_clan = $_POST['track_clan'];
if ($track_clan < 1)
{
	$track_clan = NULL;
}
$prefix = $_POST['prefix'];
if ($prefix == "")
{
	$prefix = NULL;
}
$trackname = $_POST['trackname'];
$track_version = $_POST['track_version'];
$track_version_extra = $_POST['track_version_extra'];
if ($track_version_extra == "")
{
	$track_version_extra = NULL;
}
$track_author = $_POST['track_author'];
$track_editor = $_POST['track_editor'];
if ($track_editor == "")
{
	$track_editor = NULL;
}
$track_created = $_POST['track_created'];
$track_music = $_POST['track_music'];
$track_prop = $_POST['track_prop'];
$track_wiki = $_POST['track_wiki'];
if ($track_wiki < 1)
{
	$track_wiki = NULL;
}
$track_type = 0;

// Check optional checkboxes
$id_enabled = 		 isset($_POST['id_enabled']) 		? 1 : 0;
$track_customtrack = isset($_POST['track_customtrack']) ? 1 : 0;
$track_customarena = isset($_POST['track_customarena']) ? 1 : 0;
$track_texturehack = isset($_POST['track_texturehack']) ? 1 : 0;
$track_boost = 		 isset($_POST['track_boost'])		? 1 : 0;
$track_competition = isset($_POST['track_competition']) ? 1 : 0;
$track_change = 	 isset($_POST['track_change']) 		? 1 : 0;
$track_nintendo = 	 isset($_POST['track_nintendo']) 	? 1 : 0;

// Generate a unique track identifier
$track_unique = sha1(implode('', [
    $track_family, $track_clan, $prefix, $trackname, $track_version, 
    $track_version_extra, $track_author, $track_editor, $track_created, 
    $track_music, $track_prop, $track_wiki, $track_type, 
    $id_enabled, $track_customtrack, $track_customarena, $track_texturehack, 
    $track_boost, $track_competition, $track_change, $track_nintendo
]));

// Query additional track data from the database
$stmt = $conn->prepare('SELECT track_wiimm, track_sha1, track_sha3, track_wbz_size, track_szs_size, track_laps, track_speed, track_warn FROM tracks WHERE id_first=?');
$stmt->bind_param('i', $id_first);
$stmt->execute();
$stmt->bind_result($track_wiimm, $track_sha1, $track_sha3, $track_wbz_size, $track_szs_size, $track_laps, $track_speed, $track_warn);
$stmt->fetch();
$stmt->close();

// Default track_wiimm if empty
if (empty($track_wiimm)) {
    $track_wiimm = $id_first;
}

// Disable all other revisions
$stmt = $conn->prepare('UPDATE tracks SET id_enabled = NULL WHERE id_first=?');
$stmt->bind_param('i', $id_first);
$stmt->execute();
$stmt->close();

// Insert new track data

	$sql = 'INSERT INTO tracks (id_enabled, id_first, track_wiimm, track_family, track_clan, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_created, track_music, track_prop, track_laps, track_speed, track_wbz_size, track_szs_size, track_wiki, track_type, track_sha1, track_sha3, track_warn, track_customtrack, track_customarena, track_texturehack, track_boost, track_competition, track_change, track_nintendo, last_mod, track_unique) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$sql .= ' ON DUPLICATE KEY UPDATE id_enabled=1';
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
	}

	$stmt->bind_param('iiiiisssssssiiiiiiiissiiiiiiiiss', 
		$id_enabled, $id_first, $track_wiimm, $track_family, $track_clan, $prefix, 
		$trackname, $track_version, $track_version_extra, $track_author, $track_editor, 
		$track_created, $track_music, $track_prop, $track_laps, $track_speed, 
		$track_wbz_size, $track_szs_size, $track_wiki, $track_type, $track_sha1, 
		$track_sha3, $track_warn, $track_customtrack, $track_customarena, 
		$track_texturehack, $track_boost, $track_competition, $track_change, $track_nintendo,
		$username, $track_unique
	);

// Execute the statement
if (!$stmt->execute()) {
    die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
}

$stmt->close();
$conn->close();

// Redirect after successful operation
echo '<meta http-equiv="refresh" content="0; url=http://archive.tock.eu/">';

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