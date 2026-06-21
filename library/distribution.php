<?php
session_start();
require __DIR__ . '/../included/header.php';
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
?> 
<!doctype html>

<html>
<head>
    <meta charset="UTF-8" />
    <title>SZS Library - Distributions</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="./style.css" />
	
<!-- jQuery -->
<script src="/js/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="/js/jquery.dataTables.min.js"></script>
<!-- Buttons extension JS -->
<script src="/js/dataTables.buttons.min.js"></script>
<script src="/js/buttons.colVis.min.js"></script>
<!-- Required for export buttons -->
<script src="/js/buttons.html5.min.js"></script>
<script src="/js/jszip.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<!-- Buttons extension CSS -->
<link rel="stylesheet" href="/css/buttons.dataTables.min.css" />
<!-- Responsive extension JS + CSS -->
<script src="/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="/css/responsive.dataTables.min.css" />

 <?php

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: /');
    exit;
}
$disttype = (int) $_GET['id'];

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
$result1 = $conn->prepare('SELECT dist_name, dist_version, dist_author, dist_release, dist_pre, dist_suc, dist_region, dist_url, dist_info FROM Distribution WHERE dist_id=?');
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$result1->bind_param('i', $disttype);
	$result1->execute();
	// Store the result so we can check if the account exists in the database.
	$result1->store_result();
	$result1->bind_result($dist_name, $dist_version, $dist_author, $dist_release, $dist_pre, $dist_suc, $dist_region, $dist_url, $dist_info);
	$result1->fetch();
if ($result1->num_rows === 0) {
    header('Location: /');
    exit;
}
?> 
</head>

<body>
<?php 
require __DIR__ . '/../included/topbar.php'; // Top Login Button
?> 

<div id="container">
	<a href='/'><h1>SZS Library</a> - <a href='/?type=distribution'>Distributions</a></h1>

		<div class="track_info">
			<h1><?php echo("{$dist_name}");?></h1>
			<div class="information">
				<div id="trackinfo"><table style="margin-top: 0px;">
				<tbody>
				<tr><td style="width: 160px">Version</td><td><?= e($dist_version)?></td></tr>
				<tr><td>Author</td><td><?= e($dist_author)?></td></tr>
				<tr><td>Release Date</td><td><?= e($dist_release)?></td></tr>
				<tr><td>Predecessor</td><td><?= e($dist_pre)?></td></tr>
				<tr><td>Successor</td><td><?= e($dist_suc)?></td></tr>
				<tr><td>Wiimmfi Region</td><td><?= e($dist_region)?></td></tr>
				<tr><td>Website</td><td><a href="<?php echo htmlspecialchars($dist_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($dist_url, ENT_QUOTES, 'UTF-8'); ?></a></td></tr>
				<tr><td>Additional Information</td><td><?= e($dist_info)?></td></tr>
				<?php if (isset($_SESSION['loggedin'])): ?>
                <tr><td colspan="2"><a href="editdistribution.php?id=<?= (int)$disttype ?>">Edit</a></td></tr>
                <?php endif; ?>
				<?php if (isset($_SESSION['loggedin'])): ?>
                <tr><td colspan="2"><a href="submittracklist.php?id=<?= (int)$disttype ?>">Replace Tracklist</a></td></tr>
                <?php endif; ?>
				</tbody>
				</table>
				</div>
			</div></div>
<?php
$sql = "
    SELECT 
        dt.dis_sha1, dt.dis_slot, dt.dis_cup,
        t.track_wiimm, t.prefix, t.trackname, t.track_version, t.track_version_extra, 
        t.track_author, t.track_editor
    FROM Distribution_tracks dt
    LEFT JOIN hash_table h ON dt.dis_sha1 = h.track_sha1
    LEFT JOIN tracks t ON h.id_first = t.id_first
    WHERE dt.dis_id = ?
    ORDER BY dt.dis_slot, dt.dis_cup
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $disttype);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 style="margin-top:10px; padding:5px; margin-bottom:1px;">
    Tracks in this Distribution:
</h2>

<table id="tracksTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Trackname</th>
            <th>Version</th>
            <th>Author</th>
            <th>SHA1</th>
            <th>Slot</th>
            <th>Cup</th>
        </tr>
    </thead>
    <tbody>

<?php
$banana = -1;
while ($row = $result->fetch_assoc()) {
    $banana++;

    // Insert repeated header every 4 rows
    if ($banana == 4) {
        echo "
            <tr class='repeat-header'>
                <th>Trackname</th>
                <th>Version</th>
                <th>Author</th>
                <th>SHA1</th>
                <th>Slot</th>
                <th>Cup</th>
            </tr>";
        $banana = 0;
    }

    // Handle null trackname
    if ($row['trackname'] === NULL) {
        $display_trackname = htmlspecialchars($row['dis_sha1']);
        $display_version   = "";
        $display_author    = "";
    } else {
        $prefix   = htmlspecialchars($row['prefix'] ?? '');
        $tn       = htmlspecialchars($row['trackname']);
        $wiimm_id = htmlspecialchars($row['track_wiimm']);

        $display_trackname = "<a href='track.php?id={$wiimm_id}'>{$prefix} {$tn}</a>";

        $display_version = htmlspecialchars($row['track_version']);
        if (!empty($row['track_version_extra'])) {
            $display_version .= "-" . htmlspecialchars($row['track_version_extra']);
        }

        $author     = htmlspecialchars($row['track_author']);
        $editor     = htmlspecialchars($row['track_editor'] ?? '');
        $authorLink = "./index.php?s=" . urlencode($row['track_author']);

        $display_author = "<a href='{$authorLink}'>{$author}";
        if (!empty($editor)) {
            $display_author .= ", {$editor}";
        }
        $display_author .= "</a>";
    }

    echo "<tr>
        <td>{$display_trackname}</td>
        <td>{$display_version}</td>
        <td>{$display_author}</td>
        <td><a title='" . htmlspecialchars($row['dis_sha1']) . "'>hover</a></td>
        <td>" . htmlspecialchars($row['dis_slot']) . "</td>
        <td>" . htmlspecialchars($row['dis_cup']) . "</td>
    </tr>";
}
?>

    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#tracksTable').DataTable({
        processing: true,
        responsive: true,
        searching: false,
        paging: false,
        "order": [],

        // Tell DataTables to ignore the repeated header rows
        "rowCallback": function(row, data) {
            if ($(row).hasClass("repeat-header")) {
                $(row).addClass("dt-no-sort");
            }
        },

        // Disable sorting/searching for repeated header rows
        "createdRow": function(row, data, dataIndex) {
            if ($(row).hasClass("repeat-header")) {
                $('td', row).each(function() {
                    $(this).attr('colspan', 1);
                });
            }
        }
    });

    // Prevent sorting of repeated-header rows
    $('#tracksTable').on('click', 'tr.repeat-header th', function (e) {
        e.stopPropagation();
    });
});
</script>

</div>
<?php 
require '/var/www/vhosts/szslibrary.com/httpdocs/included/footer.php'; // Bottom Footer
?> 
</body>
</html>
