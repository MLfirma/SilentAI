<?php
session_start();
include("../config/db.php");

/* =========================
   AUTH CHECK
========================= */
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

/* =========================
   COUNTS
========================= */
$total = $conn->query("SELECT COUNT(*) as t FROM concerns")->fetch_assoc()['t'];

$esc = $conn->query("SELECT COUNT(*) as e FROM concerns WHERE status='Escalated'")->fetch_assoc()['e'];

$pending = $conn->query("SELECT COUNT(*) as p FROM concerns WHERE status='Submitted'")->fetch_assoc()['p'];

$resolved = $conn->query("SELECT COUNT(*) as r FROM concerns WHERE status='Resolved'")->fetch_assoc()['r'];

$acad = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Academic'")->fetch_assoc()['c'];
$fin = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Financial'")->fetch_assoc()['c'];
$wel = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Welfare'")->fetch_assoc()['c'];

/* =========================
   🚨 ALERT SYSTEM
========================= */

$overdue = $conn->query("
    SELECT COUNT(*) as o 
    FROM concerns 
    WHERE status != 'Resolved' 
    AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 48
")->fetch_assoc()['o'];

$today = $conn->query("SELECT COUNT(*) as t FROM concerns WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['t'];
$yesterday = $conn->query("SELECT COUNT(*) as y FROM concerns WHERE DATE(created_at)=CURDATE()-INTERVAL 1 DAY")->fetch_assoc()['y'];
$spike = ($today > ($yesterday * 1.5));

/* =========================
   FILTER + SORT SYSTEM (ADDED)
========================= */

$filter_status = $_GET['status'] ?? '';
$filter_category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

$where = "WHERE 1=1";

/* FILTER */
if($filter_status != ''){
    $where .= " AND status='".$conn->real_escape_string($filter_status)."'";
}

if($filter_category != ''){
    $where .= " AND category='".$conn->real_escape_string($filter_category)."'";
}

if($search != ''){
    $s = $conn->real_escape_string($search);
    $where .= " AND (student_name LIKE '%$s%' OR email LIKE '%$s%')";
}

/* SORT */
$order = "ORDER BY created_at DESC";

if($sort == "oldest"){
    $order = "ORDER BY created_at ASC";
}
elseif($sort == "id_asc"){
    $order = "ORDER BY id ASC";
}
elseif($sort == "id_desc"){
    $order = "ORDER BY id DESC";
}
elseif($sort == "status_asc"){
    $order = "ORDER BY status ASC";
}
elseif($sort == "status_desc"){
    $order = "ORDER BY status DESC";
}
elseif($sort == "category_asc"){
    $order = "ORDER BY category ASC";
}
elseif($sort == "category_desc"){
    $order = "ORDER BY category DESC";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.card { border-radius:12px; }

#aiBtn{
    position:fixed;
    bottom:20px;
    right:20px;
    z-index:999;
    border-radius:50px;
    padding:12px 18px;
}

#chatBox{
    height:300px;
    overflow-y:auto;
    background:#f1f1f1;
    padding:10px;
    border-radius:10px;
}

.userMsg{ text-align:right; color:blue; margin:5px; }
.aiMsg{ text-align:left; color:green; margin:5px; }
</style>
</head>

<body class="bg-light">

<div class="container mt-4">

<!-- ALERTS -->
<?php if($overdue > 0): ?>
<div class="alert alert-danger">
🚨 <strong><?= $overdue ?></strong> overdue tickets (>48 hours)
</div>
<?php endif; ?>

<?php if($pending > 20): ?>
<div class="alert alert-warning">
⚠️ High number of pending tickets (<?= $pending ?>)
</div>
<?php endif; ?>

<?php if($spike): ?>
<div class="alert alert-info">
📈 Sudden spike detected today (<?= $today ?> vs <?= $yesterday ?> yesterday)
</div>
<?php endif; ?>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📊 Admin Dashboard</h3>
    <div>
        <a href="manage_accounts.php" class="btn btn-primary btn-sm me-2">👥 Manage Accounts</a>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</div>

<!-- CARDS -->
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card p-3 text-center shadow">
            <h6>Total</h6>
            <h3><?= $total ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center text-danger shadow">
            <h6>Escalated</h6>
            <h3><?= $esc ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center text-warning shadow">
            <h6>Pending</h6>
            <h3><?= $pending ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center text-success shadow">
            <h6>Resolved</h6>
            <h3><?= $resolved ?></h3>
        </div>
    </div>

</div>

<!-- CHART -->
<div class="card p-3 shadow mb-4">
<h5>📊 Department Analytics</h5>
<canvas id="deptChart"></canvas>
</div>

<script>
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: ['Academic','Financial','Welfare'],
        datasets: [{
            label: 'Concerns',
            data: [<?= $acad ?>, <?= $fin ?>, <?= $wel ?>],
            backgroundColor: ['blue','green','orange']
        }]
    }
});
</script>

<!-- FILTER + SORT (UPDATED) -->
<div class="card shadow p-3 mb-3">
<form method="GET" class="row g-2">

<div class="col-md-2">
<select name="status" class="form-control">
    <option value="">All Status</option>
    <option value="Submitted">Submitted</option>
    <option value="Escalated">Escalated</option>
    <option value="Resolved">Resolved</option>
</select>
</div>

<div class="col-md-2">
<select name="category" class="form-control">
    <option value="">All Category</option>
    <option value="Academic">Academic</option>
    <option value="Financial">Financial</option>
    <option value="Welfare">Welfare</option>
</select>
</div>

<div class="col-md-3">
<input type="text" name="search" class="form-control" placeholder="Search name or email">
</div>

<!-- SORT ADDED -->
<div class="col-md-3">
<select name="sort" class="form-control">
    <option value="newest">Newest First</option>
    <option value="oldest">Oldest First</option>
    <option value="id_desc">ID Desc</option>
    <option value="id_asc">ID Asc</option>
    <option value="status_asc">Status A-Z</option>
    <option value="status_desc">Status Z-A</option>
    <option value="category_asc">Category A-Z</option>
    <option value="category_desc">Category Z-A</option>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Apply</button>
</div>

</form>
</div>

<!-- CONCERN HISTORY -->
<div class="card shadow p-3 mb-4">
<h5>📋 Concern History (Live)</h5>

<table class="table table-striped table-bordered">
<thead class="table-dark">
<tr>
<th>ID</th><th>Student</th><th>Email</th><th>Category</th><th>Assigned To</th><th>Status</th><th>Date</th>
</tr>
</thead>

<tbody>
<?php
$res = $conn->query("SELECT * FROM concerns $where $order");

while($row = $res->fetch_assoc()):
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['student_name'] ?></td>
<td><?= $row['email'] ?></td>
<td><?= $row['category'] ?></td>

<td>
<span class="badge bg-info">
<?= !empty($row['assigned_to']) ? $row['assigned_to'] : 'Not Assigned' ?>
</span>
</td>

<td>
<?php if($row['status']=='Escalated'): ?>
<span class="badge bg-danger">Escalated</span>
<?php elseif($row['status']=='Submitted'): ?>
<span class="badge bg-primary">Submitted</span>
<?php elseif($row['status']=='Resolved'): ?>
<span class="badge bg-success">Resolved</span>
<?php endif; ?>
</td>

<td>
<?= $row['created_at'] ?>
<?php
$hours = (time() - strtotime($row['created_at'])) / 3600;
if($row['status'] != 'Resolved' && $hours > 48):
?>
<span class="badge bg-danger">Overdue</span>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- OFFICES (UNCHANGED) -->
<div class="card shadow p-3 mb-4">
<h5>🏢 Department Offices</h5>
<div class="row">
<div class="col-md-4"><h6 class="text-primary">Academic</h6><ul><li>Registrar</li><li>Dean's Office</li></ul></div>
<div class="col-md-4"><h6 class="text-success">Financial</h6><ul><li>Cashier</li><li>Accounting</li></ul></div>
<div class="col-md-4"><h6 class="text-info">Welfare</h6><ul><li>OSA</li><li>Clinic</li></ul></div>
</div>
</div>

<!-- RESPONSES (UNCHANGED) -->
<div class="card shadow p-3 mb-4">
<h5>💬 Department Responses</h5>
<table class="table table-bordered">
<thead class="table-dark">
<tr><th>ID</th><th>Department</th><th>Message</th><th>Date</th></tr>
</thead>
<tbody>
<?php
$res2 = $conn->query("SELECT * FROM responses ORDER BY created_at DESC");
while($r = $res2->fetch_assoc()):
?>
<tr>
<td><?= $r['concern_id'] ?></td>
<td><?= $r['department'] ?></td>
<td><?= $r['message'] ?></td>
<td><?= $r['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- AUDIT (UNCHANGED) -->
<div class="card shadow p-3 mb-5">
<h5>🧾 Audit Trail</h5>
<table class="table table-striped">
<thead class="table-dark">
<tr><th>ID</th><th>Action</th><th>Actor</th><th>Date</th></tr>
</thead>
<tbody>
<?php
$audit = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
while($a = $audit->fetch_assoc()):
?>
<tr>
<td><?= $a['concern_id'] ?></td>
<td><?= $a['action'] ?></td>
<td><?= $a['actor'] ?></td>
<td><?= $a['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>

<!-- AI BUTTON (UNCHANGED) -->
<button id="aiBtn" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#aiModal">
🤖 AI Help
</button>

<!-- AI MODAL (UNCHANGED) -->
<div class="modal fade" id="aiModal">
<div class="modal-dialog modal-dialog-end">
<div class="modal-content">

<div class="modal-header">
<h5>AI Assistant</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div id="chatBox"></div>
<input type="text" id="msg" class="form-control mt-2" placeholder="Ask something...">
<button class="btn btn-primary w-100 mt-2" onclick="sendMsg()">Send</button>
</div>

</div>
</div>
</div>

<script>
function addMsg(text, cls){
let div=document.createElement("div");
div.className=cls;
div.innerHTML=text;
document.getElementById("chatBox").appendChild(div);
}

function sendMsg(){
let msg=document.getElementById("msg").value;
if(msg=="") return;

addMsg("You: "+msg,"userMsg");

fetch("ai_chat.php",{
method:"POST",
headers:{"Content-Type":"application/x-www-form-urlencoded"},
body:"msg="+encodeURIComponent(msg)
})
.then(res=>res.json())
.then(data=>addMsg("AI: "+data.reply,"aiMsg"))
.catch(()=>addMsg("AI error","aiMsg"));

document.getElementById("msg").value="";
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>