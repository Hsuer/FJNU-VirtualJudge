<?php
$conn = new mysqli($DB_CONN['server'], $DB_CONN['username'], $DB_CONN['password'], $DB_CONN['database']);
if ($conn->connect_error) {
    die("Connect failed: " . $conn->connect_error);
} 
$conn->set_charset("utf8");
//MUST SET mysqli.reconnect = On
?>