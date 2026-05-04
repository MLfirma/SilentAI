<?php
session_start();
include("../config/db.php");

/* =========================
   AUTH CHECK
========================= */
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Welfare'){
    header("Location: ../login.php");
    exit;
}

/* =========================
   GET DEPARTMENT
========================= */
$dept = $_GET['dept'] ?? '';

if(empty($dept)){
    echo "No department selected.";
    exit;
}

/* =========================
   SAFE QUERY (WELFARE ONLY)
========================= */
$stmt = $conn->prepare("
    SELECT * FROM concerns 
    WHERE category='Welfare' AND assigned_to=?
    ORDER BY created_at DESC
");

$stmt->bind_param("s", $dept);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($dept) ?> - Welfare Department</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }

        .card-box {
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 3px 10px rgba(0,0,0,0.1);
        }

        .dept-title {
            font-weight:bold;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- BACK BUTTON -->
    <a href="welfare_dashboard.php" class="btn btn-secondary btn-sm mb-3">
        ⬅ Back to Welfare Dashboard
    </a>

    <h3 class="dept-title">🏢 <?= htmlspecialchars($dept) ?> Department</h3>

    <!-- TABLE -->
    <div class="card-box mt-3">

        <table class="table table-bordered table-hover">

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

            <?php if($res->num_rows > 0): ?>
                <?php while($row = $res->fetch_assoc()): ?>

                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>

                    <td>
                        <?php if($row['status']=='Submitted'): ?>
                            <span class="badge bg-primary">New</span>
                        <?php elseif($row['status']=='Read'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php elseif($row['status']=='Resolved'): ?>
                            <span class="badge bg-success">Resolved</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Escalated</span>
                        <?php endif; ?>
                    </td>

                    <td><?= $row['created_at'] ?></td>

                    <td>
                        <a href="view_concern.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
                            View
                        </a>
                    </td>
                </tr>

                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No concerns assigned to this department yet.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>