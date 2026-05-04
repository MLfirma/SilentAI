<?php
session_start();
include("../config/db.php");

/* AUTH */
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Academic'){
    header("Location: ../login.php");
    exit;
}

$dept = $_GET['dept'] ?? '';

/* SAFE QUERY */
$stmt = $conn->prepare("
    SELECT * FROM concerns 
    WHERE category='Academic' AND assigned_to=?
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $dept);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $dept ?> Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }
        .card-box {
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 3px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- BACK -->
    <a href="academic_dashboard.php" class="btn btn-secondary btn-sm mb-3">
        ⬅ Back to Academic Dashboard
    </a>

    <h3>🏢 <?= $dept ?> Department</h3>

    <div class="card-box mt-3">

        <table class="table table-bordered">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Concern</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = $res->fetch_assoc()): ?>

            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['student_name'] ?></td>
                <td><?= $row['description'] ?></td>

                <td>
                    <span class="badge bg-primary">
                        <?= $row['status'] ?>
                    </span>
                </td>

                <td><?= $row['created_at'] ?></td>

                <td>
                    <a href="view_concern.php?id=<?= $row['id'] ?>"
                       class="btn btn-info btn-sm">
                       View
                    </a>
                </td>
            </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>