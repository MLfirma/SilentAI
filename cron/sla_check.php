<?php
include("../config/db.php");

echo "<h3>SLA Check Running...</h3>";

// 1. Escalate Submitted > 2 days
$result1 = $conn->query("
UPDATE concerns 
SET status = 'Escalated', updated_at = NOW()
WHERE status = 'Submitted'
AND TIMESTAMPDIFF(DAY, created_at, NOW()) > 2
");

echo "Submitted → Escalated: " . $conn->affected_rows . "<br>";

// 2. Escalate Read > 5 days without screening
$result2 = $conn->query("
UPDATE concerns 
SET status = 'Escalated', updated_at = NOW()
WHERE status = 'Read'
AND TIMESTAMPDIFF(DAY, updated_at, NOW()) > 5
");

echo "Read → Escalated: " . $conn->affected_rows . "<br>";

echo "<br>SLA check done.";
?>