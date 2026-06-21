<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /../');
    exit();
}

require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; // Top Login Button

$id = isset($_GET['id']) ? (int)$_GET['id'] : -1;
$row2 = [
    'dist_id' => 0,
    'dist_name' => '',
    'dist_version' => '',
    'dist_author' => '',
    'dist_release' => '',
    'dist_pre' => '',
    'dist_suc' => '',
    'dist_region' => '',
    'dist_url' => '',
    'dist_info' => '',
];

if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM Distribution WHERE dist_id = ? LIMIT 1');
    if (!$stmt) {
        die('Preparation failed: (' . $conn->errno . ') ' . $conn->error);
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result2 = $stmt->get_result();

    if ($result2 && $result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
    } else {
        $row2['dist_id'] = $id;
    }

    $stmt->close();
} else {
    $result24 = $conn->query('SELECT COALESCE(MAX(dist_id), 0) + 1 AS next_dist_id FROM Distribution');
    if ($result24) {
        $max_id = $result24->fetch_assoc();
        $row2['dist_id'] = (int)$max_id['next_dist_id'];
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
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
<form method="post" action="scripts/submitteddistribution.php">
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
                <td><input id="submitbox" type="text" name="dist_name" value="<?php echo e($row2['dist_name']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_version" value="<?php echo e($row2['dist_version']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_author" value="<?php echo e($row2['dist_author']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_release" value="<?php echo e($row2['dist_release']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_pre" value="<?php echo e($row2['dist_pre']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_suc" value="<?php echo e($row2['dist_suc']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_region" value="<?php echo e($row2['dist_region']); ?>"></td>
                <td><input id="submitbox" type="text" name="dist_url" value="<?php echo e($row2['dist_url']); ?>"></td>
                <td><textarea id="submitbox" name="dist_info"><?php echo e($row2['dist_info']); ?></textarea></td>
            </tr>
        </tbody>
    </table>
    <input id="submitbox" type="hidden" name="dist_id" value="<?php echo (int)$row2['dist_id']; ?>">
    <br>
    <input type="submit" name="Submit" id="Submit" value="Submit">
    <br><br>
</form>
</div>
</body>
</html>
