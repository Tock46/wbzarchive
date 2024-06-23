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
</head>

<body>

 <?php 
require __DIR__ . '/included/topbar.php'; // Top Login Button
?> 

<?php // Search Bar / Search Buttons ?>

<div id="container">
	<a href='/?type=files'><h1>WBZ Archive</a> - <a href='/?type=distribution'>Distributions</a></h1>

 <?php 
require __DIR__ . '/included/search.php'; // Search
?> 

<?php
// Search Script
	$lcSearchVal[] = "";
// Ensure $searchString is properly escaped
if ($searchString != "")
{
	$searchString = mysqli_real_escape_string($conn, $searchString);
}

$searchString2 = $searchString;

if ($searchString != "")
{
	$lcSearchVal = explode(' ', $searchString2);
}
$lcSearchVal1[] = $searchString2;
$prefixamount = 0;
$unprefixamount = 0;

foreach ($lcSearchVal as $lcSearchWord) {
    if (in_array(strtolower($lcSearchWord), ["gcn", "snes", "gba", "n64", "tour", "mkt", "3ds"])) {
        $prefixamount++;
    } else {
        $unprefixamount++;
    }
}

$sql = 'SELECT id, trackname, id_enabled, id_first, prefix, track_version, track_version_extra, track_author, track_editor, track_type, track_family, track_clan, track_sha1, track_created, track_wbz_size, track_customtrack, track_customarena, track_texturehack, track_change, track_competition, track_nintendo FROM tracks WHERE id_enabled=1';

$params = [];
$types = '';

if ($clan != 0) {
    $sql .= ' AND track_clan=?';
    $params[] = $clan;
    $types .= 'i';
}

if ($type != 0) {
    $sql .= ' AND track_type=?';
    $params[] = $type;
    $types .= 'i';
}

if ($missingfiles != 0) {
    $sql .= ' AND track_sha1 IS NULL';
}

if ($missingfiles == 0) {
    $sql .= ' AND track_sha1 IS NOT NULL';
}

// TRACKNAME search
if ($unprefixamount > 0) {
    $sql .= ' AND (';
    $parts = [];
    foreach ($lcSearchVal as $lcSearchWord) {
        if (!in_array(strtolower($lcSearchWord), ["gcn", "snes", "gba", "n64", "tour", "mkt", "3ds"])) {
            $parts[] = 'trackname LIKE ?';
            $params[] = "%$lcSearchWord%";
            $types .= 's';
        }
    }
    if (!empty($parts)) {
        $sql .= implode(' OR ', $parts);
    }
}

// AUTHOR search
if ($unprefixamount > 0) {
    $sql .= ' OR ';
    $parts = [];
    foreach ($lcSearchVal as $lcSearchWord) {
        if (!in_array(strtolower($lcSearchWord), ["gcn", "snes", "gba", "n64", "tour", "mkt", "3ds"])) {
            $parts[] = 'track_author LIKE ?';
            $params[] = "%$lcSearchWord%";
            $types .= 's';
        }
    }
    if (!empty($parts)) {
        $sql .= implode(' OR ', $parts);
    }
    $sql .= ')';
}

// PREFIX search
if ($prefixamount > 0) {
    $sql .= ' AND (';
    $parts = [];
    foreach ($lcSearchVal as $lcSearchWord) {
        if (in_array(strtolower($lcSearchWord), ["gcn", "snes", "gba", "n64", "tour", "mkt", "3ds"])) {
            $parts[] = 'prefix LIKE ?';
            $params[] = "%$lcSearchWord%";
            $types .= 's';
        }
    }
    if (!empty($parts)) {
        $sql .= implode(' OR ', $parts);
    }
    $sql .= ')';
}

if ($ct == 1)
{
	$sql .= ' AND track_customtrack=?';
$params[] = 0;
$types .= 'i';
}

if ($ca == 1)
{
	$sql .= ' AND track_customarena=?';
$params[] = 0;
$types .= 'i';
}

if ($th == 1)
{
	$sql .= ' AND track_texturehack=?';
$params[] = 0;
$types .= 'i';
}

if ($tc == 1)
{
	$sql .= ' AND track_change=?';
$params[] = 0;
$types .= 'i';
}

$sql .= ' ORDER BY ';

if ($clan == 0)
{
	$sql .= 'id_first';
}
else
{
	$sql .= 'track_family';
}

$sql .= ' DESC';

if ($clan != 0)
{
	$sql .= ', id_first DESC';
}

$sql .= ' LIMIT ?, 20';

$params[] = $pagelimit;
$types .= 'i';

// Prepare and execute the statement
$result2 = $conn->prepare($sql);
if ($result2->errno) {
    echo "Error preparing statement: $result2->error";
    exit();
}

// Bind parameters dynamically
$result2->bind_param($types, ...$params);
$result2->execute();
$result2->store_result();
$result2->bind_result($id, $trackname, $id_enabled, $id_first, $prefix, $track_version, $track_version_extra, $track_author, $track_editor, $track_type, $track_family, $track_clan, $track_sha1, $track_created, $track_wbz_size, $track_customtrack, $track_customarena, $track_texturehack, $track_change, $track_competition, $track_nintendo);
	
        if ($result2->num_rows === 0) {
            // No match found
            echo "No match found";
            // Kill the script
            exit();

        } 
	 echo("<table>
	    <thead>
		<tr>
			<th style=\"width: 35%\">Trackname</th>
			<th style=\"width: 10%\">Version</th>
			<th style=\"width: 30%\">Author</th>
			<th style=\"width: 5%\">Family</th>
			<th style=\"width: 5%\">Clan</th>
			<th style=\"width: 8%\">Type</th>
			<th style=\"width: 5%\">Date</th>"
		);
if (isset($_SESSION['loggedin'])) {
 echo ("<th style=\"width: 5%\">Edit</th>");
} 
	 echo("
		</tr>
	    </thead>
	    <tbody>"
		);
		
  while($result2->fetch()) {
	 echo("
		<tr class=\"class\">
			<td><a href='track.php?id={$id_first}'>{$prefix} {$trackname}</a></td>
			<td>{$track_version}"
		);
		if ($track_version_extra != NULL){
		echo("-{$track_version_extra}");
		}
	 echo("</td>
			<td><a href='index.php?s={$track_author}'>{$track_author}"
		);
		if ($track_editor != NULL){
		echo(", {$track_editor}");
		}
	 echo("</a></td>
			<td><a href='track.php?id={$track_family}'>{$track_family}</a></td>
			<td><a href='./?clan={$track_clan}'>{$track_clan}"
		);
		if ($track_clan === NULL){
		echo("-");
		}
	 echo("</a></td>
			<td>");
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
	 echo("</td>
			<td>{$track_created}</td>"
		);
if (isset($_SESSION['loggedin'])) {
 echo ("<td><a href='submit.php?id={$id_first}'>edit</a></td>");
} 
	 echo("
		</tr>");
	}
$result2->close();
$conn->close();
?>
	    </tbody>
	</table>
	<?php echo("<a href='/index.php?s={$searchString}");
	echo("&clan=$clan");
	echo("&type=$type");
	echo("&ct=$ct");
	echo("&ca=$ca");
	echo("&tc=$tc");
	echo("&th=$th");
	echo("&cc=$cc");
	echo("&cv=$cv");
	echo("&cm=$cm");
	echo("&pe=$pe");echo("&page=$ppage'>")
	?><button class="rightclick"><h2>Previous Page</h2></button></a><?php echo("<a href='/index.php?s={$searchString}");
	echo("&clan=$clan");
	echo("&type=$type");
	echo("&ct=$ct");
	echo("&ca=$ca");
	echo("&tc=$tc");
	echo("&th=$th");
	echo("&cc=$cc");
	echo("&cv=$cv");
	echo("&cm=$cm");
	echo("&pe=$pe");echo("&page=$npage'>")
	?><button class="leftclick"><h2>Next Page</h2></button></a>
</div>
<?php 
require __DIR__ . '/included/footer.php'; // Bottom Footer
?> 
</body>
</html>