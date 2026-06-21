<?php
// Include database connection (this should contain your MySQLi connection setup)
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

$time_start = microtime(true);

// Path to search for files
$files = glob($u8_dir . '/*/*.u8');

// Check if we have any files to process
if (!$files) {
    echo "No files found in the specified directory.<br>";
    exit;
}

// Prepare a statement to check if id_first exists in the database
$check_stmt = $conn->prepare("SELECT 1 FROM warn_table WHERE id_first = ? LIMIT 1");
if (!$check_stmt) {
    echo "Failed to prepare check statement: " . $conn->error . "<br>";
    exit;
}

// Prepare batch insert statement data
$batch_data = [];
$batch_size = 200;  // Number of rows per batch to insert

foreach ($files as $file) {
    // Use the basename of the file as the id_first
    $id_first = basename($file);

    // Check if the entry already exists
    $check_stmt->bind_param('s', $id_first);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        continue;  // Skip this file as it's already in the database
    }

    // Command to execute for the specific file
    $command = '/usr/local/bin/wszst check --brief ' . escapeshellarg($file);

    // Debugging: Output the command being executed
    echo "Executing command for $id_first: <pre>$command</pre>\n";

    // Use exec to capture both output and error
    $output = [];
    exec($command, $output);  // No '2>&1' here, only capturing stdout
	
    // Display output and return status for debugging
    echo "Command output for $file:<br><pre>" . implode("\n", $output) . "</pre>\n";

    // Initialize variable for warnings
    $track_warn = 0;

    // Loop through each line of the output and parse the warning count
    foreach ($output as $line) {
        // Debugging: Output each line to ensure it's being processed
        echo "Processing line: $line<br>";

        // Use regex to extract the warning count (number after '=>')
        if (preg_match('/=>\s+(\d+)\s+warnings?/', $line, $matches)) {
            // Extracted warning count
            $track_warn = intval($matches[1]);
            echo "Warning count found: $track_warn<br>"; // Debugging: Output the captured warning count
        }
    }

    // Debugging: Output final track_warn before inserting
    echo "Final warning count for $id_first: $track_warn<br>";

    // Accumulate batch data for insertion (only id_first and track_warn)
    $batch_data[] = "('" . $conn->real_escape_string($id_first) . "', " . $track_warn . ")";

    // Insert in batches of $batch_size
    if (count($batch_data) >= $batch_size) {
        $insert_query = "INSERT INTO warn_table (id_first, track_warn) VALUES " . implode(", ", $batch_data) . " ON DUPLICATE KEY UPDATE track_warn = VALUES(track_warn)";
        if ($conn->query($insert_query)) {
            echo "Successfully inserted/updated batch of records.<br>";
        } else {
            echo "Batch insert failed: " . $conn->error . "\n";
        }
        // Clear the batch after insertion
        $batch_data = [];
    }
}

// Insert any remaining entries
if (!empty($batch_data)) {
    $insert_query = "INSERT INTO warn_table (id_first, track_warn) VALUES " . implode(", ", $batch_data) . " ON DUPLICATE KEY UPDATE track_warn = VALUES(track_warn)";
    if ($conn->query($insert_query)) {
        echo "Successfully inserted/updated the remaining records.\n";
    } else {
        echo "Batch insert failed for the remaining records: " . $conn->error . "\n";
    }
}

// Close the prepared statement
$check_stmt->close();

// Close the MySQLi connection
$conn->close();

$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start);
//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
// if you get weird results, use number_format((float) $execution_time, 10) 
?>
