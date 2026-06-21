<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!doctype html>
<html>
<head>
   <meta charset="UTF-8">
   <!--<link rel="shortcut icon" href="./.favicon.ico" <link rel="stylesheet" href="./style.css">>-->
   
   <title>WBZ Archive - Admin</title>
</head>

<body>

<?php

$id = 0;
$sql3 = "TRUNCATE TABLE translate";
$result4 = $conn->query($sql3);
$time_start = microtime(true);

$json = file_get_contents('https://wiki.tockdom.com/info-w/translations.json');
$data = json_decode($json, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
	echo ('<meta http-equiv="refresh" content="0; url=/../">');
	exit();
}

$stmt = $conn->prepare("INSERT INTO translate (page_id, page_name, tran_en, tran_nl, tran_fr_ntsc, tran_fr_pal, tran_de, tran_it, tran_jp, tran_kr, tran_pt_ntsc, tran_pt_pal, tran_ru, tran_es_ntsc, tran_es_pal, tran_gr, tran_pl, tran_fi, tran_sw, tran_cz, tran_dk, tran_us) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("isssssssssssssssssssss", $page_id, $page_name, $tran_en, $tran_nl, $tran_fr_ntsc, $tran_fr_pal, $tran_de, $tran_it, $tran_jp, $tran_kr, $tran_pt_ntsc, $tran_pt_pal, $tran_ru, $tran_es_ntsc, $tran_es_pal, $tran_gr, $tran_pl, $tran_fi, $tran_sw, $tran_cz, $tran_dk, $tran_us);

// Insert data
foreach ($data['translate'] as $key => $entry) {
	
    $page_id = $entry['page_id'];
    $page_name = $entry['page_name'];
    $tran_en = $entry['translate']['en'] ?? NULL;
    $tran_nl = $entry['translate']['nl'] ?? NULL;
    $tran_fr_ntsc = $entry['translate']['ca'] ?? NULL;
    $tran_fr_pal = $entry['translate']['fr'] ?? NULL;
    $tran_de = $entry['translate']['de'] ?? NULL;
    $tran_it = $entry['translate']['it'] ?? NULL;
    $tran_jp = $entry['translate']['ja'] ?? NULL;
    $tran_kr = $entry['translate']['ko'] ?? NULL;
    $tran_pt_ntsc = $entry['translate']['br'] ?? NULL;
    $tran_pt_pal = $entry['translate']['pt'] ?? NULL;
    $tran_ru = $entry['translate']['ru'] ?? NULL;
    $tran_es_ntsc = $entry['translate']['mx'] ?? NULL;
    $tran_es_pal = $entry['translate']['es'] ?? NULL;
    $tran_gr = $entry['translate']['el'] ?? NULL;
    $tran_pl = $entry['translate']['pl'] ?? NULL;
    $tran_fi = $entry['translate']['fi'] ?? NULL;
    $tran_sw = $entry['translate']['sv'] ?? NULL;
    $tran_cz = $entry['translate']['cs'] ?? NULL;
    $tran_dk = $entry['translate']['da'] ?? NULL;
    $tran_us = $entry['translate']['us'] ?? NULL;
    $stmt->execute();
}

// Close connections

$sql3 = "UPDATE translate SET tran_dk = NULL WHERE tran_dk = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_cz = NULL WHERE tran_cz = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_sw = NULL WHERE tran_sw = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_fi = NULL WHERE tran_fi = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_pl = NULL WHERE tran_pl = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_gr = NULL WHERE tran_gr = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_es_pal = NULL WHERE tran_es_pal = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_es_ntsc = NULL WHERE tran_es_ntsc = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_ru = NULL WHERE tran_ru = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_pt_pal = NULL WHERE tran_pt_pal = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_pt_ntsc = NULL WHERE tran_pt_ntsc = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_kr = NULL WHERE tran_kr = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_jp = NULL WHERE tran_jp = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_it = NULL WHERE tran_it = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_de = NULL WHERE tran_de = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_fr_pal = NULL WHERE tran_fr_pal = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_fr_ntsc = NULL WHERE tran_fr_ntsc = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_nl = NULL WHERE tran_nl = '-'";
$result4 = $conn->query($sql3);
$sql3 = "UPDATE translate SET tran_en = NULL WHERE tran_en = '-'";
$result4 = $conn->query($sql3);

$time_end = microtime(true);

$stmt->close();
$conn->close();

//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
// if you get weird results, use number_format((float) $execution_time, 10) 




?>
</div>
</body>
</html>

