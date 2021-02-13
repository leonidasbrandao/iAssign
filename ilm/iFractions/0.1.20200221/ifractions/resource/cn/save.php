 <?php

$servername = "localhost";
$username = "jrustler_uscore";
$password = "12345";
$dbname = "jrustler_fscore";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ip = $_REQUEST["s_ip"];
$play = $_REQUEST["s_name"];
$date = date("Y-m-d H:i:s");
$lang = $_REQUEST["s_lang"];
$game = $_REQUEST["s_game"];
$mode = $_REQUEST["s_mode"];
$oper = $_REQUEST["s_oper"];
$leve = $_REQUEST["s_leve"];
$posi = $_REQUEST["s_posi"];
$resu = $_REQUEST["s_resu"];
$time = $_REQUEST["s_time"];
$deta = $_REQUEST["s_deta"];


$sql = "INSERT INTO `score`
(`s_hostip`,
`s_playername`,
`s_datetime`,
`s_lang`,
`s_game`,
`s_mode`,
`s_operator`,
`s_level`,
`s_mappos`,
`s_result`,
`s_time`,
`s_details`)
VALUES(
'$ip',
'$play',
'$date',
'$lang',
'$game',
'$mode',
'$oper',
$leve,
$posi,
'$resu',
$time,
'$deta')";

/*if ($conn->query($sql) === TRUE) {
    echo "Grabado";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();*/
?> 