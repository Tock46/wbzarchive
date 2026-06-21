<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?> 
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>SZS Library - Tracks</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="./style.css" />
<style>

/* Remove border from last row */
tr:last-child td {
    border-bottom: none;
}

/* Remove border from last column */
td:last-child {
    border-right: none;
}
td {
  border-top: 0px solid var(--main-bg);
  border-left: 0px solid var(--main-bg);
    border-bottom: 1px solid (--main-bg); /* Border between rows */
    border-right: 1px solid (--main-bg);  /* Border between columns */
}

table {
border-radius: 10px; 
border-collapse: separate; 
border-spacing: 0; 
overflow: hidden; 
border: 2px solid #FE4902;
}
</style>
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

if (!isset($_GET['id']) && !isset($_GET['sha1'])){
    header('Location: /');
	exit;
}

if (isset($_GET['id'])) {
    $tracktype = (int) $_GET['id'];
}

if (isset($_GET['sha1']) && !isset($_GET['id'])) {
        $sha1 = $_GET['sha1'];

        // Prepare the combined SQL statement
        $stmt = $conn->prepare('
            SELECT COALESCE(NULLIF(c.id_first, 0), s.id_first, 0) AS id_first
            FROM (SELECT ? AS sha1) AS params
            LEFT JOIN combined c ON c.track_sha1 = params.sha1
            LEFT JOIN sha1_hash s ON s.track_sha1 = params.sha1
            LIMIT 1
        ');

        // Bind the parameter
        $stmt->bind_param('s', $sha1);

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        // Get the id_first value
        $tracktype = $data['id_first'] ?? 0;

        // Check if tracktype is 0 and redirect if necessary
        if ($tracktype == 0) {
            header('Location: /');
			exit;
        }
}

if (!is_numeric($tracktype)){
    header('Location: /');
	exit;
}

$sql = "
    SELECT 
        t.id_first AS id,
        t.prefix,
        t.trackname,
        t.track_version,
        t.track_version_extra,
        t.track_author,
        t.track_editor,
        t.track_type,
        t.track_family,
        t.track_clan,
        h.track_sha1,
        t.track_created,
        s.track_wbz_size,
        w.track_warn,
        sl.track_slot,
        t.track_prop,
        t.track_music,
        st.track_speed,
        st.track_laps,
        t.track_customtrack,
        t.track_customarena,
        t.track_texturehack,
        t.track_boost,
        t.track_competition,
        t.track_nintendo,
        t.track_change,
		wc.page_id AS track_wiki2
    FROM tracks t
    LEFT JOIN hash_table h    ON t.id_first = h.id_first
    LEFT JOIN size_table s    ON t.id_first = s.id_first
    LEFT JOIN warn_table w    ON t.id_first = w.id_first
    LEFT JOIN stgi_table st   ON t.id_first = st.id_first
    LEFT JOIN slot_table sl   ON t.id_first = sl.id_first
    LEFT JOIN wiki_curid wc   ON t.track_family = wc.wbz_id  /* only if needed */
    WHERE t.id_enabled = 1
      AND t.id_first = ?
";

$result = $conn->prepare($sql);
$result->bind_param('i', $tracktype);
$result->execute();
$result->store_result();

if ($result->num_rows === 0 && $tracktype !== 0) {
    header('Location: /');
	exit;
}

$result->bind_result(
    $id,
    $prefix,
    $trackname,
    $track_version,
    $track_version_extra,
    $track_author,
    $track_editor,
    $track_type,
    $track_family,
    $track_clan,
    $track_sha1,
    $track_created,
    $track_wbz_size,
    $track_warn,
    $track_slot,
    $track_prop,
    $track_music,
    $track_speed,
    $track_laps,
    $track_customtrack,
    $track_customarena,
    $track_texturehack,
    $track_boost,
    $track_competition,
    $track_nintendo,
    $track_change,
	$track_wiki2
);

$result->fetch();

	echo("<meta 
		name='description'
		content='{$prefix} {$trackname} {$track_version} by {$track_author}");
        if ($track_editor) {
            echo ", {$track_editor}";
        }
	echo "'>";
	$track_sha1_orig = $track_sha1;
?>
</head>

<body>
<?php 
require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; // Top Login Button

?> 

<div id="container">
	<a href='/'><h1>SZS Library</a> - <a href='/?type=distribution'>Distributions</a></h1>

<div class="track_info">
    <h1>
        <?php
        echo "{$prefix} {$trackname} ({$track_author}";
        if ($track_editor) echo ", {$track_editor}";
        echo ")";
        ?>
    </h1>

    <div style="display:flex; flex-wrap:wrap; gap:0px; margin-bottom:10px;">
        <div style="flex:1 1 300px; max-width:480px; margin:20px; margin-top:10px;">
            <?php
            $imgPath = sprintf("thumbnailjpg/%02u/%05u.jpg", $tracktype % 100, $tracktype);
            if (!file_exists($imgPath)) $imgPath = "thumbnailjpg/00/00000.jpg";
            ?>
            <img src="<?php echo $imgPath; ?>" alt="Track Thumbnail" style="width:100%; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.2); margin-bottom:10px;">
			<div id="">
            <table style="margin-top: 0px;">
                <tbody>
                    <tr style="border: none;"><td><a href="https://wiki.tockdom.com/w/index.php?curid=<?php echo $track_wiki2; ?>">wiki.tockdom.com</a></td></tr>
                    <tr><td><a href="scripts/download.php?id=<?php echo $tracktype; ?>">Download</a></td></tr>
                    <?php if (isset($_SESSION['loggedin'])): ?>
                        <tr><td><a href="edit.php?id=<?php echo $tracktype; ?>">Edit</a></td></tr>
                    <?php endif; ?>
				</tbody>
			</table>
			</div>
        </div>
        <div style="flex:2 1 428px; margin: 5px; margin-top: 10px;" >
			<table style="margin-top: 0px;">
                <tbody>
                    <tr><td>Name</td><td><?php echo "{$prefix} {$trackname}"; ?></td></tr>
                    <tr><td>Version</td><td><?php echo "{$track_version}" . ($track_version_extra ? "-{$track_version_extra}" : ""); ?></td></tr>
                    <tr><td>ID</td><td><?php echo "{$tracktype}"; ?></td></tr>
                    <tr><td>Family</td><td><a href="/?family=<?php echo $track_family; ?>"><?php echo "{$track_family}"; ?></a></td></tr>
                    <tr><td>Clan</td><td><a href="/?clan=<?php echo $track_clan; ?>"><?php echo "{$track_clan}"; ?></a>
					<?php if ($track_clan == NULL) {
						echo '-';
					} ?></td></tr>
                    <tr><td>Type</td><td><?php 
					if ($track_nintendo == 1)
					{
						echo "Original ";
					}
					else 
					{
						echo "Custom ";
					}
					if ($track_texturehack == 1)
					{
						echo "Texture";
					}
					if ($track_customtrack == 1)
					{
						echo " Track";
					}
					if ($track_customarena == 1)
					{
						echo " Arena";
					}
					if ($track_change == 1)
					{
						echo " Hack";
					}
					if ($track_competition == 1)
					{
						echo " Competition";
					}
					?></td></tr>
					
                    <tr><td>Size</td><td><?php echo "{$track_wbz_size} Bytes"; ?></td></tr>
                    <tr><td>Date created</td><td><?php echo "{$track_created}"; ?></td></tr>
                    <tr><td>Laps:</td><td><?php echo "{$track_laps}"; ?></td></tr>
                    <tr><td>Speed Modifier:</td><td><?php echo "{$track_speed}"; ?></td></tr>
                    <tr><td>Slot Info:</td><td><?php echo $track_slot2[ trim($track_slot ?? '') ] ?? "None"; ?></td></tr>
                    <tr><td>Music Slot:</td><td><?php echo $track_music2[$track_music] ?? "None";?></td></tr>
                    <tr><td>Property Slot:</td><td><?php echo $track_names[$track_prop] ?? "None";?></td></tr>
                    <tr><td>Warnings:</td><td><?php echo "{$track_warn}"; ?></td></tr>
                </tbody>
            </table>
			<br>
            <table style="margin-top: 0px;">
                <tbody>
					<tr><td>SHA1</td><td><?php echo "{$track_sha1}"; ?></td></tr>
<?php 				$sql2 = 'SELECT track_sha1 FROM sha1_hash WHERE id_first = ? && track_sha1 != ?';
					$result3 = $conn->prepare($sql2);
					$result3->bind_param('is', $tracktype, $track_sha1);
					$result3->execute();
					$result3->store_result();
					$result3->bind_result($track_alias);
					if ($result3->num_rows === 0) { 
						} else { 
					echo("<tr><td>Alias:</td><td>");
					while($result3->fetch()) {
						echo("{$track_alias}<br>");
					}
						echo("</td></tr>");
					}
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
	echo("<h2 style='margin-top: 10px; padding: 5px; margin-bottom: 10px;'>Family {$track_family}</h2>"
	);
?>

<table id="trackTable" class="display" style="width:100%">
</table>

<script>
var track_family = "<?php echo htmlspecialchars($track_family); ?>";
$(document).ready(function () {

// Family filter
$('#trackTable').on('click', '.filter-family', function (e) {
    e.preventDefault();
    var family = $(this).data('family');
    table.column(5).search(family, false, false).draw(); // exact
});
	
    var table = $('#trackTable').DataTable({
        processing: true,
        serverSide: true,
		responsive: true,
		searching: false,
		paging: false,
		ajax: {
			url: 'fetch_tracks.php',
			type: 'POST',
			data: function (d) {
				const urlParams = new URLSearchParams(window.location.search);
				d.family = track_family || '';
			}
		},
		columns: [
			{ data: 'id_first', title: 'ID', responsivePriority: 8 },
			{ data: 'trackname', title: 'Name', responsivePriority: 2 },
			{ data: 'track_version', title: 'Version', responsivePriority: 4 },
			{ data: 'track_author', title: 'Author', responsivePriority: 3 },
			{ data: 'track_type', title: 'Type', responsivePriority: 8 },
			{ data: 'track_size', title: 'Size', responsivePriority: 5 },
			{ data: 'track_created', title: 'Created', responsivePriority: 6 }
			<?php if (isset($_SESSION['loggedin'])): ?>
				,{ data: 'edit', title: 'Edit', responsivePriority: 1 }
			<?php endif; ?>
		],
		columnDefs: [
		  { targets: 0, width: "60px" },
		  { targets: 1, width: "250px" },
		],
		order: [[0, 'desc']] // Column index 0 descending
    });
});

</script>

<h2 style="margin-top: 10px; padding: 5px; margin-bottom: 10px;">
    Distributions using this track
</h2>

<table id="distributionTable" class="display" style="width:100%">
</table>

<script>
var track_sha1 = "<?php echo htmlspecialchars($track_sha1_orig); ?>";

$(document).ready(function () {

    var distTable = $('#distributionTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false,
        paging: false,
        ajax: {
            url: 'fetch_distribution.php',
            type: 'POST',
            data: function (d) {
                d.sha1 = track_sha1;
            }
        },
        columns: [
            { data: 'dist_name', title: 'Name', responsivePriority: 2 },
            { data: 'dist_version', title: 'Version', responsivePriority: 3 },
            { data: 'dist_author', title: 'Author', responsivePriority: 4 },
            { data: 'dist_release', title: 'Release Date', responsivePriority: 5 }
        ],
        order: [[0, 'asc']]
    });

});
</script>
</div>
<?php 
require '/var/www/vhosts/szslibrary.com/httpdocs/included/footer.php'; // Bottom Footer
?> 
</body>
</html>
