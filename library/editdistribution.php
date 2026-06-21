<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: /');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /');
    exit;
}

$dist_id = (int) $_GET['id'];

$sql = "SELECT dist_id, dist_name, dist_version, dist_author, dist_release, dist_pre, dist_suc, dist_region, dist_url, dist_info 
        FROM Distribution 
        WHERE dist_id=? 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $dist_id);
$stmt->execute();
$stmt->store_result();

$stmt->bind_result(
    $dist_id, $dist_name, $dist_version, $dist_author, $dist_release,
    $dist_pre, $dist_suc, $dist_region, $dist_url, $dist_info
);

$stmt->fetch();
?>

<!doctype html>
<html>
<head>
   <meta charset="UTF-8">
   <link rel="stylesheet" href="./style.css">
   <title>SZS Library - Admin</title>
</head>
<body>

<?php require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; ?>

<div id="container">
<form method="post" action="scripts/editteddistribution.php">
    <a href='/'><h1>SZS Library - Admin</h1></a>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th style="width: 76px">Version</th>
                <th>Author</th>
				<th style="width: 76px">Date</th>
                <th style="width: 76px">Predecessor</th>
                <th style="width: 76px">Successor</th>
                <th style="width: 76px">Region</th>
                <th>URL</th>
                <th>Info</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input id="submitbox" type="text" name="dist_name" value="<?php echo htmlspecialchars($dist_name); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_version" value="<?php echo htmlspecialchars($dist_version); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_author" value="<?php echo htmlspecialchars($dist_author); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_release" value="<?php echo htmlspecialchars($dist_release); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_pre" value="<?php echo htmlspecialchars($dist_pre); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_suc" value="<?php echo htmlspecialchars($dist_suc); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_region" value="<?php echo htmlspecialchars($dist_region); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_url" value="<?php echo htmlspecialchars($dist_url); ?>"></td>
                <td><textarea id="submitbox" name="dist_info"><?php echo htmlspecialchars($dist_info); ?></textarea></td>
            </tr>
        </tbody>
    </table>
    <input id="submitbox" type="hidden" name="dist_id" value="<?php echo (int)$dist_id; ?>">
    <br>
    <input type="submit" name="Submit" id="Submit" value="Submit">
    <br><br>
</form>
</div>

</body>
</html>
