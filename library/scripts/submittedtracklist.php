<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /../');
    exit();
}

$tracktype = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($tracktype <= 0) {
    header('Location: /../');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /../');
    exit();
}

if (!isset($_FILES['tracklist'])) {
    header('Location: /../');
    exit();
}

$file = $_FILES['tracklist'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Location: /../');
    exit();
}

// Basic upload validation.
$maxBytes = 2 * 1024 * 1024; // 2 MB; adjust if your real lists are larger.
if ($file['size'] <= 0 || $file['size'] > $maxBytes) {
    header('Location: /../');
    exit();
}

$originalName = $file['name'] ?? '';
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if ($extension !== 'txt') {
    header('Location: /../');
    exit();
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowedMimes = ['text/plain', 'text/x-asm', 'application/octet-stream'];
if (!in_array($mime, $allowedMimes, true)) {
    header('Location: /../');
    exit();
}

$contents = file_get_contents($file['tmp_name']);
if ($contents === false || trim($contents) === '') {
    header('Location: /../');
    exit();
}

// Reject binary-looking files.
if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $contents)) {
    header('Location: /../');
    exit();
}

/**
 * Parse Wiimm distribution TXT rows.
 *
 * Supported formats:
 *
 * 1) ct.wiimm.de distribution list/export rows:
 *      vs ---- ------ SHA1 --    1.1  Name
 *      vs ---- ------ SHA1 256   9.1  Name
 *
 * 2) [TRACK-LIST] "new" format rows:
 *      vs --------- -o---- SHA1 8 1.1 Name
 *
 * 3) [TRACK-LIST] "old" format rows:
 *      SHA1 1.1  Name
 *      SHA1 A1.1 Name
 *
 * Captured fields:
 *   dis_sha1 = SHA1 column
 *   dis_slot = slot column, or 0 when the file uses "--" or old format has no slot
 *   dis_cup  = Cup column, kept as string because values are like "1.1" or "A1.1"
 */
function parseDistributionTrackList(string $contents): array
{
    $rows = [];
    $lineNumber = 0;
    $invalidDataLines = [];
    $insideTrackList = false;
    $sawTrackListHeader = false;

    $lines = preg_split('/\R/', $contents);
    foreach ($lines as $line) {
        $lineNumber++;
        $trimmed = trim($line);

        if ($trimmed === '') {
            continue;
        }

        if (strcasecmp($trimmed, '[TRACK-LIST]') === 0) {
            $insideTrackList = true;
            $sawTrackListHeader = true;
            continue;
        }

        // Skip comments and metadata/section headers. For DISTRIB files, parse only after [TRACK-LIST].
        if ($trimmed[0] === '#' || $trimmed[0] === '@' || preg_match('/^\[[^\]]+\]$/', $trimmed)) {
            continue;
        }
        if ($sawTrackListHeader && !$insideTrackList) {
            continue;
        }

        $row = null;

        // New/full format: TYPE DISTRIB_FLAGS LE_FLAGS SHA1 TRACK_SLOT CUP NAME
        // Also covers ct.wiimm.de exports like: vs ---- ------ SHA1 -- CUP NAME
        if (preg_match('/^(?:vs|bt|ba|ar)\s+\S+\s+\S+\s+([a-f0-9]{40})\s+(--|-?\d+)\s+([Aa]?\d+\.\d+|-)\b/i', $trimmed, $matches)) {
            $slotText = $matches[2];
            $row = [
                'sha1' => strtolower($matches[1]),
                'slot' => ctype_digit(ltrim($slotText, '-')) && (int) $slotText >= 0 ? (int) $slotText : 0,
                'cup'  => strtoupper($matches[3][0]) === 'A' ? 'A' . substr($matches[3], 1) : $matches[3],
            ];
        }
        // Old [TRACK-LIST] format: SHA1 CUP NAME. There is no slot, so store 0.
        elseif (preg_match('/^([a-f0-9]{40})\s+([Aa]?\d+\.\d+|-)\b/i', $trimmed, $matches)) {
            $row = [
                'sha1' => strtolower($matches[1]),
                'slot' => 0,
                'cup'  => strtoupper($matches[2][0]) === 'A' ? 'A' . substr($matches[2], 1) : $matches[2],
            ];
        }
        // A line that looks like a data row but does not match exactly should fail validation.
        elseif (preg_match('/^(?:vs|bt|ba|ar)\s+|^[a-f0-9]{40}\b/i', $trimmed)) {
            $invalidDataLines[] = $lineNumber;
            continue;
        }
        else {
            // Ignore explanatory text in distribution files.
            continue;
        }

        // Extra safety checks after parsing.
        if (!preg_match('/^[a-f0-9]{40}$/', $row['sha1'])) {
            $invalidDataLines[] = $lineNumber;
            continue;
        }
        if ($row['slot'] < 0 || $row['slot'] > 4095) {
            $invalidDataLines[] = $lineNumber;
            continue;
        }
        if (!preg_match('/^(?:A?\d+\.\d+|-)$/', $row['cup'])) {
            $invalidDataLines[] = $lineNumber;
            continue;
        }

        $rows[] = $row;
    }

    if ($invalidDataLines !== []) {
        throw new RuntimeException('Invalid data row format on line(s): ' . implode(', ', $invalidDataLines));
    }

    return $rows;
}

try {
    $rows = parseDistributionTrackList($contents);
} catch (RuntimeException $e) {
    header('Location: /../');
    exit();
}

if (count($rows) === 0) {
    header('Location: /../');
    exit();
}

// Optional sanity check: Wiimm distribution exports include a printed record count.
if (preg_match('/#\s*(\d+)\s+records printed/i', $contents, $countMatch)) {
    $expectedCount = (int) $countMatch[1];
    if ($expectedCount !== count($rows)) {
    header('Location: /../');
    exit();
    }
}

$conn->begin_transaction();

try {
    // Replace the existing imported rows for this distribution id.
    $delete = $conn->prepare('DELETE FROM Distribution_tracks WHERE dis_id = ?');
    if (!$delete) {
        throw new RuntimeException('Delete prepare failed: (' . $conn->errno . ') ' . $conn->error);
    }
    $delete->bind_param('i', $tracktype);
    if (!$delete->execute()) {
        throw new RuntimeException('Delete failed: (' . $delete->errno . ') ' . $delete->error);
    }
    $delete->close();

    $insert = $conn->prepare('INSERT INTO Distribution_tracks (dis_id, dis_sha1, dis_slot, dis_cup) VALUES (?, ?, ?, ?)');
    if (!$insert) {
        throw new RuntimeException('Insert prepare failed: (' . $conn->errno . ') ' . $conn->error);
    }

    foreach ($rows as $row) {
        $sha1 = $row['sha1'];
        $slot = $row['slot'];
        $cup = $row['cup'];

        // bind_param allows NULL for an integer variable; MySQLi sends it as SQL NULL.
        $insert->bind_param('isis', $tracktype, $sha1, $slot, $cup);
        if (!$insert->execute()) {
            throw new RuntimeException('Insert failed: (' . $insert->errno . ') ' . $insert->error);
        }
    }

    $insert->close();
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    die($e->getMessage());
}

$conn->close();
// Redirect after successful operation
    echo "<meta http-equiv='refresh' content='0; url=https://szslibrary.com/distribution.php?id={$tracktype}'>";
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <!--<link rel="shortcut icon" href="./.favicon.ico">-->
    <link rel="stylesheet" href="./style.css">
    <title>WBZ Archive - Admin</title>
</head>
<body>
</body>
</html>