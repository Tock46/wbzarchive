<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /../');
    exit();
}

if (isset($_GET['enabled'])) {
    $row2['id_enabled'] = htmlspecialchars($_GET['id_enabled'] ?? '');
}

if (isset($_GET['prefix'])) {
    $row2['prefix'] = htmlspecialchars($_GET['prefix'] ?? '');
}

if (isset($_GET['trackname'])) {
    $row2['trackname'] = htmlspecialchars($_GET['trackname'] ?? '');
}

if (isset($_GET['version'])) {
    $row2['track_version'] = htmlspecialchars($_GET['version'] ?? '');
}

if (isset($_GET['extra'])) {
    $row2['track_version_extra'] = htmlspecialchars($_GET['extra'] ?? '');
}

if (isset($_GET['author'])) {
    $row2['track_author'] = htmlspecialchars($_GET['author'] ?? '');
}

if (isset($_GET['editor'])) {
    $row2['track_editor'] = htmlspecialchars($_GET['editor'] ?? '');
}

if (isset($_GET['family'])) {
    $row2['track_family'] = htmlspecialchars($_GET['family'] ?? '');
}

if (isset($_GET['clan'])) {
    $row2['track_clan'] = htmlspecialchars($_GET['clan'] ?? '');
}

if (isset($_GET['date'])) {
    $row2['track_created'] = htmlspecialchars($_GET['date'] ?? '');
}

if (isset($_GET['link'])) {
    $row2['link'] = htmlspecialchars($_GET['link'] ?? '');
}

require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; // Top Login Button

$id = $_GET['id'] ?? "-1";

// Fetch track details
if ($id != "-1") {
    $stmt = $conn->prepare("SELECT * FROM tracks WHERE id_enabled=1 AND id_first=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $row2 = $result2->fetch_assoc();
    $stmt->close();
} else {
    $result24 = $conn->query("SELECT MAX(id_first) AS max_value FROM tracks");
    $max_id = $result24->fetch_assoc();
    $row2['id_first'] = $max_id['max_value'] + 1;
}

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./style.css">
    <title>SZS Library - Admin</title>
</head>
<body>
    <div id="container">
        <form method="post" action="scripts/submitted.php">
            <a href='/?type=files'><h1>SZS Library - Admin</h1></a>
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 0px; text-align: center;">Enabled</th>
                        <th style="padding-left: 0px; text-align: center;">Prefix</th>
                        <th style="padding-left: 0px; text-align: center;">Trackname</th>
                        <th style="padding-left: 0px; text-align: center;">Version</th>
                        <th style="padding-left: 0px; text-align: center;">Version Extra</th>
                        <th style="padding-left: 0px; text-align: center;">Author</th>
                        <th style="padding-left: 0px; text-align: center;">Track Editor</th>
                        <th style="padding-left: 0px; text-align: center;">Family</th>
                        <th style="padding-left: 0px; text-align: center;">Clan</th>
                        <th style="padding-left: 0px; text-align: center;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input id="submitbox" type="checkbox" name="id_enabled" <?php echo ($row2['id_enabled'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="text" name="prefix" value="<?php echo htmlspecialchars($row2['prefix'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="text" name="trackname" value="<?php echo htmlspecialchars($row2['trackname'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="text" name="track_version" value="<?php echo htmlspecialchars($row2['track_version'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="text" name="track_version_extra" value="<?php echo htmlspecialchars($row2['track_version_extra'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="text" name="track_author" value="<?php echo htmlspecialchars($row2['track_author'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="text" name="track_editor" value="<?php echo htmlspecialchars($row2['track_editor'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="number" name="track_family" value="<?php echo htmlspecialchars($row2['track_family'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="number" name="track_clan" value="<?php echo htmlspecialchars($row2['track_clan'] ?? ''); ?>"></td>
                        <td><input id="submitbox" type="text" name="track_created" value="<?php echo htmlspecialchars($row2['track_created'] ?? ''); ?>"></td>
                    </tr>
                </tbody>
            </table>
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
                        <td><input id="submitbox" type="checkbox" name="track_customtrack" <?php echo ($row2['track_customtrack'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="checkbox" name="track_customarena" <?php echo ($row2['track_customarena'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="checkbox" name="track_change" <?php echo ($row2['track_change'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="checkbox" name="track_competition" <?php echo ($row2['track_competition'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="checkbox" name="track_texturehack" <?php echo ($row2['track_texturehack'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="checkbox" name="track_boost" <?php echo ($row2['track_boost'] ?? '') === "1" ? "checked" : ""; ?>></td>
                        <td><input id="submitbox" type="checkbox" name="track_nintendo" <?php echo ($row2['track_nintendo'] ?? '') === "1" ? "checked" : ""; ?>></td>
	<td><select id="submitbox" name="track_music">
		<?php foreach ($track_music2 as $value => $label): ?>
			<option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
		<?php endforeach; ?>
  </select></td>
	<td><select id="submitbox" name="track_prop">
		<?php foreach ($track_names as $value2 => $label2): ?>
			<option value="<?= htmlspecialchars($value2) ?>"><?= htmlspecialchars($label2) ?></option>
		<?php endforeach; ?>
  </select></td>
                    </tr>
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
						<th id="submitbox" style="padding-left: 0px; text-align: center;">Download Link</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
						<td><input id="submitbox" type="text" name="downloadlink" id="downloadlink" value="<?php echo htmlspecialchars($row2['link'] ?? ''); ?>"></td>
                    </tr>
                </tbody>
            </table>
            <br>
				<input type="submit" name="Submit" id="Submit" value="Submit">
            <br>
			<input id="submitbox"  type="hidden" name="id_first" value="<?php echo htmlspecialchars($row2['id_first'] ?? ''); ?>">
        </form>
		<br>
    </div>
</body>
</html>