<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) && !isset($_GET['sha1']) && !isset($_GET['dis']) && !isset($_GET['family']) && !isset($_GET['clan'])) {
    header('Location: /');
    exit;
}

$TrackOrDistribution = 0;
$tracktype = 0;
$dist_id = 0;
$family_id = 0;
$clan_id = 0;
$dist_id = 0;
$page_id = ''; // for translations
$sha1 = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$tracktype = intval($_GET['id']);
}

if (isset($_GET['sha1'])) {
    $sha1 = $_GET['sha1'];
}

if (isset($_GET['dis']) && is_numeric($_GET['dis'])) {
	$dist_id = intval($_GET['dis']);
}

if (isset($_GET['family']) && is_numeric($_GET['family'])) {
	$family_id = intval($_GET['family']);
}

if (isset($_GET['clan']) && is_numeric($_GET['clan'])) {
	$clan_id = intval($_GET['clan']);
}

if (!is_numeric($tracktype)) {
    header('Location: /');
    exit;
}

// Make Sha1 into a Track ID

if (isset($_GET['sha1'])) {
    // Assuming $conn is your database connection
    if (!isset($_GET['id'])) {
        // Prepare the statement
        $stmt1 = $conn->prepare('SELECT id_first FROM hash_table WHERE track_sha1 = ?');
        // Bind the parameter
        $stmt1->bind_param('s', $sha1);
        // Execute the statement
        $stmt1->execute();
        // Fetch the result
        $result1 = $stmt1->get_result();
        $data0 = $result1->fetch_all(MYSQLI_ASSOC);

        if (!empty($data0)) {
            $tracktype = $data0[0]['id_first'];
        }

        if ($tracktype == 0) {
            // Prepare the statement
            $stmt1 = $conn->prepare('SELECT id_first FROM sha1_hash WHERE track_sha1 = ?');
            // Bind the parameter
            $stmt1->bind_param('s', $sha1);
            // Execute the statement
            $stmt1->execute();
            // Fetch the result
            $result1 = $stmt1->get_result();
            $data10 = $result1->fetch_all(MYSQLI_ASSOC);
            if (!empty($data10)) {
                $tracktype = $data10[0]['id_first'];
            }
        }
		
		if ($tracktype == 0) {
		header('Location: /');
		exit;
		}
    }
}

if ($tracktype != 0) {
// -------------------------------------------------
// 1. Fetch track base information
// -------------------------------------------------
$stmt1 = $conn->prepare("
    SELECT 
        t.id_first,
        wc.page_id AS track_wiki,
        t.prefix,
        t.trackname,
        t.track_version,
        t.track_version_extra,
        t.track_author,
        t.track_editor,
        t.track_family,
        t.track_clan,
        ht.track_sha1,
        t.track_created,
        sz.track_wbz_size,
        wt.track_warn,
        sl.track_slot,
        t.track_prop,
        t.track_music,
        sg.track_speed,
        sg.track_laps,
        t.track_customtrack,
        t.track_customarena,
        t.track_texturehack,
        t.track_boost,
        t.track_competition,
        t.track_nintendo,
        t.track_change
    FROM tracks AS t
    LEFT JOIN hash_table AS ht ON t.id_first = ht.id_first
    LEFT JOIN size_table AS sz ON t.id_first = sz.id_first
    LEFT JOIN wiki_curid AS wc ON t.track_family = wc.wbz_id
    LEFT JOIN warn_table AS wt ON t.id_first = wt.id_first
    LEFT JOIN stgi_table AS sg ON t.id_first = sg.id_first
    LEFT JOIN slot_table AS sl ON t.id_first = sl.id_first
    WHERE t.id_enabled = 1
      AND t.id_first = ?
");
$stmt1->bind_param('i', $tracktype);
$stmt1->execute();
$result1 = $stmt1->get_result();
$data1 = $result1->fetch_all(MYSQLI_ASSOC);

if (!$data1) {
    echo json_encode(['error' => 'Track not found']);
    exit;
}

$track = $data1[0];
$track_clan = !empty($track['track_clan']) ? $track['track_clan'] : $track['id_first'];
$page_id = null;

// -------------------------------------------------
// 2. Try to resolve page_id from retros
// -------------------------------------------------
$stmt2 = $conn->prepare('SELECT curid FROM retros WHERE track_clan = ?');
$stmt2->bind_param('i', $track_clan);
$stmt2->execute();
$result2 = $stmt2->get_result();
if ($row = $result2->fetch_assoc()) {
    $page_id = $row['curid'];
}

// -------------------------------------------------
// 3. Fallback: wiki_curid lookup
// -------------------------------------------------
if ($page_id === null) {
    $stmt3 = $conn->prepare('SELECT page_id FROM wiki_curid WHERE wbz_id = ?');
    $stmt3->bind_param('i', $track_clan);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    if ($row = $result3->fetch_assoc()) {
        $page_id = $row['page_id'];
    }
}

// -------------------------------------------------
// 4. Final fallback: use track_wiki
// -------------------------------------------------
if ($page_id === null) {
    $page_id = $track['track_wiki'];
}

// -------------------------------------------------
// 5. Fetch translations if page_id available
// -------------------------------------------------
$translations = [];
if ($page_id !== null) {
    $stmt4 = $conn->prepare("
        SELECT 
            tran_en, tran_nl, tran_fr_ntsc, tran_fr_pal, tran_de, tran_it,
            tran_jp, tran_kr, tran_pt_ntsc, tran_pt_pal, tran_ru,
            tran_es_ntsc, tran_es_pal, tran_gr, tran_pl, tran_fi, tran_sw,
            tran_cz, tran_dk, tran_us
        FROM translate
        WHERE page_id = ?
    ");
    $stmt4->bind_param('i', $page_id);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $translations = $result4->fetch_all(MYSQLI_ASSOC);
}

// -------------------------------------------------
// 6. Build response
// -------------------------------------------------
$response = [
    'track_info'        => $track,
    'track_translation' => $translations
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

if ($dist_id != 0) {

$stmt1 = $conn->prepare("
    SELECT 
        dist_name,
        dist_version,
        dist_author,
        dist_release,
        dist_pre,
        dist_suc,
        dist_region,
        dist_url,
        dist_info
    FROM Distribution
    WHERE dist_id = ?
");
$stmt1->bind_param('i', $dist_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$distributionData = $result1->fetch_assoc();

if (!$distributionData) {
    echo json_encode(['error' => 'Distribution not found']);
    exit;
}

// ------------------------------------
// 2. Get Tracks inside Distribution
// ------------------------------------
$stmt2 = $conn->prepare("
    SELECT 
        t.prefix,
        t.trackname,
        dt.dis_sha1,
        dt.dis_slot,
        dt.dis_cup,
        t.track_wiimm,
        t.track_version,
        t.track_version_extra,
        t.track_author,
        t.track_editor
    FROM Distribution_tracks dt
    LEFT JOIN hash_table h ON dt.dis_sha1 = h.track_sha1
    LEFT JOIN tracks t ON h.id_first = t.id_first
    WHERE dt.dis_id = ?
    ORDER BY dt.dis_slot, dt.dis_cup
");
$stmt2->bind_param('i', $dist_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$tracksData = $result2->fetch_all(MYSQLI_ASSOC);

// ------------------------------------
// 3. Combine & Return JSON
// ------------------------------------
$response = [
    'distribution' => $distributionData,
    'tracks'       => $tracksData
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

}

if ($family_id != 0) {
    // -------------------------------------------------
    // 1. Fetch all tracks in this family
    // -------------------------------------------------
    $stmt1 = $conn->prepare("
        SELECT 
            t.id_first,
            wc.page_id AS track_wiki,
            t.prefix,
            t.trackname,
            t.track_version,
            t.track_version_extra,
            t.track_author,
            t.track_editor,
            t.track_family,
            t.track_clan,
            ht.track_sha1,
            t.track_created,
            sz.track_wbz_size,
            wt.track_warn,
            sl.track_slot,
            t.track_prop,
            t.track_music,
            sg.track_speed,
            sg.track_laps,
            t.track_customtrack,
            t.track_customarena,
            t.track_texturehack,
            t.track_boost,
            t.track_competition,
            t.track_nintendo,
            t.track_change
        FROM tracks AS t
        LEFT JOIN hash_table AS ht ON t.id_first = ht.id_first
        LEFT JOIN size_table AS sz ON t.id_first = sz.id_first
        LEFT JOIN wiki_curid AS wc ON t.track_family = wc.wbz_id
        LEFT JOIN warn_table AS wt ON t.id_first = wt.id_first
        LEFT JOIN stgi_table AS sg ON t.id_first = sg.id_first
        LEFT JOIN slot_table AS sl ON t.id_first = sl.id_first
        WHERE t.id_enabled = 1
          AND t.track_family = ?
    ");
    $stmt1->bind_param('i', $family_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $tracks = $result1->fetch_all(MYSQLI_ASSOC);

    if (!$tracks) {
        echo json_encode(['error' => 'Track family not found']);
        exit;
    }

    // -------------------------------------------------
    // 2. Determine track_clan from the first entry
    // (same for the whole family)
    // -------------------------------------------------
    $track_clan = !empty($tracks[0]['track_clan']) ? $tracks[0]['track_clan'] : $tracks[0]['id_first'];
    $page_id = null;

    // -------------------------------------------------
    // 3. Try to resolve page_id from retros
    // -------------------------------------------------
    $stmt2 = $conn->prepare('SELECT curid FROM retros WHERE track_clan = ?');
    $stmt2->bind_param('i', $track_clan);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($row = $result2->fetch_assoc()) {
        $page_id = $row['curid'];
    }

    // -------------------------------------------------
    // 4. Fallback: wiki_curid lookup
    // -------------------------------------------------
    if ($page_id === null) {
        $stmt3 = $conn->prepare('SELECT page_id FROM wiki_curid WHERE wbz_id = ?');
        $stmt3->bind_param('i', $track_clan);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        if ($row = $result3->fetch_assoc()) {
            $page_id = $row['page_id'];
        }
    }

    // -------------------------------------------------
    // 5. Final fallback: use track_wiki of first track
    // -------------------------------------------------
    if ($page_id === null) {
        $page_id = $tracks[0]['track_wiki'];
    }

    // -------------------------------------------------
    // 6. Fetch translations if page_id available
    // -------------------------------------------------
    $translations = [];
    if ($page_id !== null) {
        $stmt4 = $conn->prepare("
            SELECT 
                tran_en, tran_nl, tran_fr_ntsc, tran_fr_pal, tran_de, tran_it,
                tran_jp, tran_kr, tran_pt_ntsc, tran_pt_pal, tran_ru,
                tran_es_ntsc, tran_es_pal, tran_gr, tran_pl, tran_fi, tran_sw,
                tran_cz, tran_dk, tran_us
            FROM translate
            WHERE page_id = ?
        ");
        $stmt4->bind_param('i', $page_id);
        $stmt4->execute();
        $result4 = $stmt4->get_result();
        $translations = $result4->fetch_all(MYSQLI_ASSOC);
    }

    // -------------------------------------------------
    // 7. Build response
    // -------------------------------------------------
    $response = [
        'tracks'            => $tracks,
        'track_translation' => $translations
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

if ($clan_id != 0) {
    // -------------------------------------------------
    // 1. Fetch all tracks in this family
    // -------------------------------------------------
    $stmt1 = $conn->prepare("
        SELECT 
            t.id_first,
            wc.page_id AS track_wiki,
            t.prefix,
            t.trackname,
            t.track_version,
            t.track_version_extra,
            t.track_author,
            t.track_editor,
            t.track_family,
            t.track_clan,
            ht.track_sha1,
            t.track_created,
            sz.track_wbz_size,
            wt.track_warn,
            sl.track_slot,
            t.track_prop,
            t.track_music,
            sg.track_speed,
            sg.track_laps,
            t.track_customtrack,
            t.track_customarena,
            t.track_texturehack,
            t.track_boost,
            t.track_competition,
            t.track_nintendo,
            t.track_change
        FROM tracks AS t
        LEFT JOIN hash_table AS ht ON t.id_first = ht.id_first
        LEFT JOIN size_table AS sz ON t.id_first = sz.id_first
        LEFT JOIN wiki_curid AS wc ON t.track_family = wc.wbz_id
        LEFT JOIN warn_table AS wt ON t.id_first = wt.id_first
        LEFT JOIN stgi_table AS sg ON t.id_first = sg.id_first
        LEFT JOIN slot_table AS sl ON t.id_first = sl.id_first
        WHERE t.id_enabled = 1
          AND t.track_clan = ?
    ");
    $stmt1->bind_param('i', $clan_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $tracks = $result1->fetch_all(MYSQLI_ASSOC);

    if (!$tracks) {
        echo json_encode(['error' => 'Track family not found']);
        exit;
    }

    // -------------------------------------------------
    // 2. Determine track_clan from the first entry
    // (same for the whole family)
    // -------------------------------------------------
    $track_clan = !empty($tracks[0]['track_clan']) ? $tracks[0]['track_clan'] : $tracks[0]['id_first'];
    $page_id = null;

    // -------------------------------------------------
    // 3. Try to resolve page_id from retros
    // -------------------------------------------------
    $stmt2 = $conn->prepare('SELECT curid FROM retros WHERE track_clan = ?');
    $stmt2->bind_param('i', $track_clan);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($row = $result2->fetch_assoc()) {
        $page_id = $row['curid'];
    }

    // -------------------------------------------------
    // 4. Fallback: wiki_curid lookup
    // -------------------------------------------------
    if ($page_id === null) {
        $stmt3 = $conn->prepare('SELECT page_id FROM wiki_curid WHERE wbz_id = ?');
        $stmt3->bind_param('i', $track_clan);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        if ($row = $result3->fetch_assoc()) {
            $page_id = $row['page_id'];
        }
    }

    // -------------------------------------------------
    // 5. Final fallback: use track_wiki of first track
    // -------------------------------------------------
    if ($page_id === null) {
        $page_id = $tracks[0]['track_wiki'];
    }

    // -------------------------------------------------
    // 6. Fetch translations if page_id available
    // -------------------------------------------------
    $translations = [];
    if ($page_id !== null) {
        $stmt4 = $conn->prepare("
            SELECT 
                tran_en, tran_nl, tran_fr_ntsc, tran_fr_pal, tran_de, tran_it,
                tran_jp, tran_kr, tran_pt_ntsc, tran_pt_pal, tran_ru,
                tran_es_ntsc, tran_es_pal, tran_gr, tran_pl, tran_fi, tran_sw,
                tran_cz, tran_dk, tran_us
            FROM translate
            WHERE page_id = ?
        ");
        $stmt4->bind_param('i', $page_id);
        $stmt4->execute();
        $result4 = $stmt4->get_result();
        $translations = $result4->fetch_all(MYSQLI_ASSOC);
    }

    // -------------------------------------------------
    // 7. Build response
    // -------------------------------------------------
    $response = [
        'tracks'            => $tracks,
        'track_translation' => $translations
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

?>