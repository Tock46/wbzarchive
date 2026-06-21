 <?php // Top Login Button
 
// Check if the 'remember_me' cookie exists
if (isset($_COOKIE['remember_me']) && !isset($_SESSION['loggedin'])) {
    list($userId, $token) = explode(':', $_COOKIE['remember_me']);

    // Look up the token in the database
    if ($stmt = $conn->prepare('SELECT remember_token, username FROM accounts WHERE id = ?')) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($remember_token, $username);
            $stmt->fetch();

            // Verify if the token matches the stored token in the database
            if (hash_equals($remember_token, $token)) {
                // Token is valid, log the user in automatically
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $username;
                $_SESSION['id'] = $userId;

                echo 'Welcome back, ' . $_SESSION['name'] . '!';
            }
        }

        $stmt->close();
    }
}

if (!isset($_SESSION['loggedin'])) {
 echo '<a href="/login.php"><button class="loginbutton"><h2>Login</h2></button></a><br><br>';
}
 else {
 echo '<a href="/scripts/logout.php"><button class="loginbutton"><h2>Logout</h2></button></a><a href="/submit.php"><button class="loginbutton"><h2>Submit</h2></button></a><a href="/submitdistribution.php"><button class="loginbutton"><h2>Submit Distribution</h2></button></a><br><br>';
 }
$track_slot2 = [
    "-4.2 -6.1 -6.2" => "Moonview Highway unsupported<br>N64 Sherbet Land unsupported<br>GBA Shy Guy Beach unsupported",
    "+6.2" => "GBA Shy Guy Beach required",
    "-6.1 -6.2" => "Moonview Highway required",
    "-4.2 -6.2" => "Moonview Highway unsupported<br>GBA Shy Guy Beach unsupported",
    "+6.1" => "N64 Sherbet Land required",
    "+3.1 +7.1" => "Daisy Circuit recommended<br>DS Desert Hills recommended",
    "ARENA" => "ARENA",
    "none" => "None",
];

	$track_names = [
            "8" => "Luigi Circuit",
            "1" => "Moo Moo Meadows",
            "2" => "Mushroom Gorge",
            "4" => "Toad's Factory",
            "0" => "Mario Circuit",
            "5" => "Coconut Mall",
            "6" => "DK Summit",
            "7" => "Wario's Gold Mine",
            "9" => "Daisy Circuit",
            "15" => "Koopa Cape",
            "11" => "Maple Treeway",
            "3" => "Grumble Volcano",
            "14" => "Dry Dry Ruins",
            "10" => "Moonview Highway",
            "12" => "Bowser's Castle",
            "13" => "Rainbow Road",
            "16" => "GCN Peach Beach",
            "20" => "DS Yoshi Falls",
            "25" => "SNES Ghost Valley 2",
            "26" => "N64 Mario Raceway",
            "27" => "N64 Sherbet Land",
            "31" => "GBA Shy Guy Beach",
            "23" => "DS Delfino Square",
            "18" => "GCN Waluigi Stadium",
            "21" => "DS Desert Hills",
            "30" => "GBA Bowser Castle 3",
            "29" => "N64 DK's Jungle Parkway",
            "17" => "GCN Mario Circuit",
            "24" => "SNES Mario Circuit 3",
            "22" => "DS Peach Gardens",
            "19" => "GCN DK Mountain",
            "28" => "N64 Bowser's Castle",
            "33" => "Block Plaza",
            "32" => "Delfino Pier",
            "35" => "Funky Stadium",
            "34" => "Chain Chomp Wheel",
            "36" => "Thwomp Desert",
            "39" => "SNES Battle Course 4",
            "40" => "GBA Battle Course 3",
            "41" => "N64 Skyscraper",
            "37" => "GCN Cookie Land",
            "38" => "DS Twilight House",
            "" => "None",
        ];

	$track_music2 = [
			"117" => "Luigi Circuit (LC, T11)",
			"119" => "Moo Moo Meadows (MMM, T12)",
			"121" => "Mushroom Gorge (MG, T13)",
			"123" => "Toad's Factory (TF, T14)",
			"125" => "Mario Circuit (MC, T21)",
			"127" => "Coconut Mall (CM, T22)",
			"129" => "DK Summit (DKS, T23)",
			"131" => "Wario's Gold Mine (WGM, T24)",
			"135" => "Daisy Circuit (DC, T31)",
			"133" => "Koopa Cape (KC, T32)",
			"143" => "Maple Treeway (MT, T33)",
			"139" => "Grumble Volcano (GV, T34)",
			"137" => "Dry Dry Ruins (DDR, T41)",
			"141" => "Moonview Highway (MH, T42)",
			"145" => "Bowser's Castle (BC, T43)",
			"147" => "Rainbow Road (RR, T44)",
			"165" => "GCN Peach Beach (gPB, T51)",
			"173" => "DS Yoshi Falls (dYF, T52)",
			"151" => "SNES Ghost Valley 2 (sGV2, T53)",
			"159" => "N64 Mario Raceway (nMR, T54)",
			"157" => "N64 Sherbet Land (nSL, T61)",
			"149" => "GBA Shy Guy Beach (gSGB, T62)",
			"175" => "DS Delfino Square (dDS, T63)",
			"169" => "GCN Waluigi Stadium (gWS, T64)",
			"177" => "DS Desert Hills (dDH, T71)",
			"155" => "GBA Bowser Castle 3 (gBC3, T72)",
			"161" => "N64 DK's Jungle Parkway (nDKJP, T73)",
			"167" => "GCN Mario Circuit (gMC, T74)",
			"153" => "SNES Mario Circuit 3 (sMC3, T81)",
			"179" => "DS Peach Gardens (dPG, T82)",
			"171" => "GCN DK Mountain (gDKM, T83)",
			"163" => "N64 Bowser's Castle (nBC, T84)",
			"183" => "Block Plaza (aBP, A11)",
			"181" => "Delfino Pier (aDP, A12)",
			"185" => "Funky Stadium (aFS, A13)",
			"187" => "Chain Chomp Wheel (aCCW, A14)",
			"189" => "Thwomp Desert (aTD, A15)",
			"195" => "SNES Battle Course 4 (asBC4, A21)",
			"197" => "GBA Battle Course 3 (agBC3, A22)",
			"199" => "N64 Skyscraper (anSS, A23)",
			"191" => "GCN Cookie Land (agCL, A24)",
			"193" => "DS Twilight House (adTH, A25)",
			"54" => "Galaxy Colosseum",
            "" => "None",
	];
?> 