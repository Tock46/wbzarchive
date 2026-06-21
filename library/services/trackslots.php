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
$check_stmt = $conn->prepare("SELECT 1 FROM slot_table WHERE id_first = ? LIMIT 1");
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
        // echo "Skipping $id_first, already exists in the database.<br>";
        continue;  // Skip this file as it's already in the database
    }

    // Command to execute for the specific file
    $command = '/usr/local/bin/wszst slots ' . escapeshellarg($file);

    // Debugging: Output the command being executed
    echo "Executing command for $id_first: <pre>$command</pre>\n";

    // Use exec to capture both output and error
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);  // No '2>&1' here, only capturing stdout
	
    // Display output and return status for debugging
    echo "Command output for $file:<br><pre>" . implode("\n", $output) . "</pre>\n";
    echo "Return status: $return_var<br>";

    // Check if the command failed
    if ($return_var !== 0) {
        echo "Command execution failed for $id_first.\n";
        continue; // Skip to the next file
    }

    // Assuming the first line of the output contains the string we need
    $track_slot = implode("\n", $output);

    // Remove the last 65 characters from the track_slot string
    $track_slot_trimmed = substr($track_slot, 0, -65);

    // Accumulate batch data
    $batch_data[] = "('" . $conn->real_escape_string($id_first) . "', '" . $conn->real_escape_string($track_slot_trimmed) . "')";

    // Insert in batches of $batch_size
    if (count($batch_data) >= $batch_size) {
        $insert_query = "INSERT INTO slot_table (id_first, track_slot) VALUES " . implode(", ", $batch_data) . " ON DUPLICATE KEY UPDATE track_slot = VALUES(track_slot)";
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
    $insert_query = "INSERT INTO slot_table (id_first, track_slot) VALUES " . implode(", ", $batch_data) . " ON DUPLICATE KEY UPDATE track_slot = VALUES(track_slot)";
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
