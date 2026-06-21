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


if (!isset($_SESSION['loggedin'])) {
	echo ('<meta http-equiv="refresh" content="0; url=/../">');
	exit();
}
$id = 0;
$sql3 = "DELETE FROM `wiki_curid`";
$result4 = $conn->query($sql3);
$time_start = microtime(true);

$json = file_get_contents('https://wiki.tockdom.com/info-w/wbz-id.json');

if ($json === false) {
    echo "Failed to fetch data.\n";
} elseif (empty($json)) {
    echo "Request succeeded, but response is empty.\n";
} else {
    echo "Successfully fetched data.\n";
}

$data = json_decode($json, true);

$stmt = $conn->prepare("INSERT INTO wiki_curid (page_id, wbz_id) VALUES (?,?)");
$stmt->bind_param("ii", $page_id, $wbz_id);

// Insert data
foreach ($data['page_list'] as $key => $entry) {
	
    $page_id = $entry['page_id'];
    $wbz_id = $entry['wbzid_list']['0'] ?? NULL;
    $stmt->execute();
}

// Close connections
$stmt->close();
$conn->close();

$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
// if you get weird results, use number_format((float) $execution_time, 10) 

?>
</div>
</body>
</html>

