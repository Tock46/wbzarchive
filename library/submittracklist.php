<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /../');
    exit();
}

require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php';

$tracktype = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($tracktype <= 0) {
    header('Location: /../');
    exit();
}

$distCheck = $conn->prepare('SELECT 1 FROM Distribution WHERE dist_id = ? LIMIT 1');
if (!$distCheck) {
    header('Location: /../');
    exit();
}
$distCheck->bind_param('i', $tracktype);
if (!$distCheck->execute()) {
    header('Location: /../');
    exit();
}
$distCheck->store_result();
if ($distCheck->num_rows === 0) {
    $distCheck->close();
    header('Location: /../');
    exit();
}
$distCheck->close();
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./style.css">
    <title>SZS Library - Submit Distribution Track List</title>
</head>
<body>
    <div id="container">
        <a href='/?type=files'><h1>SZS Library - Admin</h1></a>

        <h2>Submit Distribution Track List</h2>

        <form method="post" action="scripts/submittedtracklist.php?id=<?php echo urlencode((string) $tracktype); ?>" enctype="multipart/form-data">
            <table>
                <tbody>
                    <tr>
                        <th style="padding-left: 0px; text-align: center;">Distribution ID</th>
                        <td><?php echo htmlspecialchars((string) $tracktype, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <th style="padding-left: 0px; text-align: center;">TXT file</th>
                        <td>
                            <input id="submitbox" type="file" name="tracklist" accept=".txt,text/plain" required>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <input type="submit" name="Submit" id="Submit" value="Upload Track List">
        </form>
		<br>
    </div>
<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/footer.php'; // Bottom Footer
?>
</body>
</html>
