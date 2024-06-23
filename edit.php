<?php
session_start();
require __DIR__ . '/included/header.php';
if (!isset($_SESSION['loggedin'])) {
	echo ('<meta http-equiv="refresh" content="0; url=http://archive.tock.eu/">');
	exit();
}
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

 <?php

if (isset($_GET['id'])){
$id = $_GET['id'];
}
else {
$id = "1";
}

$sql2 = "SELECT id, id_enabled, id_first, track_wiimm, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_family, track_clan, track_type, track_sha1, track_sha3, track_created, track_wbz_size, track_szs_size, track_customtrack, track_customarena, track_texturehack, track_change, track_nintendo, track_laps, track_speed, track_music, track_prop, track_warn, track_wiki, last_mod, revision FROM tracks WHERE id_first=$id ORDER BY revision DESC LIMIT 0, 1";
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();
?> 

<?php 
require __DIR__ . '/included/topbar.php'; // Top Login Button
?> 

<div id="container">
<form method="post" action="scripts/editted.php">
	<a href='/'><h1>WBZ Archive - Admin</h1></a>
	<table>
	    <thead>
		<tr>
			<th>Enabled</th>
			<th>Prefix</th>
			<th>Trackname</th>
			<th>Version</th>
			<th>Version Extra</th>
			<th>Author</th>
			<th>Editor</th>
			<th>Family</th>
			<th>Clan</th>
			<th>Date</th>
		</tr>
	    </thead>
	    <tbody>
<tr>
	<td>
		<p>
			<input id="submitbox" type="checkbox" name="id_enabled" id="id_enabled" <?php if($row2['id_enabled'] === "1") echo "checked"; ?>>
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="prefix" id="prefix" value="<?php echo $row2['prefix']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="trackname" id="trackname" value="<?php echo $row2['trackname']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_version" id="track_version" value="<?php echo $row2['track_version']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_version_extra" id="track_version_extra" value="<?php echo $row2['track_version_extra']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_author" id="track_author" value="<?php echo $row2['track_author']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_editor" id="track_editor" value="<?php echo $row2['track_editor']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="number" name="track_family" id="track_family" value="<?php echo $row2['track_family']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="number" name="track_clan" id="track_clan" value="<?php echo $row2['track_clan']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_created" id="track_created" value="<?php echo $row2['track_created']; ?>">
		</p>
	</td>
</tr>
</tbody></table>
		<table>
	    <thead>
		<tr>
			<th width="8%" text-align="center"  padding-left="0px">Track</th>
			<th width="8%" text-align="center">Arena</th>
			<th width="8%" text-align="center">Hack</th>
			<th width="8%" text-align="center">Texture</th>
			<th width="8%" text-align="center">Nintendo</th>
			<th width="10%" text-align="center">Music Slot</th>
			<th width="10%" text-align="center">Property Slot</th>
			<th width="10%" text-align="center">Wiki Cur ID</th>
		</tr>
	    </thead>
	    <tbody>
<tr>
	<td>
		<p>
			<input id="submitbox" type="checkbox" name="track_customtrack" id="track_customtrack" <?php if($row2['track_customtrack'] === "1") echo "checked"; ?>>
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="checkbox" name="track_customarena" id="track_customarena" <?php if($row2['track_customarena'] === "1") echo "checked"; ?>>
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="checkbox" name="track_change" id="track_change" <?php if($row2['track_change'] === "1") echo "checked"; ?>>
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="checkbox" name="track_texturehack" id="track_texturehack" <?php if($row2['track_texturehack'] === "1") echo "checked"; ?>>
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="checkbox" name="track_nintendo" id="track_nintendo" <?php if($row2['track_nintendo'] === "1") echo "checked"; ?>>
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_music" id="track_music" value="<?php echo $row2['track_music']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="text" name="track_prop" id="track_prop" value="<?php echo $row2['track_prop']; ?>">
		</p>
	</td>
	<td>
		<p>
			<input id="submitbox" type="number" name="track_wiki" id="track_wiki" value="<?php echo $row2['track_wiki']; ?>">
		</p>
	</td>
</tr>
	<input id="submitbox" type="hidden" name="id_first" id="id_first" value="<?php echo $row2['id_first']; ?>">
</form>
	</tbody>
	</table>
	<br>
<input type="submit" name="Submit" id="Submit" value="Submit">
	<br>
	<br>

<?php 
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
$result3 = $conn->prepare('SELECT id_first, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_type, track_family, track_clan, track_sha1, track_sha3, track_created, track_customtrack, track_customarena, track_texturehack, track_nintendo, track_change, track_wbz_size, track_wiki, track_warn, track_prop, track_music, track_speed, track_laps, last_mod FROM tracks WHERE id_first=? ORDER BY revision DESC');
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$result3->bind_param('i', $id);
	$result3->execute();
	// Store the result so we can check if the account exists in the database.
	$result3->store_result();
	$result3->bind_result($id_first, $prefix, $trackname, $track_version, $track_version_extra, $track_author, $track_editor, $track_type, $track_family, $track_clan, $track_sha1, $track_sha3, $track_created, $track_customtrack, $track_customarena, $track_texturehack, $track_nintendo, $track_change, $track_wbz_size, $track_wiki, $track_warn, $track_prop, $track_music, $track_speed, $track_laps, $last_mod);

	 echo("<h2>History</h2><table>
	    <thead>
		<tr>
			<th>Trackname</th>
			<th>Version</th>
			<th>Author</th>
			<th>Family</th>
			<th>Clan</th>
			<th>Date</th>
			<th>CT</th>
			<th>CA</th>
			<th>CH</th>
			<th>TH</th>
			<th>Nin</th>
			<th>Music Slot</th>
			<th>Prop Slot</th>
			<th>Mod</th>
		</tr>
	    </thead>
	    <tbody>"
		);

  while($row = $result3->fetch()) {
	 echo("
		<tr class=\"class\">
			<td>{$prefix} {$trackname}</td>
			<td>{$track_version}");
		if ($track_version_extra != NULL){
		echo("-{$track_version_extra}");
		}
	 echo("</td>
			<td>{$track_author}");
		if ($track_editor != NULL){
		echo(", {$track_editor}");
		}
	 echo("</a></td>
			<td>{$track_family}</td>
			<td>{$track_clan}</td>
			<td>{$track_created}</td>
			<td>{$track_customtrack}</td>
			<td>{$track_customarena}</td>
			<td>{$track_texturehack}</td>
			<td>{$track_nintendo}</td>
			<td>{$track_change}</td>
			<td>{$track_music}</td>
			<td>{$track_prop}</td>
			<td>{$last_mod}</td>
		</tr>");
	}
	 echo("
	    </tbody>
	</table>");
?>
</div>
</body>
</html>

