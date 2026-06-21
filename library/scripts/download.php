<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

//if (!isset($_SESSION['loggedin'])) {
//  echo '<meta http-equiv="refresh" content="0; url=/../">';
//	exit();
//}

if (!isset($_GET['id'])){
    echo '<meta http-equiv="refresh" content="0; url=/../">';
    //echo "No track ID given.";
	exit();
}
$tracktype = (int) $_GET['id'];
if (!is_numeric($tracktype)){
    echo '<meta http-equiv="refresh" content="0; url=/../">';
    //echo "No number.";
    exit();
}

$stmt = $conn->prepare('SELECT id_enabled FROM tracks WHERE id_first=?');
$stmt->bind_param('i', $tracktype);
$stmt->execute();
$stmt->bind_result($track_enabled);
$stmt->fetch();
$stmt->close();

if ($track_enabled != 1){
    echo '<meta http-equiv="refresh" content="0; url=/../">';
    //echo "Not enabled.";
    exit();
}

$file = sprintf("../wbz/%02u/%05u.wbz",($tracktype%100),($tracktype));
if (file_exists($file)) {
	$Message="";
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	readfile($file);
	exit;
}
?>
