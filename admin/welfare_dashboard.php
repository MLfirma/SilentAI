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
   FILTER
========================= */
$filter = $_GET['filter'] ?? 'all';

/* =========================
   BASE QUERY
========================= */
$sql = "SELECT * FROM concerns WHERE category='Welfare'";

if($filter == 'new'){
    $sql .= " AND status='Submitted'";
}
elseif($filter == 'pending'){
    $sql .= " AND status='Read'";
}
elseif($filter == 'resolved'){
    $sql .= " AND status='Resolved'";
}
elseif($filter == 'escalated'){
    $sql .= " AND status='Escalated'";
}

$sql .= " ORDER BY created_at DESC";
$res = $conn->query($sql);

/* =========================
   COUNTERS
========================= */
$newCount = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Welfare' AND status='Submitted'")->fetch_assoc()['c'];
$pendingCount = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Welfare' AND status='Read'")->fetch_assoc()['c'];
$resolvedCount = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Welfare' AND status='Resolved'")->fetch_assoc()['c'];
$escCount = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Welfare' AND status='Escalated'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welfare Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }

        .card-box {
            background:white;
            border-radius:12px;
            padding:15px;
            box-shadow:0 3px 8px rgba(0,0,0,0.1);
        }

        .dept-btn{
            border-radius:10px;
            padding:12px;
            font-weight:bold;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>🧑‍⚕️ Welfare Dashboard</h3>

        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

    <!-- =========================
        DEPARTMENT ROUTING BUTTONS
    ========================= -->
    <div class="card-box mb-3">
        <h5>🏢 Assign To Department</h5>

        <div class="row g-2 mt-2">

            <div class="col-md-4">
                <a href="dept_welfare.php?dept=OSA" class="btn btn-primary w-100 dept-btn">
                    OSA
                </a>
            </div>

            <div class="col-md-4">
                <a href="dept_welfare.php?dept=Guidance" class="btn btn-warning w-100 dept-btn">
                    Guidance
                </a>
            </div>

            <div class="col-md-4">
                <a href="dept_welfare.php?dept=Clinic" class="btn btn-success w-100 dept-btn">
                    Clinic
                </a>
            </div>

        </div>
    </div>

    <!-- =========================
        COUNTERS
    ========================= -->
    <div class="row mb-3">

        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>New</h6>
                <h4><?= $newCount ?></h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Pending</h6>
                <h4><?= $pendingCount ?></h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Resolved</h6>
                <h4><?= $resolvedCount ?></h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Escalated</h6>
                <h4><?= $escCount ?></h4>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="mb-3">
        <a href="?filter=all" class="btn btn-secondary btn-sm">All</a>
        <a href="?filter=new" class="btn btn-primary btn-sm">New</a>
        <a href="?filter=pending" class="btn btn-warning btn-sm">Pending</a>
        <a href="?filter=resolved" class="btn btn-success btn-sm">Resolved</a>
        <a href="?filter=escalated" class="btn btn-danger btn-sm">Escalated</a>
    </div>

    <!-- =========================
        TABLE
    ========================= -->
    <div class="card-box">

        <table class="table table-bordered table-hover">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Concern</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = $res->fetch_assoc()): ?>

            <?php
                $assigned = !empty($row['assigned_to']) ? $row['assigned_to'] : 'Not Assigned';
            ?>

            <tr>

                <td><?= $row['id'] ?></td>
                <td><?= $row['student_name'] ?></td>
                <td><?= $row['description'] ?></td>

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

                <td>
                    <span class="badge bg-info">
                        <?= $assigned ?>
                    </span>
                </td>

                <td><?= $row['created_at'] ?></td>

                <td>
                    <a href="view_concern.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
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