<?php
session_start();
include("config/db.php");

if($_POST){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $res->fetch_assoc();

    if($user && $password == $user['password']){

        $_SESSION['user'] = $user;

        // ROLE ROUTING (DEPARTMENT LEVEL)
        if($user['role'] == 'Academic'){
            header("Location: admin/academic_dashboard.php");
        }
        elseif($user['role'] == 'Financial'){
            header("Location: admin/financial_dashboard.php");
        }
        elseif($user['role'] == 'Welfare'){
            header("Location: admin/welfare_dashboard.php");
        }
        else {
            header("Location: admin/dashboard.php");
        }

    } else {
        echo "Invalid login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Department Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow p-4">

                <h4 class="text-center mb-3">🏢 Department Login</h4>

                <form method="POST">

                    <input class="form-control mb-3" name="email" placeholder="Email">

                    <input class="form-control mb-3" type="password" name="password" placeholder="Password">

                    <button class="btn btn-primary w-100">Login</button>

                </form>

                <hr>

                <small class="text-muted">
                    Academic • Financial • Welfare Access System
                </small>

            </div>

        </div>
    </div>

</div>

</body>
</html>