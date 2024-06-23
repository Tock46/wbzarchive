<?php // All the variable checks
 $searchString = NULL;
if (!isset($_POST['search'])) {

	if (isset($_GET['s'])){
	$searchString = $_GET['s'];
	}
	
	else if (isset($_POST['search'])){
	$searchString = $_POST['search'];
	}
}

else {
$searchString = mysqli_real_escape_string($conn, trim(htmlentities($_POST['search'])));
}

$ct = "0";
if (isset($_POST['btnControl1'])) {
$ct = "1";
}
else if (isset($_GET['ct'])){
if ($_GET['ct'] === "1"){
$ct = "1";
}
}

$ca = "0";
if (isset($_POST['btnControl2'])) {
$ca = "1";
}
else if (isset($_GET['ca'])){
if ($_GET['ca'] === "1"){
$ca = "1";
}
}

$tc = "0";
if (isset($_POST['btnControl3'])) {
$tc = "1";
}
else if (isset($_GET['tc'])){
if ($_GET['tc'] === "1"){
$tc = "1";
}
}

$th = "0";
if (isset($_POST['btnControl4'])) {
$th = "1";
}
else if (isset($_GET['th'])){
if ($_GET['th'] === "1"){
$th = "1";
}
}

$cc = "0";
if (isset($_POST['btnControl5'])) {
$cc = "1";
}
else if (isset($_GET['cc'])){
if ($_GET['cc'] === "1"){
$cc = "1";
}
}

$cv = "0";
if (isset($_POST['btnControl6'])) {
$cv = "1";
}
else if (isset($_GET['cv'])){
if ($_GET['cv'] === "1"){
$cv = "1";
}
}

$cm = "0";
if (isset($_POST['btnControl7'])) {
$cm = "1";
}
else if (isset($_GET['cm'])){
if ($_GET['cm'] === "1"){
$cm = "1";
}
}

$pe = "0";
if (isset($_POST['btnControl8'])) {
$pe = "1";
}
else if (isset($_GET['pe'])){
if ($_GET['pe'] === "1"){
$pe = "1";
}
}

$missingfiles = "0";
if (isset($_GET['missingfiles'])){
if ($_GET['missingfiles'] === "1"){
$missingfiles = "1";
}
}

$page = 1;
if (isset($_GET['page'])){
$page = (int)$_GET['page'];
}

// Check if 'clan' exists and is an integer
if (isset($_GET['clan']) && is_numeric($_GET['clan'])) {
    // Cast the value to an integer
    $clan = (int) $_GET['clan'];
} else {
    // Handle the case where 'clan' is not set or not an integer
    (int) $clan = 0; // Default value or handle error
}

// Check if 'type' exists and is an integer
if (isset($_GET['type']) && is_numeric($_GET['type'])) {
    // Cast the value to an integer
    $type = (int) $_GET['type'];
} else {
    // Handle the case where 'type' is not set or not an integer
    (int) $type = 0; // Default value or handle error
}

$pagelimit = $page * 20;
$pagelimit = $pagelimit -20;
$npage = $page+1;
$ppage = $page-1;

if ($page < 2){
$page = 1;
$ppage = 1;
$npage = 2;
}

?>

	<form action="index.php" method="post">
<input type="checkbox" <?php if($ct === "1") echo "checked"; ?> name="btnControl1" id="btnControl1"/><label class="btn1" for="btnControl1">Custom Track</label><?php
?><input type="checkbox" <?php if($ca === "1") echo "checked"; ?> name="btnControl2" id="btnControl2"/><label class="btn2" for="btnControl2">Custom Arena</label><?php
?><input type="checkbox" <?php if($tc === "1") echo "checked"; ?> name="btnControl3" id="btnControl3"/><label class="btn3" for="btnControl3">Track Change</label><?php
?><input type="checkbox" <?php if($th === "1") echo "checked"; ?> name="btnControl4" id="btnControl4"/><label class="btn4" for="btnControl4">Texture Hack</label><br><?php
?><input type="checkbox" <?php if($cc === "1") echo "checked"; ?> name="btnControl5" id="btnControl5"/><label class="btn5" for="btnControl5">Custom Character</label><?php
?><input type="checkbox" <?php if($cv === "1") echo "checked"; ?> name="btnControl6" id="btnControl6"/><label class="btn6" for="btnControl6">Custom Vehicle</label><?php
?><input type="checkbox" <?php if($cm === "1") echo "checked"; ?> name="btnControl7" id="btnControl7"/><label class="btn7" for="btnControl7">Custom Competition</label><?php
?><input type="checkbox" <?php if($pe === "1") echo "checked"; ?> name="btnControl8" id="btnControl8"/><label class="btn8" for="btnControl8">PEGI 18 Custom Tracks</label>
	<input id="searchbox" type="text" placeholder=<?php echo("Search...")?> name="search" required><button id="searchbutton" type="submit" name="submit">Search</button>
	</form>