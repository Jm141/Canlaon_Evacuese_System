<?php
$server = "localhost";
$user ="u651277261_evac";
$password = "s5CdWGsG?5M";
$db = "u651277261_evac";
// $port = "21";

$conn = mysqli_connect($server, $user, $password, $db);

if (!$conn) {
    die("Connection failed: ". mysqli_connect_error());
}else{
   
}

?>