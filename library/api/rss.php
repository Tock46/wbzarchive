<?php
// Load DB connection 
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

// Tell the browser this is RSS/XML
header('Content-Type: application/rss+xml; charset=UTF-8');

$sql2 = "
    SELECT
        id,
        id_first,
        track_wiimm,
        prefix,
        trackname,
        track_version,
        track_version_extra,
        track_author,
        track_editor,
        track_family,
        track_clan,
        track_created,
        track_music,
        track_prop,
        revision
    FROM tracks
    WHERE id_enabled = 1
    ORDER BY revision DESC
	LIMIT 100
";

$result = $conn->query($sql2);

// Basic site info
$siteTitle = "SZSLibrary – Latest Tracks";
$siteLink  = "https://szslibrary.com/";
$siteDesc  = "Latest enabled tracks ordered by newest revision.";

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
<channel>
    <title><?= htmlspecialchars($siteTitle) ?></title>
    <link><?= htmlspecialchars($siteLink) ?></link>
    <description><?= htmlspecialchars($siteDesc) ?></description>
    <language>en-us</language>

<?php
if ($result) {
    while ($row = $result->fetch_assoc()) {

        // Build a readable title
        $title = trim(
            $row['prefix'] . ' ' .
            $row['trackname'] . ' ' .
            $row['track_version'] . ' ' .
            $row['track_version_extra']
        );

        // Example item URL
        $itemLink = $siteLink . "track.php?id=" . (int)$row['id'];

        // Description content
        $description = "
Author: {$row['track_author']}<br>
Editor: {$row['track_editor']}<br>
Family: {$row['track_family']}<br>
Clan: {$row['track_clan']}<br>
Music: {$row['track_music']}<br>
Properties: {$row['track_prop']}<br>
";

        // Publication date (fallback if empty)
        $pubDate = !empty($row['revision'])
            ? date(DATE_RSS, strtotime($row['revision']))
            : date(DATE_RSS);
        ?>
        <item>
            <title><?= htmlspecialchars($title) ?></title>
            <link><?= htmlspecialchars($itemLink) ?></link>
            <guid isPermaLink="true"><?= htmlspecialchars($itemLink) ?></guid>
            <description><![CDATA[<?= $description ?>]]></description>
            <pubDate><?= $pubDate ?></pubDate>
        </item>
        <?php
    }
}
?>

</channel>
</rss>
