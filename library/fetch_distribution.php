<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

header('Content-Type: application/json; charset=UTF-8');

// --- Get DataTables params safely ---
$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$sha1   = $_POST['sha1'] ?? '';

if (!$sha1) {
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
    exit();
}

// --- Total rows count ---
$count_sql = "
    SELECT COUNT(DISTINCT d.dist_id)
    FROM Distribution_tracks dt
    JOIN Distribution d ON dt.dis_id = d.dist_id
    WHERE dt.dis_sha1 = ?
";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $sha1);
$count_stmt->execute();
$count_stmt->bind_result($totalRows);
$count_stmt->fetch();
$count_stmt->close();

// --- Paginated data ---
$data_sql = "
    SELECT DISTINCT 
        d.dist_id,
        d.dist_name,
        d.dist_version,
        d.dist_author,
        d.dist_release
    FROM Distribution_tracks dt
    JOIN Distribution d ON dt.dis_id = d.dist_id
    WHERE dt.dis_sha1 = ?
    ORDER BY d.dist_name ASC
    LIMIT ?, ?
";
$data_stmt = $conn->prepare($data_sql);
$data_stmt->bind_param("sii", $sha1, $start, $length);
$data_stmt->execute();
$result = $data_stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $dist_id = intval($row['dist_id']);
    $data[] = [
        "dist_id"      => $dist_id,
        "dist_name"    => "<a href='distribution.php?id={$dist_id}'>" . htmlspecialchars($row['dist_name'] ?? '', ENT_QUOTES, 'UTF-8') . "</a>",
        "dist_version" => htmlspecialchars($row['dist_version'] ?? '', ENT_QUOTES, 'UTF-8'),
        "dist_author"  => htmlspecialchars($row['dist_author'] ?? '', ENT_QUOTES, 'UTF-8'),
        "dist_release" => htmlspecialchars($row['dist_release'] ?? '', ENT_QUOTES, 'UTF-8'),
        "edit"         => isset($_SESSION['loggedin']) 
                            ? "<a href='edit.php?id={$dist_id}'>Edit</a>"
                            : ''
    ];
}

$data_stmt->close();

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalRows),
    "recordsFiltered" => intval($totalRows),
    "data" => $data
], JSON_UNESCAPED_UNICODE);
