<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Financial'){
    header("Location: ../login.php");
    exit;
}

$dept = $_GET['dept'] ?? '';

if(empty($dept)){
    die("Invalid Department");
}

/* FETCH BASED SA ASSIGNED */
$stmt = $conn->prepare("
    SELECT * FROM concerns 
    WHERE category='Financial' AND assigned_to=?
    ORDER BY updated_at DESC
");
$stmt->bind_param("s", $dept);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $dept ?> Concerns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>💰 <?= $dept ?> Concerns</h3>

<a href="financial_dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

<table class="table table-bordered">

<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Concern</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = $res->fetch_assoc()): ?>

<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['student_name'] ?></td>
    <td><?= $row['description'] ?></td>
    <td><?= $row['status'] ?></td>

    <td>
        <!-- ✅ FIXED VIEW BUTTON -->
        <a href="../admin/view_concern.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
            View
        </a>
    </td>
</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</body>
</html>