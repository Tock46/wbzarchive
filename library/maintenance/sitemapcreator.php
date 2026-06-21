<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

// Define the string to prepend
$prependString = "https://szslibrary.com/track.php?id=";

// Query to get all IDs from the table
$sql = "SELECT id_first FROM combined";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Define the file path one directory up
    $filePath = dirname(__DIR__) . "/sitemap.txt";

    // Open a file to write the output
    $file = fopen($filePath, "w");

    // Fetch and process each row
    while($row = $result->fetch_assoc()) {
        $outputLine = $prependString . $row['id_first'] . "\n";
        fwrite($file, $outputLine);
    }

    // Close the file
    fclose($file);

    echo "Output has been written to output.txt";
} else {
    echo "0 results found";
}

// Close the database connection
$conn->close();
?>