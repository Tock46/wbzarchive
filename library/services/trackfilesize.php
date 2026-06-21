<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

// Get all .u8 and .wbz files
$u8_files = glob($u8_dir . '/*/*.u8');
$wbz_files = glob($wbz_dir . '/*/*.wbz');

// Check if no .u8 or .wbz files are found
if (($u8_files === false || count($u8_files) === 0) && ($wbz_files === false || count($wbz_files) === 0)) {
    die("No .u8 or .wbz files found.");
}

// Prepare SQL statements for checking if id_first exists in size_table table
$checkSizeSql = 'SELECT 1 FROM size_table WHERE id_first = ?';
$checkSizeStmt = $conn->prepare($checkSizeSql);

if (!$checkSizeStmt) {
    die('Failed to prepare check SQL statement.');
}

// Prepare SQL statement for inserting file sizes into size_table table
$insertSizeSql = 'INSERT INTO size_table (id_first, track_szs_size, track_wbz_size) VALUES (?, ?, ?)';
$insertSizeStmt = $conn->prepare($insertSizeSql);

if (!$insertSizeStmt) {
    die('Failed to prepare insert SQL statement.');
}

// Create a map of .wbz files for easy lookup
$wbz_map = [];
foreach ($wbz_files as $wbz_file) {
    $wbz_filename = basename($wbz_file, '.wbz');
    $wbz_map[$wbz_filename] = $wbz_file;
}

// Process u8 files
foreach ($u8_files as $u8_file) {
    $filename = basename($u8_file, '.u8');

    // Validate filename
    if (preg_match('/^\d{5}$/', $filename) !== 1) {
        continue;
    }

    // Check if id_first already exists in size_table table
    $checkSizeStmt->bind_param('i', $filename);
    $checkSizeStmt->execute();
    $checkSizeStmt->store_result();

    if ($checkSizeStmt->num_rows > 0) {
        // id_first already exists in size_table, skip this file
        continue;
    }

    // Get file size of the .u8 file
    $u8_size = filesize($u8_file);
    $wbz_size = null;

    // Check if a corresponding .wbz file exists in the map
    if (isset($wbz_map[$filename])) {
        $wbz_size = filesize($wbz_map[$filename]);
    }

    // Insert file sizes into size_table table
    $insertSizeStmt->bind_param('iii', $filename, $u8_size, $wbz_size);
    if (!$insertSizeStmt->execute()) {
        // Log error if insertion fails
        //error_log("Failed to insert sizes for $filename: " . $conn->error . "\n", 3, '/path/to/log/file.log');
    }
}

// Process any remaining .wbz files that don't have corresponding .u8 files
foreach ($wbz_files as $wbz_file) {
    $filename = basename($wbz_file, '.wbz');  // The filename without extension

    // Check if id_first already exists in size_table table
    $checkSizeStmt->bind_param('i', $filename);
    $checkSizeStmt->execute();
    $checkSizeStmt->store_result();

    if ($checkSizeStmt->num_rows > 0) {
        // id_first already exists in size_table, skip this file
        continue;
    }

    // Get file size of the .wbz file
    $wbz_size = filesize($wbz_file);

    // Insert wbz file size with null for track_szs_size
    $insertSizeStmt->bind_param('iii', $filename, null, $wbz_size);
    if (!$insertSizeStmt->execute()) {
        // Log error if insertion fails
        //error_log("Failed to insert size for $filename: " . $conn->error . "\n", 3, '/path/to/log/file.log');
    }
}

// Close prepared statements and database connection
$checkSizeStmt->close();
$insertSizeStmt->close();
$conn->close();

echo "File size tracking process complete.\n";
?>
