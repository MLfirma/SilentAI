<?php
$conn = new mysqli("localhost", "root", "", "concern_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>