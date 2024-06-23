<?php
session_start();
require __DIR__ . '/included/header.php';
?> 
<!doctype html>
<html>
<head>
   <meta charset="UTF-8">
   <!--<link rel="shortcut icon" href="./.favicon.ico">-->
   <link rel="stylesheet" href="./style.css">
   <title>WBZ Archive</title>

 <?php

	if (!isset($_GET['id'])){
			echo ('<meta http-equiv="refresh" content="0; url=http://archive.tock.eu">');
        // echo "No track ID given.";
		exit();
	}
	$tracktype = (int) $_GET['id'];
	if (!is_numeric($tracktype)){
			echo ('<meta http-equiv="refresh" content="0; url=http://archive.tock.eu">');
            // echo "No number.";
            exit();
	}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
$result1 = $conn->prepare('SELECT id, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_type, track_family, track_clan, track_sha1, track_sha3, track_created, track_wbz_size, track_wiki, track_warn, track_slot, track_prop, track_music, track_speed, track_laps, track_customtrack, track_customarena, track_texturehack, track_boost, track_competition, track_nintendo, track_change FROM tracks WHERE id_enabled=1 && id_first = ?');
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$result1->bind_param('i', $tracktype);
	$result1->execute();
	// Store the result so we can check if the account exists in the database.
	$result1->store_result();
	$result1->bind_result($id, $prefix, $trackname, $track_version, $track_version_extra, $track_author, $track_editor, $track_type, $track_family, $track_clan, $track_sha1, $track_sha3, $track_created, $track_wbz_size, $track_wiki, $track_warn, $track_slot, $track_prop, $track_music, $track_speed, $track_laps, $track_customtrack, $track_customarena, $track_texturehack, $track_boost, $track_competition, $track_nintendo, $track_change);
	$result1->fetch();
        if ($result1->num_rows === 0 && $tracktype !== 0) {
            // No match found

			echo ('<meta http-equiv="refresh" content="0; url=http://archive.tock.eu">');
            //echo "No match found";
            // Kill the script
            exit();

        } 
?> 
</head>

<body>
<?php 
require __DIR__ . '/included/topbar.php'; // Top Login Button
?> 

<div id="container">
	<a href='/?type=files'><h1>WBZ Archive</a> - <a href='/?type=distribution'>Distributions</a></h1>

<?php 
require __DIR__ . '/included/search.php'; // Search
?> 
<div class="track_info">
    <h1>
        <?php
        echo "{$prefix} {$trackname} ({$track_author}";
        if ($track_editor) {
            echo ", {$track_editor}";
        }
        echo ")";
        ?>
    </h1>
    <div class="information">
        <img src="<?php printf("thumbnail/%02u/%05u", $tracktype % 100, $tracktype); ?>.png" style="width:calc(33.33%); max-width:480px">

        <div id="trackinfo_left">
            <table style="margin-top: 0px;">
                <tbody>
                    <tr><td>Name</td><td><?php echo "{$prefix} {$trackname}"; ?></td></tr>
                    <tr><td>Version</td><td><?php echo "{$track_version}" . ($track_version_extra ? "-{$track_version_extra}" : ""); ?></td></tr>
                    <tr><td>ID</td><td><?php echo "{$tracktype}"; ?></td></tr>
                    <tr><td>Family</td><td><?php echo "{$track_family}"; ?></td></tr>
                    <tr><td>Clan</td><td><?php echo "{$track_clan}"; ?>
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
                    <tr><td>SHA1</td><td><?php echo "<a title='{$track_sha1}'>hover</a>"; ?></td></tr>
                    <tr><td>Size</td><td><?php echo "{$track_wbz_size} Bytes"; ?></td></tr>
                    <tr><td>Date created</td><td><?php echo "{$track_created}"; ?></td></tr>
                </tbody>
            </table>
        </div>

        <div id="trackinfo_right">
            <table style="margin-top: 0px;">
                <tbody>
                    <tr><td>Laps:</td><td><?php echo "{$track_laps}"; ?></td></tr>
                    <tr><td>Speed Modifier:</td><td><?php echo "{$track_speed}"; ?></td></tr>
                    <tr><td>Slot info:</td><td><?php echo "{$track_slot}"; ?></td></tr>
                    <tr><td>Music Slot:</td><td><?php echo "{$track_music}"; ?></td></tr>
                    <tr><td>Property Slot:</td><td><?php echo "{$track_prop}"; ?></td></tr>
                    <tr><td>Warnings:</td><td><?php echo "{$track_warn}"; ?></td></tr>
                    <tr><td colspan="2"><a href="https://wiki.tockdom.com/w/index.php?curid=<?php echo $track_wiki; ?>">wiki.tockdom.com</a></td></tr>
                    <tr><td colspan="2"><?php printf("<a href='./check/%02u/%05u.check'>WSZST Check</a>", $tracktype % 100, $tracktype); ?></td></tr>
                    <tr><td colspan="2"><a href="scripts/download.php?id=<?php echo $tracktype; ?>">Download</a></td></tr>
                    <?php if (isset($_SESSION['loggedin'])): ?>
                        <tr><td colspan="2"><a href="edit.php?id=<?php echo $tracktype; ?>">Edit</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
$result2 = $conn->prepare('SELECT track_wiimm, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_type, track_family, track_clan, track_sha1, track_sha3, track_created, track_wbz_size FROM tracks WHERE id_enabled=1 && track_family = ? ORDER BY track_wiimm DESC');
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$result2->bind_param('i', $track_family);
	$result2->execute();
	// Store the result so we can check if the account exists in the database.
	$result2->store_result();
	$result2->bind_result($track_wiimm, $prefix, $trackname, $track_version, $track_version_extra, $track_author, $track_editor, $track_type, $track_family, $track_clan, $track_sha1, $track_sha3, $track_created, $track_wbz_size);

	 echo("<h2>Family {$track_family}</h2><table>
	    <thead>
		<tr>
			<th>Trackname</th>
			<th>Version</th>
			<th>Author</th>
			<th>Clan</th>
			<th>Type</th>
			<th>SHA1</th>
			<th>Date</th>
			<th>Size</th>
			<th>WSZST Check</th>
		</tr>
	    </thead>
	    <tbody>"
		);

  while($row = $result2->fetch()) {
	 echo("
		<tr class=\"class\">
			<td><a href='track.php?id={$track_wiimm}'>{$prefix} {$trackname}</a></td>
			<td>{$track_version}");
		if ($track_version_extra != NULL){
		echo("-{$track_version_extra}");
		}
	 echo("</td>
			<td><a href='./index.php?s={$track_author}'>{$track_author}");
		if ($track_editor != NULL){
		echo(", {$track_editor}");
		}
	 echo("</a></td>
			<td><a href='./?clan={$track_clan}'>{$track_clan}");
		if ($track_clan == NULL){
		echo("-");
		}
	 echo("</a></td>
			<td>");
					if ($track_customtrack == 1)
					{
						echo "Track";
					}
					if ($track_customarena == 1)
					{
						echo "Arena";
					}
					if ($track_texturehack == 1)
					{
						echo "Texture Hack";
					}
					if ($track_change == 1)
					{
						echo "Custom Hack";
					}
					if ($track_competition == 1)
					{
						echo "Custom Competition";
					}
	 echo("</td>
			<td><a title='{$track_sha1}'>hover</a></td>
			<td>{$track_created}</td>
			<td>{$track_wbz_size} Bytes</td>
			<td><a href='./online/{$track_sha3}/track.check'>Check</a></td>
		</tr>");
	}
?>
	    </tbody>
		</table>
		</div>
<?php 
require __DIR__ . '/included/footer.php'; // Bottom Footer
?> 
</body>
</html>
