<?php
session_start();
include("../config/db.php");

/* AUTH */
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

/* CREATE USER */
if(isset($_POST['create'])){
    $conn->query("
        INSERT INTO users (name,email,password,role)
        VALUES (
            '{$_POST['name']}',
            '{$_POST['email']}',
            '{$_POST['password']}',
            '{$_POST['role']}'
        )
    ");
}

/* DELETE */
if(isset($_GET['delete'])){
    $conn->query("DELETE FROM users WHERE id={$_GET['delete']}");
}

/* UPDATE */
if(isset($_POST['update'])){
    $conn->query("
        UPDATE users SET
        name='{$_POST['name']}',
        email='{$_POST['email']}',
        role='{$_POST['role']}'
        WHERE id={$_POST['id']}
    ");
}

/* CHANGE PASSWORD */
if(isset($_POST['changepass'])){
    $conn->query("
        UPDATE users SET
        password='{$_POST['password']}'
        WHERE id={$_POST['id']}
    ");
}

/* FILTER + SEARCH */
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? 'all';

$sql = "SELECT * FROM users WHERE 1";

if(!empty($search)){
    $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}

if($roleFilter != 'all'){
    $sql .= " AND role='$roleFilter'";
}

$sql .= " ORDER BY id DESC";

$users = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }

        .card-box{
            background:#fff;
            border-radius:12px;
            padding:20px;
            box-shadow:0 3px 10px rgba(0,0,0,0.1);
            margin-bottom:20px;
        }
    </style>
</head>

<body>

<div class="container mt-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>👥 Account Management</h3>

    <!-- BACK TO DASHBOARD -->
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        ← Back to Dashboard
    </a>
</div>

<!-- CREATE USER -->
<div class="card-box">
    <h5>➕ Create User</h5>

    <form method="POST" class="row g-2">

        <div class="col-md-3">
            <input name="name" class="form-control" placeholder="Name" required>
        </div>

        <div class="col-md-3">
            <input name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="col-md-2">
            <input name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="col-md-2">
            <select name="role" class="form-select">
                <option>admin</option>
                <option>Academic</option>
                <option>Financial</option>
                <option>Welfare</option>
                <option>Student</option>
            </select>
        </div>

        <div class="col-md-2">
            <button name="create" class="btn btn-success w-100">Create</button>
        </div>

    </form>
</div>

<!-- FILTER -->
<div class="card-box">
    <form method="GET" class="row g-2">

        <div class="col-md-5">
            <input type="text" name="search" class="form-control"
                   placeholder="Search name or email..."
                   value="<?= $search ?>">
        </div>

        <div class="col-md-4">
            <select name="role" class="form-select">
                <option value="all">All Roles</option>
                <option value="admin">admin</option>
                <option value="Academic">Academic</option>
                <option value="Financial">Financial</option>
                <option value="Welfare">Welfare</option>
                <option value="Student">Student</option>
            </select>
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>
</div>

<!-- USER TABLE -->
<div class="card-box">

<h5>📋 User List</h5>

<table class="table table-bordered table-hover align-middle">

    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th width="280">Actions</th>
        </tr>
    </thead>

    <tbody>

    <?php while($u = $users->fetch_assoc()): ?>

    <tr>
        <td><?= $u['id'] ?></td>

        <td><strong><?= $u['name'] ?></strong></td>

        <td><?= $u['email'] ?></td>

        <td>
            <span class="badge bg-primary"><?= $u['role'] ?></span>
        </td>

        <!-- ACTIONS -->
        <td>

            <!-- DELETE -->
            <a href="?delete=<?= $u['id'] ?>"
               class="btn btn-danger btn-sm w-100 mb-1">
               🗑 Delete
            </a>

            <!-- UPDATE -->
            <form method="POST" class="mb-1">

                <input type="hidden" name="id" value="<?= $u['id'] ?>">

                <input name="name"
                       value="<?= $u['name'] ?>"
                       class="form-control form-control-sm mb-1">

                <input name="email"
                       value="<?= $u['email'] ?>"
                       class="form-control form-control-sm mb-1">

                <select name="role" class="form-select form-select-sm mb-1">
                    <option <?= $u['role']=='admin'?'selected':'' ?>>admin</option>
                    <option <?= $u['role']=='Academic'?'selected':'' ?>>Academic</option>
                    <option <?= $u['role']=='Financial'?'selected':'' ?>>Financial</option>
                    <option <?= $u['role']=='Welfare'?'selected':'' ?>>Welfare</option>
                    <option <?= $u['role']=='Student'?'selected':'' ?>>Student</option>
                </select>

                <button name="update" class="btn btn-primary btn-sm w-100">
                    ✏ Update
                </button>

            </form>

            <!-- PASSWORD -->
            <form method="POST">

                <input type="hidden" name="id" value="<?= $u['id'] ?>">

                <input name="password"
                       class="form-control form-control-sm mb-1"
                       placeholder="New Password">

                <button name="changepass"
                        class="btn btn-warning btn-sm w-100">
                    🔑 Password
                </button>

            </form>

        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>

</table>

</div>

</div>

</body>
</html>