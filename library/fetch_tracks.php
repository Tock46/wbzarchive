<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');

$columns = [
    'id_first',
    'trackname',
    'track_version',
    'track_author',
    'track_type',
    'tracks.track_family',
    'tracks.track_clan',
    'track_size',
    'track_created',
    'tracks.track_sha1'
];

// Fallback-safe POST reads
$draw        = intval($_POST['draw'] ?? 0);
$start       = intval($_POST['start'] ?? 0);
$length      = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumn = intval($_POST['order'][0]['column'] ?? 0);
$orderDir    = strtolower($_POST['order'][0]['dir'] ?? 'asc');
$orderDir    = in_array($orderDir, ['asc','desc']) ? $orderDir : 'asc';
$orderColumnName = $columns[$orderColumn] ?? 'id_first';

// Base SQL
$sqlBase = "FROM tracks
            LEFT JOIN hash_table ON tracks.id_first = hash_table.id_first
            LEFT JOIN size_table ON tracks.id_first = size_table.id_first
            LEFT JOIN sha1_hash ON tracks.id_first = sha1_hash.id_first
            WHERE tracks.id_enabled = 1 AND tracks.id_first > 0";

$searchSql = '';
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

        // Special search syntax
        if (preg_match('/^@f(\d+)$/',$word,$m)) {
            $val = (int)$m[1];
            $wordConditions[] = $isNegative ? "tracks.track_family != ?" : "tracks.track_family = ?";
            $searchParams[] = $val;
        } elseif (preg_match('/^@c(\d+)$/',$word,$m)) {
            $val = (int)$m[1];
            $wordConditions[] = $isNegative ? "tracks.track_clan != ?" : "tracks.track_clan = ?";
            $searchParams[] = $val;
        } elseif (preg_match('/^@i(\d+)$/',$word,$m)) {
            $val = (int)$m[1];
            $wordConditions[] = $isNegative ? "tracks.id_first != ?" : "tracks.id_first = ?";
            $searchParams[] = $val;
        } elseif (preg_match('/^@a(.+)$/',$word,$m)) {
            $val = str_replace('+',' ',$m[1]);
            $wordConditions[] = $isNegative ? "track_author != ?" : "track_author = ?";
            $searchParams[] = $val;
        } elseif (preg_match('/^@e(.+)$/',$word,$m)) {
            $val = str_replace('+',' ',$m[1]);
            $wordConditions[] = $isNegative ? "track_editor != ?" : "track_editor = ?";
            $searchParams[] = $val;
        } else {
            // Regular LIKE
            foreach (['prefix','trackname','track_author','track_editor','track_version','track_version_extra','tracks.track_family','tracks.track_clan','track_created','hash_table.track_sha1','sha1_hash.track_sha1'] as $col) {
                $wordConditions[] = $isNegative ? "$col NOT LIKE ?" : "$col LIKE ?";
                $searchParams[] = "%$word%";
            }
        }

        $connector = $isNegative ? ' AND ' : ' OR ';
        $searchSqlParts[] = '(' . implode($connector, $wordConditions) . ')';
    }

    if ($searchSqlParts) $searchSql .= ' AND ' . implode(' AND ', $searchSqlParts);
}

// --- Per-column search ---
foreach ($columns as $i => $col) {
    $val = $_POST['columns'][$i]['search']['value'] ?? '';
    if ($val === '') continue;

    if (in_array($col,['tracks.track_clan','tracks.track_family'])) {
        $searchSql .= " AND $col = ?";
        $searchParams[] = (int)$val;
    } else {
        $searchSql .= " AND $col LIKE ?";
        $searchParams[] = "%$val%";
    }
}

// --- Additional filters ---
foreach (['clan','family','id'] as $key) {
    if (!empty($_POST[$key])) {
        $colMap = ['clan'=>'tracks.track_clan','family'=>'tracks.track_family','id'=>'tracks.id_first'];
        $searchSql .= " AND {$colMap[$key]} = ?";
        $searchParams[] = (int)$_POST[$key];
    }
}

// --- Total records ---
$stmt = $conn->prepare("SELECT COUNT(DISTINCT tracks.id_first) $sqlBase");
$stmt->execute();
$stmt->bind_result($recordsTotal);
$stmt->fetch();
$stmt->close();

// --- Total filtered records ---
if ($searchSql) {
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT tracks.id_first) $sqlBase $searchSql");
    if ($searchParams) $stmt->bind_param(str_repeat('s', count($searchParams)), ...$searchParams);
    $stmt->execute();
    $stmt->bind_result($recordsFiltered);
    $stmt->fetch();
    $stmt->close();
} else $recordsFiltered = $recordsTotal;

// --- Fetch data ---
$sqlData = "SELECT tracks.id_first, tracks.prefix, tracks.trackname,
                   tracks.track_version, tracks.track_version_extra,
                   tracks.track_author, tracks.track_editor,
                   tracks.track_family, tracks.track_clan, tracks.track_created,
                   hash_table.track_sha1 AS track_sha1,
                   size_table.track_wbz_size AS track_size,
                   tracks.track_customtrack, tracks.track_customarena, tracks.track_texturehack,
                   tracks.track_change, tracks.track_competition, tracks.track_nintendo,
                   GROUP_CONCAT(DISTINCT sha1_hash.track_sha1 SEPARATOR ',') AS sha1_aliases
            $sqlBase $searchSql
            GROUP BY tracks.id_first
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
    $version = $row['track_version'] ?? '';
    if (!empty($row['track_version_extra'])) $version .= '-' . $row['track_version_extra'];

    $trackName = trim(($row['prefix'] ?? '') . ' ' . ($row['trackname'] ?? ''));

    $type = '';
    if ($row['track_nintendo']) $type .= 'Original ';
    else $type .= 'Custom ';
    if ($row['track_texturehack']) $type .= 'Texture ';
    if ($row['track_customtrack']) $type .= 'Track ';
    if ($row['track_customarena']) $type .= 'Arena ';
    if ($row['track_change']) $type .= 'Hack ';
    if ($row['track_competition']) $type .= 'Competition ';

    $aliases = $row['sha1_aliases'] ?? '';
    $trackSha1 = $row['track_sha1'] ?? '';
    if (!empty($aliases)) {
        $aliasArray = array_diff(array_map('trim', explode(',',$aliases)), [$trackSha1]);
        $aliases = implode(', ', $aliasArray);
    }

    $data[] = [
        'id_first'      => (int)($row['id_first'] ?? 0),
        'trackname'     => "<a href='track.php?id=" . (int)$row['id_first'] . "'>" . htmlspecialchars($trackName) . "</a>",
        'track_version' => htmlspecialchars($version),
        'track_author'  => ($row['track_author'] || $row['track_editor']) 
                           ? "<a href='#' class='filter-author' data-author='" . htmlspecialchars($row['track_author'] ?? '') . "'>" 
                             . htmlspecialchars($row['track_author'] ?? '') 
                             . (!empty($row['track_editor']) ? ', ' . htmlspecialchars($row['track_editor']) : '') 
                             . "</a>" 
                           : '',
        'track_type'    => htmlspecialchars(trim($type)),
        'track_family'  => $row['track_family'] ? "<a href='#' class='filter-family' data-family='" . (int)$row['track_family'] . "'>" . (int)$row['track_family'] . "</a>" : '',
        'track_clan'    => $row['track_clan'] ? "<a href='#' class='filter-clan' data-clan='" . (int)$row['track_clan'] . "'>" . (int)$row['track_clan'] . "</a>" : '',
        'track_created' => htmlspecialchars($row['track_created'] ?? ''),
        'track_sha1'    => htmlspecialchars($trackSha1),
        'sha1_aliases'  => htmlspecialchars($aliases),
        'track_size'    => htmlspecialchars($row['track_size'] ?? '') . " Bytes",
        'edit'          => isset($_SESSION['loggedin']) ? "<a href='edit.php?id=" . (int)$row['id_first'] . "'>Edit</a>" : ''
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
