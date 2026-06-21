<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
/**
 * PHP Script to Convert PNG Images to JPEG with 80% Quality (No Report)
 * 
 * Features:
 * - Recursively scans the /thumbnail/ directory for PNG images.
 * - Converts each PNG to JPEG with 80% quality.
 * - Saves the JPEG in the /thumbnailjpg/ directory, maintaining the directory structure.
 * - Skips conversion if the JPEG already exists.
 */

// Check if the source directory exists
if (!is_dir($sourceDirThump)) {
    die("Source directory does not exist: $sourceDirThump\n");
}

// Create the destination directory if it doesn't exist
if (!is_dir($destinationDirThump)) {
    if (!mkdir($destinationDirThump, 0755, true)) {
        die("Failed to create destination directory: $destinationDirThump\n");
    }
}

/**
 * Recursively traverse the source directory and process PNG files.
 *
 * @param string $source Path to the source directory
 * @param string $destination Path to the destination directory
 */
function convertPngToJpeg($source, $destination)
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        // If the item is a directory, ensure the corresponding destination directory exists
        if ($item->isDir()) {
            $relativePath = substr($item->getPathname(), strlen($source));
            $destPath = $destination . $relativePath;
            if (!is_dir($destPath)) {
                if (!mkdir($destPath, 0755, true)) {
                    echo "Failed to create directory: $destPath\n";
                }
            }
            continue;
        }

        // Process only PNG files
        if (strtolower($item->getExtension()) === 'png') {
            $relativePath = substr($item->getPath(), strlen($source));
            $destPathDir = $destination . $relativePath;
            $sourceFile = $item->getPathname();
            $filename = pathinfo($item->getFilename(), PATHINFO_FILENAME);
            $destFile = $destPathDir . '/' . $filename . '.jpg';

            // Check if the destination JPEG already exists
            if (file_exists($destFile)) {
                echo "Skipped (already exists): $destFile\n<br>";
                continue;
            }

            // Load the PNG image
            $image = imagecreatefrompng($sourceFile);
            if (!$image) {
                echo "Failed to load image: $sourceFile\n";
                continue;
            }

            // Convert and save as JPEG with 80% quality
            if (imagejpeg($image, $destFile, 80)) {
                echo "Converted: $sourceFile -> $destFile\n";
            } else {
                echo "Failed to save JPEG: $destFile\n";
            }

            // Free up memory
            imagedestroy($image);
        }
    }
}

// Execute the conversion
convertPngToJpeg($sourceDirThump, $destinationDirThump);

echo "Conversion completed.\n";
