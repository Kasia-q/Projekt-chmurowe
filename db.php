<?php
$servername = "listazadan-db.mysql.database.azure.com";
$username = "adminuser";
$password = "Password123";
$dbname = "task_manager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
