<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /../');
    exit();
}

$requiredFields = [
    'dist_name',
    'dist_version',
    'dist_author',
    'dist_release',
    'dist_pre',
    'dist_suc',
    'dist_region',
    'dist_url',
    'dist_info',
];

foreach ($requiredFields as $field) {
    if (!array_key_exists($field, $_POST)) {
        die('Missing required field: ' . htmlspecialchars($field));
    }
}

function cleanText($value) {
    return trim((string)$value);
}

function emptyToZero($value) {
    $value = trim((string)$value);
    return $value === '' ? '0' : $value;
}

$dist_name    = cleanText($_POST['dist_name']);
$dist_version = cleanText($_POST['dist_version']);
$dist_author  = cleanText($_POST['dist_author']);
$dist_release = cleanText($_POST['dist_release']);
$dist_pre     = emptyToZero($_POST['dist_pre']);
$dist_suc     = emptyToZero($_POST['dist_suc']);
$dist_region  = emptyToZero($_POST['dist_region']);
$dist_url     = cleanText($_POST['dist_url']);
$dist_info    = cleanText($_POST['dist_info']);

$dist_id = isset($_POST['dist_id']) ? (int)$_POST['dist_id'] : 0;

if ($dist_id <= 0) {
    $result = $conn->query('SELECT COALESCE(MAX(dist_id), 0) + 1 AS next_dist_id FROM Distribution');
    if (!$result) {
        die('Could not determine next Distribution ID: (' . $conn->errno . ') ' . $conn->error);
    }

    $row = $result->fetch_assoc();
    $dist_id = (int)$row['next_dist_id'];
}

$exists = false;
$checkStmt = $conn->prepare('SELECT 1 FROM Distribution WHERE dist_id = ? LIMIT 1');
if (!$checkStmt) {
    die('Preparation failed: (' . $conn->errno . ') ' . $conn->error);
}

$checkStmt->bind_param('i', $dist_id);
$checkStmt->execute();
$checkStmt->store_result();
$exists = $checkStmt->num_rows > 0;
$checkStmt->close();

if ($exists) {
    $sql = 'UPDATE Distribution
            SET dist_name = ?,
                dist_version = ?,
                dist_author = ?,
                dist_release = ?,
                dist_pre = ?,
                dist_suc = ?,
                dist_region = ?,
                dist_url = ?,
                dist_info = ?
            WHERE dist_id = ?';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Preparation failed: (' . $conn->errno . ') ' . $conn->error);
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
} else {
    $sql = 'INSERT INTO Distribution
            (dist_id, dist_name, dist_version, dist_author, dist_release, dist_pre, dist_suc, dist_region, dist_url, dist_info)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Preparation failed: (' . $conn->errno . ') ' . $conn->error);
    }

    $stmt->bind_param(
        'isssssssss',
        $dist_id,
        $dist_name,
        $dist_version,
        $dist_author,
        $dist_release,
        $dist_pre,
        $dist_suc,
        $dist_region,
        $dist_url,
        $dist_info
    );
}

if (!$stmt->execute()) {
    die('Execution failed: (' . $stmt->errno . ') ' . $stmt->error);
}

$stmt->close();
$conn->close();

header('Location: /../');
exit();
?>