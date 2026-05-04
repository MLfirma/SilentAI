<?php
include("../config/db.php");

$id = $_GET['id'];
$status = $_GET['status'];

$conn->query("UPDATE concerns SET status='$status', updated_at=NOW() WHERE id=$id");

$conn->query("INSERT INTO audit_logs (concern_id, action, actor)
VALUES ($id, 'Status changed to $status', 'Admin')");

echo "Updated!";
?>