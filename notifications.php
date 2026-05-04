<?php
include("../config/db.php");

$count = $conn->query("
    SELECT COUNT(*) as c 
    FROM concerns 
    WHERE status='Submitted'
")->fetch_assoc()['c'];

echo $count;
?>