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
echo "test";
// Redirect if any required POST data is missing
$requiredFields = [
    'trackname', 'track_version', 'track_author', 'track_family', 'track_clan', 
    'track_created', 'track_music', 'track_prop',
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
    echo '<meta http-equiv="refresh" content="0; url=/../">';
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

$sql3 = "
    UPDATE tracks
    JOIN (
        SELECT r1.*
        FROM revision r1
        JOIN (
            SELECT id_first, MAX(id) AS max_id
            FROM revision
            GROUP BY id_first
        ) r2 ON r1.id_first = r2.id_first AND r1.id = r2.max_id
    ) AS latest_revision ON tracks.id_first = latest_revision.id_first
    SET
        tracks.id_enabled = latest_revision.id_enabled,
        tracks.prefix = latest_revision.prefix,
        tracks.trackname = latest_revision.trackname,
        tracks.track_version = latest_revision.track_version,
        tracks.track_version_extra = latest_revision.track_version_extra,
        tracks.track_author = latest_revision.track_author,
        tracks.track_editor = latest_revision.track_editor,
        tracks.track_family = latest_revision.track_family,
        tracks.track_clan = latest_revision.track_clan,
        tracks.track_created = latest_revision.track_created,
        tracks.track_customtrack = latest_revision.track_customtrack,
        tracks.track_customarena = latest_revision.track_customarena,
        tracks.track_competition = latest_revision.track_competition,
        tracks.track_texturehack = latest_revision.track_texturehack,
        tracks.track_boost = latest_revision.track_boost,
        tracks.track_change = latest_revision.track_change,
        tracks.track_nintendo = latest_revision.track_nintendo,
        tracks.track_music = latest_revision.track_music,
        tracks.track_prop = latest_revision.track_prop
";
$result4 = $conn->query($sql3);

$stmt->close();
$conn->close();

// Redirect after successful operation
    echo "<meta http-equiv='refresh' content='0; url=https://szslibrary.com/track.php?id={$id_first}'>";

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