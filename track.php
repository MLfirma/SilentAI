<?php
include("config/db.php");

$concern = null;
$error = null;

/* =========================
   TRACK PROCESS (SECURE)
========================= */
if(isset($_POST['track'])){

    $id = $_POST['ticket_id'];
    $email = $_POST['email'];

    if(empty($id) || empty($email)){
        $error = "Please enter Ticket ID and Email";
    } else {

        $stmt = $conn->prepare("SELECT * FROM concerns WHERE id=? AND email=?");
        $stmt->bind_param("is", $id, $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $concern = $result->fetch_assoc();
        } else {
            $error = "No ticket found or email does not match.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Concern</title>

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

        .progress {
            height: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container mt-5" style="max-width:700px;">

<!-- TRACK FORM -->
<div class="card-box">

<h3 class="text-center">🔍 Track Your Concern</h3>

<form method="POST">

<input type="number" name="ticket_id" class="form-control mb-2" placeholder="Ticket ID" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email used in submission" required>

<button name="track" class="btn btn-primary w-100">Track</button>

</form>

</div>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if($concern): ?>

<!-- INFO -->
<div class="card-box">

<h5>📌 Ticket Information</h5>

<p><b>Ticket ID:</b> <?= $concern['id'] ?></p>
<p><b>Name:</b> <?= $concern['student_name'] ?></p>
<p><b>Email:</b> <?= $concern['email'] ?></p>
<p><b>Category:</b> <?= $concern['category'] ?></p>
<p><b>Department:</b> <?= $concern['department'] ?></p>

<p><b>Status:</b>
<span class="badge bg-primary"><?= $concern['status'] ?></span>
</p>

<p><b>Assigned To:</b>
<span class="badge bg-info"><?= $concern['assigned_to'] ?? 'Not Assigned' ?></span>
</p>

</div>

<!-- TIMELINE PROGRESS -->
<div class="card-box">

<h5>📊 Progress Tracking</h5>

<?php
$steps = ["Submitted", "Read", "Processing", "Escalated", "Resolved"];
$current = $concern['status'];
$total = count($steps);
?>

<div class="progress mb-3">

<?php foreach($steps as $step): ?>

<?php
$width = 100 / $total;

if($step == $current){
    $color = "bg-primary";
} elseif(array_search($step, $steps) < array_search($current, $steps)){
    $color = "bg-success";
} else {
    $color = "bg-secondary";
}
?>

<div class="progress-bar <?= $color ?>" style="width: <?= $width ?>%">
    <?= $step ?>
</div>

<?php endforeach; ?>

</div>

</div>

<!-- UPDATES / HISTORY -->
<div class="card-box">

<h5>📄 Updates</h5>

<?php
$id = $concern['id'];
$res = $conn->query("SELECT * FROM responses WHERE concern_id=$id ORDER BY created_at DESC");

if($res && $res->num_rows > 0){
    while($r=$res->fetch_assoc()):
?>

<div class="border p-2 mb-2 rounded">
    <b><?= $r['department'] ?>:</b> <?= $r['message'] ?>
    <br>
    <small><?= $r['created_at'] ?></small>
</div>

<?php endwhile; } else { ?>

<p>No updates yet.</p>

<?php } ?>

</div>

<?php endif; ?>

</div>

</body>
</html>