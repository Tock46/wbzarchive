<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
// Set a time limit of 100 seconds
set_time_limit(100);

// Find all .wbz files in the input directory
$files = glob($wbz_dir . '*/*.wbz');

// Initialize counters and time tracking
$startTime = time();
$time_start = microtime(true);
$maxExecutionTime = 100;

// Loop through each .wbz file
foreach ($files as $wbzFile) {
    // Extract the relative path and output .u8 file
    $relativePath = str_replace($wbz_dir, '', $wbzFile);
    $u8File = $u8_dir . str_replace('.wbz', '.u8', $relativePath);

    // Check if the output .u8 file already exists
    if (!file_exists($u8File)) {
        // Command to execute
        $command = "/usr/local/bin/wszst decompress --u8 $wbzFile -d $u8File";

        // Debugging: Output the command being executed
        echo "Executing command: <pre>$command</pre><br>\n";

        // Use exec to capture both output and error
        $output = [];
        $return_var = 0;
        exec($command . ' 2>&1', $output, $return_var);

        // Display output and return status for debugging
        echo "Command output:<br><pre>" . implode("\n", $output) . "</pre><br>\n";
        echo "Return status: $return_var<br>\n";

        // Check if the command failed
        if ($return_var !== 0) {
            echo "Command execution failed for $wbzFile.<br>\n";
        } else {
            echo "Command executed successfully for $wbzFile.<br>\n";
        }

        // Check if the time limit has been reached
        if (time() - $startTime >= $maxExecutionTime) {
            echo "Time limit of $maxExecutionTime seconds reached, stopping execution.<br>\n";
            break;
        }
    } else {
        // echo "Output file $u8File already exists, skipping.<br>\n";
    }
}
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
?>
