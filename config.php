<?php
$servername = "localhost";
$username = "root";
$password = "1131211";
$dbname = "junglecoin";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    //echo ("SUCCESS");
}
?>
