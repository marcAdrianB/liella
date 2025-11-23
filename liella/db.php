<?php
require_once "init_db.php";  // AUTO-CREATE DB + TABLES

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "liella_events_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
