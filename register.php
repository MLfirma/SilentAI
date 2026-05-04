<?php
include("config/db.php");

if($_POST){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // ✅ FIXED

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if($stmt->execute()){
        echo "<script>alert('Registered Successfully!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error registering user');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width:500px;">

    <div class="card shadow p-4">

        <h3 class="text-center">📝 Register</h3>

        <form method="POST">

            <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>

            <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

            <!-- ✅ ROLE SELECTION -->
            <select name="role" class="form-select mb-3" required>
                <option value="">Select Role</option>
                <option value="Student">Student</option>
                <option value="Academic">Academic</option>
                <option value="Financial">Financial</option>
                <option value="Welfare">Welfare</option>
            </select>

            <button class="btn btn-primary w-100">Register</button>

        </form>

        <p class="text-center mt-3">
            Already have account? <a href="login.php">Login</a>
        </p>

    </div>

</div>

</body>
</html>