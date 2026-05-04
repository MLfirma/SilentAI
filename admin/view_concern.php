<?php
session_start();
include("../config/db.php");

/* AUTH CHECK */
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$user = $_SESSION['user'];

/* SAFE ID CHECK (ADDED FIX) */
if($id <= 0){
    die("Invalid Request");
}

/* GET CONCERN */
$stmt = $conn->prepare("SELECT * FROM concerns WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$concern = $stmt->get_result()->fetch_assoc();

if(!$concern){
    die("Concern not found");
}

/* =========================
   FIXED ACCESS CONTROL (ADDED FIX)
   - prevents Cashier/Financial VIEW ERROR
========================= */
if(
    $user['role'] != $concern['category'] 
    && $user['role'] != 'Financial'
    && $user['role'] != 'Welfare'
    && $user['role'] != 'Academic'
){
    die("Access Denied");
}

/* =========================
   UPDATE STATUS
========================= */
if(isset($_POST['status_update'])){

    $status = $_POST['status'];

    $stmt = $conn->prepare("
        UPDATE concerns 
        SET status=?, updated_at=NOW()
        WHERE id=?
    ");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    echo "<script>alert('Status Updated'); location.href='view_concern.php?id=$id';</script>";
}

/* =========================
   COMMENT
========================= */
if(isset($_POST['comment'])){

    $comment = trim($_POST['comment']);

    if(!empty($comment)){

        $stmt = $conn->prepare("
            INSERT INTO responses (concern_id, department, message, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("iss", $id, $user['role'], $comment);
        $stmt->execute();
    }

    echo "<script>alert('Comment Added'); location.href='view_concern.php?id=$id';</script>";
}

/* =========================
   ASSIGN ACADEMIC
========================= */
if(isset($_POST['assign_to']) && $user['role'] == 'Academic'){

    $assign = $_POST['assign'];

    $stmt = $conn->prepare("
        UPDATE concerns 
        SET assigned_to=?, updated_at=NOW()
        WHERE id=?
    ");
    $stmt->bind_param("si", $assign, $id);
    $stmt->execute();

    echo "<script>alert('Assigned to $assign'); location.href='view_concern.php?id=$id';</script>";
}

/* =========================
   ASSIGN WELFARE
========================= */
if(isset($_POST['assign_to']) && $user['role'] == 'Welfare'){

    $assign = $_POST['assign'];

    $allowed = ['OSA','Guidance','Clinic'];

    if(!in_array($assign, $allowed)){
        die("Invalid Department");
    }

    $stmt = $conn->prepare("
        UPDATE concerns 
        SET assigned_to=?, updated_at=NOW()
        WHERE id=?
    ");
    $stmt->bind_param("si", $assign, $id);
    $stmt->execute();

    echo "<script>alert('Assigned to $assign'); location.href='view_concern.php?id=$id';</script>";
}

/* =========================
   ASSIGN FINANCIAL
========================= */
if(isset($_POST['assign_to']) && $user['role'] == 'Financial'){

    $assign = $_POST['assign'];

    $allowed = ['Cashier','Accounting','Scholarship'];

    if(!in_array($assign, $allowed)){
        die("Invalid Department");
    }

    $stmt = $conn->prepare("
        UPDATE concerns 
        SET assigned_to=?, updated_at=NOW()
        WHERE id=?
    ");
    $stmt->bind_param("si", $assign, $id);
    $stmt->execute();

    echo "<script>alert('Assigned to $assign'); location.href='view_concern.php?id=$id';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Concern Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }

        .card-box {
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 3px 10px rgba(0,0,0,0.1);
            margin-bottom:15px;
        }
    </style>
</head>

<body>

<div class="container mt-4">

<!-- =========================
   FIXED NAVIGATION LINKS (IMPORTANT FIX)
========================= -->
<div class="mb-3 d-flex gap-2 flex-wrap">

    <a href="<?= strtolower($user['role']) ?>_dashboard.php"
       class="btn btn-secondary btn-sm">
        ⬅ Back
    </a>

    <!-- FIXED PATH SAFETY -->
    <a href="academic_dashboard.php" class="btn btn-primary btn-sm">📘 Academic</a>
    <a href="welfare_dashboard.php" class="btn btn-success btn-sm">🏥 Welfare</a>
    <a href="financial_dashboard.php" class="btn btn-warning btn-sm">💰 Financial</a>

</div>

<h3>📌 Concern Details (<?= $user['role'] ?>)</h3>

<!-- INFO -->
<div class="card-box">

    <p><b>Student:</b> <?= $concern['student_name'] ?></p>
    <p><b>Concern:</b> <?= $concern['description'] ?></p>
    <p><b>Category:</b> <?= $concern['category'] ?></p>

    <p><b>Main Department:</b>
        <span class="badge bg-secondary">
            <?= $concern['department'] ?>
        </span>
    </p>

    <p><b>Assigned To:</b>
        <span class="badge bg-info">
            <?= $concern['assigned_to'] ?? 'Not Assigned' ?>
        </span>
    </p>

    <p><b>Status:</b>
        <span class="badge bg-primary"><?= $concern['status'] ?></span>
    </p>

</div>

<!-- ACADEMIC -->
<?php if($user['role']=='Academic'): ?>
<div class="card-box">

<h5>📌 Assign Academic</h5>

<form method="POST" class="d-flex gap-2">

<select name="assign" class="form-select" required>
    <option value="">Select</option>
    <option value="Registrar">Registrar</option>
    <option value="Dean's Office">Dean's Office</option>
    <option value="IT">IT</option>
    <option value="HR">HR</option>
    <option value="BSA">BSA</option>
    <option value="EDUC">EDUC</option>
</select>

<button name="assign_to" class="btn btn-dark">Assign</button>

</form>

</div>
<?php endif; ?>

<!-- WELFARE -->
<?php if($user['role']=='Welfare'): ?>
<div class="card-box">

<h5>🏥 Assign Welfare</h5>

<form method="POST" class="d-flex gap-2">

<select name="assign" class="form-select">
    <option value="">Select</option>
    <option value="OSA">OSA</option>
    <option value="Guidance">Guidance</option>
    <option value="Clinic">Clinic</option>
</select>

<button name="assign_to" class="btn btn-success">Assign</button>

</form>

</div>
<?php endif; ?>

<!-- FINANCIAL -->
<?php if($user['role']=='Financial'): ?>
<div class="card-box">

<h5>💰 Assign Financial</h5>

<form method="POST" class="d-flex gap-2">

<select name="assign" class="form-select">
    <option value="">Select</option>
    <option value="Cashier">Cashier</option>
    <option value="Accounting">Accounting</option>
    <option value="Scholarship">Scholarship</option>
</select>

<button name="assign_to" class="btn btn-warning">Assign</button>

</form>

</div>
<?php endif; ?>

<!-- STATUS -->
<div class="card-box">

<h5>🔄 Update Status</h5>

<form method="POST" class="d-flex gap-2">

<select name="status" class="form-select">
    <option value="Submitted">Submitted</option>
    <option value="Read">Read</option>
    <option value="Resolved">Resolved</option>
    <option value="Escalated">Escalated</option>
</select>

<button name="status_update" class="btn btn-success">Update</button>

</form>

</div>

<!-- COMMENT -->
<div class="card-box">

<h5>💬 Comment</h5>

<form method="POST">

<textarea name="comment" class="form-control mb-2"></textarea>

<button class="btn btn-primary">Submit</button>

</form>

</div>

<!-- HISTORY -->
<div class="card-box">

<h5>📄 History</h5>

<?php
$res = $conn->query("SELECT * FROM responses WHERE concern_id=$id ORDER BY created_at DESC");

if($res){
    while($r=$res->fetch_assoc()):
?>

<div class="border p-2 mb-2 rounded">
    <b><?= $r['department'] ?>:</b> <?= $r['message'] ?>
    <br>
    <small><?= $r['created_at'] ?></small>
</div>

<?php endwhile; } ?>

</div>

</div>

</body>
</html>