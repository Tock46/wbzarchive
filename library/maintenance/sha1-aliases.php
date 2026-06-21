<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['loggedin'])) {
    echo ('<meta http-equiv="refresh" content="0; url=/../">');
    exit();
}

$time_start = microtime(true);

/* -------------------------------
   Prepare all statements
--------------------------------*/

// Check in hash_table
$checkHashTable = $conn->prepare(
    "SELECT 1 FROM hash_table WHERE track_sha1 = BINARY ? LIMIT 1"
);

// Check in sha1_hash
$checkSha1Hash = $conn->prepare(
    "SELECT 1 FROM sha1_hash WHERE track_sha1 = BINARY ? LIMIT 1"
);

// Insert into sha1_hash
$insert = $conn->prepare("
    INSERT INTO sha1_hash (track_sha1, id_first, track_family, track_clan, track_cflag)
    VALUES (?, ?, NULL, NULL, NULL)
");

/* -------------------------------
   Open the input file
--------------------------------*/
$handle = fopen("2.txt", "r");
if ($handle) {

    $isFirstLine = true;

    while (($line = fgets($handle)) !== false) {

        $line = trim($line);
        if ($line === "") continue;

        // Remove BOM from the first line if present
        if ($isFirstLine) {
            $line = preg_replace('/^\x{FEFF}/u', '', $line);
            $isFirstLine = false;
        }

        // Split by any whitespace
        $parts = explode(" ", $line);

        if (count($parts) < 2) {
            echo "Skipping malformed line: '{$line}'<br><hr>";
            continue;
        }

        $sha1 = trim($parts[0]);
        $filename = trim($parts[1]);

        // Extract numeric ID from filename
        $id_first = intval(pathinfo($filename, PATHINFO_FILENAME));

        echo "Processing SHA1: {$sha1} | ID: {$id_first}<br>";

        /* ------------------------------
           1. Check hash_table
        -------------------------------*/
        $checkHashTable->bind_param("s", $sha1);
        $checkHashTable->execute();
        $checkHashTable->store_result();
        if ($checkHashTable->num_rows > 0) {
            echo "Skipped: exists in hash_table<br><hr>";
            continue;
        }

        /* ------------------------------
           2. Check sha1_hash
        -------------------------------*/
        $checkSha1Hash->bind_param("s", $sha1);
        $checkSha1Hash->execute();
        $checkSha1Hash->store_result();
        if ($checkSha1Hash->num_rows > 0) {
            echo "Skipped: exists in sha1_hash<br><hr>";
            continue;
        }

        /* ------------------------------
           3. Insert into sha1_hash
        -------------------------------*/
        if ($insert->bind_param("si", $sha1, $id_first)) {
            if ($insert->execute()) {
                echo "Inserted successfully<br><hr>";
            } else {
                echo "Insert failed: " . $insert->error . "<br><hr>";
            }
        } else {
            echo "Bind failed: " . $insert->error . "<br><hr>";
        }
    }

    fclose($handle);
}

$time_end = microtime(true);
$execution_time = $time_end - $time_start;

echo "<b>Total Execution Time:</b> {$execution_time} Seconds";
?>
