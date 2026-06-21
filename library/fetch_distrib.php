<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');

$columns = ['dist_id','dist_name','dist_version','dist_author','dist_release'];

// Safe POST reads
$draw       = intval($_POST['draw'] ?? 0);
$start      = intval($_POST['start'] ?? 0);
$length     = intval($_POST['length'] ?? 10);
$searchValue= $_POST['search']['value'] ?? '';
$orderColumn= intval($_POST['order'][0]['column'] ?? 0);
$orderDir   = strtolower($_POST['order'][0]['dir'] ?? 'asc');
$orderDir   = in_array($orderDir, ['asc','desc']) ? $orderDir : 'asc';
$orderColumnName = $columns[$orderColumn] ?? 'dist_id';

$sqlBase = "FROM Distribution WHERE dist_id > 0";

$searchSql    = '';
$searchParams = [];

// --- Global Search ---
if (!empty($searchValue)) {
    $words = preg_split('/\s+/', trim($searchValue));
    $searchSqlParts = [];
    foreach ($words as $word) {
        $isNegative = false;
        if (substr($word,0,1) === '-' && strlen($word) > 1) {
            $isNegative = true;
            $word = substr($word,1);
        }
        if ($word === '') continue;

        $wordConditions = [];
        foreach (['dist_name','dist_version','dist_author','dist_release'] as $col) {
            $wordConditions[] = $isNegative ? "$col NOT LIKE ?" : "$col LIKE ?";
            $searchParams[] = "%$word%";
        }
        $connector = $isNegative ? ' AND ' : ' OR ';
        $searchSqlParts[] = '(' . implode($connector, $wordConditions) . ')';
    }
    if ($searchSqlParts) {
        $searchSql = ' AND ' . implode(' AND ', $searchSqlParts);
    }
}

// --- Total records ---
$stmt = $conn->prepare("SELECT COUNT(*) $sqlBase");
$stmt->execute();
$stmt->bind_result($recordsTotal);
$stmt->fetch();
$stmt->close();

// --- Total filtered records ---
if ($searchSql) {
    $stmt = $conn->prepare("SELECT COUNT(*) $sqlBase $searchSql");
    if ($searchParams) {
        $stmt->bind_param(str_repeat('s', count($searchParams)), ...$searchParams);
    }
    $stmt->execute();
    $stmt->bind_result($recordsFiltered);
    $stmt->fetch();
    $stmt->close();
} else {
    $recordsFiltered = $recordsTotal;
}

// --- Data ---
$sqlData = "SELECT dist_id, dist_name, dist_version, dist_author, dist_release
            $sqlBase $searchSql
            ORDER BY $orderColumnName $orderDir
            LIMIT ?, ?";

$params = $searchParams;
$params[] = $start;
$params[] = $length;

$stmt = $conn->prepare($sqlData);
$bindTypes = str_repeat('s', count($searchParams)) . 'ii';
$stmt->bind_param($bindTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $id = intval($row['dist_id'] ?? 0);
    $data[] = [
        'dist_id'      => $id,
        'dist_name'    => "<a href='distribution.php?id={$id}'>" . htmlspecialchars($row['dist_name'] ?? '') . "</a>",
        'dist_version' => htmlspecialchars($row['dist_version'] ?? ''),
        'dist_author'  => htmlspecialchars($row['dist_author'] ?? ''),
        'dist_release' => htmlspecialchars($row['dist_release'] ?? ''),
        'edit'         => isset($_SESSION['loggedin']) ? "<a href='edit.php?id={$id}'>Edit</a>" : ''
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data'            => $data
], JSON_UNESCAPED_UNICODE);
