<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

$files = glob($u8_dir . '/*/*.u8');

if ($files === false || count($files) === 0) {
    //error_log("No .u8 files found.", 3, '/path/to/log/file.log');
    die();
}

// Prepare SQL statements for checking if id_first exists
$checkHashSql = 'SELECT 1 FROM hash_table WHERE id_first = ?';
$checkHashStmt = $conn->prepare($checkHashSql);

if (!$checkHashStmt) {
    die('Failed to prepare check SQL statement.');
}

$insertHashSql = 'INSERT INTO hash_table (id_first, track_sha1, track_sha3) VALUES (?, ?, ?)';
$insertHashStmt = $conn->prepare($insertHashSql);

if (!$insertHashStmt) {
    die('Failed to prepare insert SQL statement.');
}

$hashValues = [];
$batchSize = 1000; // Adjust batch size

foreach ($files as $file) {
    $filename = basename($file, '.u8');
    
    if (preg_match('/^\d{5}$/', $filename) !== 1) {
        //error_log("Skipping invalid filename: $filename\n", 3, '/path/to/log/file.log');
        continue;
    }

    // Check if id_first already exists in hash_table
    $checkHashStmt->bind_param('s', $filename);
    $checkHashStmt->execute();
    $checkHashStmt->store_result();
    
    if ($checkHashStmt->num_rows > 0) {
        // id_first already exists in hash_table, skip this file
        //error_log("Skipping $filename: already exists in hash_table\n", 3, '/path/to/log/file.log');
        continue;
    }

    // Get the content of the file
    $fileContent = file_get_contents($file);
    if ($fileContent === false) {
        //error_log("Unable to read file: $file\n", 3, '/path/to/log/file.log');
        continue;
    }

    $sha1Hash = sha1($fileContent);
    $sha3Hash = hash('sha3-256', $fileContent);

    // Add to batch
    $hashValues[] = "('$filename', '$sha1Hash', '$sha3Hash')";

    if (count($hashValues) >= $batchSize) {
        // Insert batch into hash_table
        $insertSql = 'INSERT INTO hash_table (id_first, track_sha1, track_sha3) VALUES ' . implode(',', $hashValues);

        if (!$conn->query($insertSql)) {
            //error_log("Failed to insert batch into hash_table: " . $conn->error . "\n", 3, '/path/to/log/file.log');
        }

        // Clear array for the next batch
        $hashValues = [];
    }
}

// Insert any remaining records
if (count($hashValues) > 0) {
    $insertSql = 'INSERT INTO hash_table (id_first, track_sha1, track_sha3) VALUES ' . implode(',', $hashValues);

    if (!$conn->query($insertSql)) {
        //error_log("Failed to insert remaining batch into hash_table: " . $conn->error . "\n", 3, '/path/to/log/file.log');
    }
}

$checkHashStmt->close();
$insertHashStmt->close();
$conn->close();

echo "Hashing process complete.\n";
?>
