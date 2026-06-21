<?php
// Include required setup file
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

$time_start = microtime(true);

// Path to search for files
$files = glob($u8_dir . '/*/*.u8');

// Check if we have any files to process
if (!$files) {
    echo "No files found in the specified directory.<br>";
    exit;
}

foreach ($files as $file) {
    // Use the basename of the file as the id_first
    $id_first = pathinfo(basename($file), PATHINFO_FILENAME); // Remove file extension

    // Command to execute for the specific file
    $command = '/usr/local/bin/wszst check --brief ' . escapeshellarg($file);

    // Debugging: Output the command being executed
    echo "Executing command for $id_first: <pre>$command</pre>\n";

    // Use exec to capture output
    $output = [];
    exec($command, $output); // No '2>&1' here, only capturing stdout

    // Display output and return status for debugging
    echo "Command output for $file:<br><pre>" . implode("\n", $output) . "</pre>\n";

    // Determine the directory and file path for the output
    $last_two_chars = substr($id_first, -2); // Extract last two characters
    $output_dir = "/var/www/vhosts/szslibrary.com/httpdocs/library/check/$last_two_chars";

    // Create the directory if it doesn't exist
    if (!is_dir($output_dir)) {
        if (!mkdir($output_dir, 0755, true)) {
            echo "Failed to create directory: $output_dir<br>";
            continue;
        }
    }

    $output_file = "$output_dir/$id_first.txt";

    // Write output to the file
    $file_content = implode("\n", $output);
    if (file_put_contents($output_file, $file_content) === false) {
        echo "Failed to write to file: $output_file<br>";
    } else {
        echo "Output successfully written to $output_file<br>";
    }
}

$time_end = microtime(true);

// Calculate execution time
$execution_time = ($time_end - $time_start);
// Execution time of the script
echo '<b>Total Execution Time:</b> ' . $execution_time . ' Seconds';
?>
