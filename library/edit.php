<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
if (!isset($_SESSION['loggedin'])) {
    header('Location: /');
    exit;
}
?> 
<!doctype html>
<html>
<head>
   <meta charset="UTF-8">
   <!--<link rel="shortcut icon" href="./.favicon.ico">-->
   <link rel="stylesheet" href="./style.css">
   <title>SZS Library - Admin</title>
</head>

<body>

 <?php

if (!isset($_GET['id'])){
    header('Location: /');
    exit;
}

$tracktype = (int) $_GET['id'];

if (!is_numeric($tracktype)){
    header('Location: /');
    exit;
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$sql2 = "SELECT id, id_enabled, id_first, track_wiimm, prefix, trackname, track_version, track_version_extra, track_author, track_editor, track_family, track_clan, track_created, track_customtrack, track_customarena, track_competition, track_texturehack, track_change, track_boost, track_nintendo, track_music, track_prop, last_mod, revision FROM tracks WHERE id_first=? ORDER BY revision DESC LIMIT 0, 1";

$result2 = $conn->prepare($sql2);
$result2->bind_param('i', $tracktype);
$result2->execute();
$result2->store_result();

$result2->bind_result(
    $id, $id_enabled, $id_first, $track_wiimm, $prefix, $trackname, $track_version, $track_version_extra, 
    $track_author, $track_editor, $track_family, $track_clan, 
    $track_created, $track_customtrack, $track_customarena, $track_competition, $track_texturehack, 
    $track_change, $track_boost, $track_nintendo, $track_music, $track_prop, 
    $last_mod, $revision
);

$result2->fetch();

?> 

<?php 
require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; // Top Login Button
?> 

<div id="container">
<form method="post" action="scripts/editted.php">
	<a href='/'><h1>SZS Library - Admin</h1></a>
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
	<td><input id="submitbox" type="checkbox" name="id_enabled" value="1" <?= ((int)$id_enabled === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="text" name="prefix" value="<?= e($prefix) ?>"></td>
	<td><input id="submitbox" type="text" name="trackname" value="<?= e($trackname) ?>"></td>
	<td><input id="submitbox" type="text" name="track_version" value="<?= e($track_version) ?>"></td>
	<td><input id="submitbox" type="text" name="track_version_extra" value="<?= e($track_version_extra) ?>"></td>
	<td><input id="submitbox" type="text" name="track_author" value="<?= e($track_author) ?>"></td>
	<td><input id="submitbox" type="text" name="track_editor" value="<?= e($track_editor) ?>"></td>
	<td><input id="submitbox" type="number" name="track_family" value="<?= (int)$track_family ?>"></td>
	<td><input id="submitbox" type="number" name="track_clan" value="<?= (int)$track_clan ?>"></td>
	<td><input id="submitbox" type="text" name="track_created" value="<?= e($track_created) ?>"></td>
</tr>
</tbody></table>
		<table>
	    <thead>
		<tr>
			<th style="padding-left: 0px; text-align: center;">Track</th>
            <th style="padding-left: 0px; text-align: center;">Arena</th>
            <th style="padding-left: 0px; text-align: center;">Hack</th>
            <th style="padding-left: 0px; text-align: center;">Competition</th>
            <th style="padding-left: 0px; text-align: center;">Texture</th>
            <th style="padding-left: 0px; text-align: center;">Boost</th>
			<th style="padding-left: 0px; text-align: center;">Nintendo</th>
            <th style="padding-left: 0px; text-align: center;">Music Slot</th>
            <th style="padding-left: 0px; text-align: center;">Property Slot</th>
		</tr>
	    </thead>
	    <tbody>
<tr>
    <td><input id="submitbox" type="checkbox" name="track_customtrack" value="1" <?= ((int)($track_customtrack ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="checkbox" name="track_customarena" value="1" <?= ((int)($track_customarena ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="checkbox" name="track_change" value="1" <?= ((int)($track_change ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="checkbox" name="track_competition" value="1"<?= ((int)($track_competition ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="checkbox" name="track_texturehack" value="1"<?= ((int)($track_texturehack ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="checkbox" name="track_boost" value="1"<?= ((int)($track_boost ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><input id="submitbox" type="checkbox" name="track_nintendo" value="1"<?= ((int)($track_nintendo ?? 0) === 1) ? 'checked' : '' ?>></td>
	<td><select id="submitbox" name="track_music">
        <?php foreach ($track_music2 as $value => $label): ?>
            <option
                value="<?= e($value) ?>"
                <?= ((string)$value === (string)$track_music) ? 'selected' : '' ?>
            >
                <?= e($label) ?>
            </option>
        <?php endforeach; ?>
    </select></td>
	<td><select id="submitbox" name="track_prop">
        <?php foreach ($track_names as $value2 => $label2): ?>
            <option
                value="<?= e($value2) ?>"
                <?= ((string)$value2 === (string)$track_prop) ? 'selected' : '' ?>
            >
                <?= e($label2) ?>
            </option>
        <?php endforeach; ?>
    </select></td>
</tr>
	<input
    id="submitbox"
    type="hidden"
    name="id_first"
    value="<?= (int)$id_first ?>"
>
</form>
	</tbody>
	</table>
	<br>
<input type="submit" name="Submit" id="Submit" value="Submit">
	<br>
	<br>
</div>
</body>
</html>