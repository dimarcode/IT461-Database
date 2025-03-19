
<!-- this is an example connect file. You must remove "example" from the beginning of this file.
Once the file reads just connect.php, replace the values appropriately for $db_server, etc.-->

<?php
$db_server = "<db_server>";
$db_user = "<db_user>";
$db_pass = "<db_pass>";
$db_name = "<db_name>";

$connect = mysqli_connect($db_server, $db_user, $db_pass, $db_name) or die("Database connection error");///////////////
mysqli_query($connect,"set names 'utf8'");/////////////////////////////////////
date_default_timezone_set('America/New_York');